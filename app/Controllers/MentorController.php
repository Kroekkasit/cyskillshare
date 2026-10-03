<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\MentorService;
use InvalidArgumentException;
use RuntimeException;

final class MentorController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'skill' => trim((string) $request->input('skill', '')),
            'verified' => $request->input('verified') !== null && $request->input('verified') !== '',
        ];
        $page = max(1, (int) $request->input('page', 1));

        $result = MentorService::list($filters, $page, 12, Auth::id());
        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $this->view('mentors/index', [
            'title' => 'Mentors — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'skills' => $skills,
        ]);
    }

    public function show(Request $request): void
    {
        $username = (string) $request->param('username');

        try {
            $detail = MentorService::detailByUsername($username, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Mentor Not Found',
                    'message' => 'This mentor profile does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/mentors');
            return;
        }

        $this->view('mentors/show', [
            'title' => '@' . $username . ' — Mentor',
            ...$detail,
        ]);
    }

    public function settingsForm(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $mentor = MentorService::findByUserId($userId);
        $skillIds = [];

        if ($mentor !== null) {
            $rows = Database::fetchAll(
                'SELECT skill_id FROM mentor_skills WHERE mentor_id = ?',
                [(int) $mentor['id']]
            );
            $skillIds = array_map(static fn(array $r): int => (int) $r['skill_id'], $rows);
        }

        $this->view('mentors/settings', [
            'title' => 'Mentor Settings — CySkillShare',
            'mentor' => $mentor,
            'skillIds' => $skillIds,
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
        ]);
    }

    public function setup(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $data = [
            'bio' => (string) $request->input('bio', ''),
            'accepting_requests' => $request->input('accepting_requests'),
            'max_mentees' => (int) $request->input('max_mentees', 3),
            'preferred_frequency' => (string) $request->input('preferred_frequency', ''),
            'preferred_session_length' => (string) $request->input('preferred_session_length', ''),
            'languages' => (string) $request->input('languages', ''),
            'communication_style' => (string) $request->input('communication_style', ''),
        ];

        $skillIds = [];
        foreach ((array) $request->input('skill_ids', []) as $sid) {
            $sid = (int) $sid;
            if ($sid > 0) {
                $skillIds[] = $sid;
            }
        }

        try {
            MentorService::enable($userId, $data, $skillIds);
            $this->withSuccess('Mentor profile saved.');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/mentors/settings');
    }
}
