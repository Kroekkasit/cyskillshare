<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\ContentFormatter;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\LabInstanceService;
use App\Services\LabRecommendationService;
use App\Services\LabService;
use InvalidArgumentException;
use RuntimeException;

final class LabController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'category' => trim((string) $request->input('category', '')),
            'difficulty' => trim((string) $request->input('difficulty', '')),
            'skill' => trim((string) $request->input('skill', '')),
            'search' => trim((string) $request->input('search', '')),
            'sort' => (string) $request->input('sort', 'recent'),
        ];
        $page = max(1, (int) $request->input('page', 1));
        $viewerId = Auth::id();

        $result = LabService::list($filters, $page, 12, $viewerId);
        $recommendations = LabRecommendationService::forUser($viewerId, 4);

        $this->view('labs/index', [
            'title' => 'Cyber Labs — CySkillShare',
            'labs' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => 12,
            'filters' => $filters,
            'categories' => LabService::categories(),
            'recommendations' => $recommendations,
        ]);
    }

    public function show(Request $request): void
    {
        $slug = (string) $request->param('slug');
        $viewerId = Auth::id();

        try {
            $detail = LabService::detail($slug, $viewerId);
        } catch (InvalidArgumentException $e) {
            $code = (int) $e->getCode();
            if ($code === 404 || $code === 403) {
                http_response_code($code);
                $this->view('pages/errors/' . ($code === 404 ? '404' : '403'), [
                    'title' => $code === 404 ? 'Lab Not Found' : 'Forbidden',
                    'message' => $e->getMessage(),
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/labs');
            return;
        }

        $lab = $detail['lab'];
        $formattedDescription = ContentFormatter::renderWithToc((string) ($lab['description'] ?? ''));
        $formattedObjectives = !empty($lab['learning_objectives'])
            ? ContentFormatter::renderWithToc((string) $lab['learning_objectives'])
            : null;

        $this->view('labs/show', [
            'title' => (string) $lab['title'] . ' — Cyber Labs',
            'detail' => $detail,
            'formattedDescription' => $formattedDescription,
            'formattedObjectives' => $formattedObjectives,
        ]);
    }

    public function redirectById(Request $request): void
    {
        $id = (int) $request->param('id');
        $lab = LabService::find($id);
        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }
        $this->redirect('/labs/' . rawurlencode((string) $lab['slug']));
    }

    public function history(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();

        $completions = Database::fetchAll(
            "SELECT lc.*, l.title, l.slug, l.difficulty, c.name AS category_name
             FROM lab_completions lc
             INNER JOIN labs l ON l.id = lc.lab_id
             LEFT JOIN lab_categories c ON c.id = l.category_id
             WHERE lc.user_id = ?
             ORDER BY lc.completed_at DESC
             LIMIT 50",
            [$userId]
        );

        $recentInstances = Database::fetchAll(
            "SELECT i.id, i.lab_id, i.status, i.provision_state, i.started_at, i.expires_at,
                    i.completed_at, i.score, l.title, l.slug
             FROM lab_instances i
             INNER JOIN labs l ON l.id = i.lab_id
             WHERE i.user_id = ?
             ORDER BY i.started_at DESC
             LIMIT 20",
            [$userId]
        );

        $this->view('labs/history', [
            'title' => 'Lab History — Cyber Labs',
            'completions' => $completions,
            'recentInstances' => $recentInstances,
        ]);
    }

    public function start(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $slug = (string) $request->param('slug');
        $userId = (int) Auth::id();
        $lab = LabService::findBySlug($slug);

        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }

        try {
            $instance = LabInstanceService::start($userId, $lab);
            $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . (int) $instance['id']);
        } catch (RuntimeException $e) {
            $code = (int) $e->getCode();
            if ($code === 429 || $code === 503 || $code === 403) {
                http_response_code($code >= 400 ? $code : 400);
            }
            $this->withError($e->getMessage());
            $this->redirect('/labs/' . rawurlencode($slug));
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/labs/' . rawurlencode($slug));
        }
    }

    public function feedback(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $slug = (string) $request->param('slug');
        $userId = (int) Auth::id();
        $lab = LabService::findBySlug($slug);

        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }

        $difficulty = (string) $request->input('difficulty_rating', '');
        $clarity = (string) $request->input('clarity_rating', '');
        $allowedDiff = ['too_easy', 'appropriate', 'too_hard'];
        $allowedClarity = ['poor', 'okay', 'good'];

        if (!in_array($difficulty, $allowedDiff, true) || !in_array($clarity, $allowedClarity, true)) {
            $this->withError('Please provide valid ratings.');
            $this->redirect('/labs/' . rawurlencode($slug));
            return;
        }

        $text = trim((string) $request->input('feedback', ''));
        if (mb_strlen($text) > 5000) {
            $text = mb_substr($text, 0, 5000);
        }

        Database::execute(
            'INSERT INTO lab_feedback (lab_id, user_id, difficulty_rating, clarity_rating, feedback)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               difficulty_rating = VALUES(difficulty_rating),
               clarity_rating = VALUES(clarity_rating),
               feedback = VALUES(feedback)',
            [(int) $lab['id'], $userId, $difficulty, $clarity, $text !== '' ? $text : null]
        );

        $this->withSuccess('Thank you for your feedback.');
        $this->redirect('/labs/' . rawurlencode($slug));
    }
}
