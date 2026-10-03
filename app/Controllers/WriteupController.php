<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\ContentFormatter;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\User;
use App\Services\ContentReactionService;
use App\Services\WriteupService;
use InvalidArgumentException;
use RuntimeException;

final class WriteupController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'category' => trim((string) $request->input('category', '')),
            'skill' => trim((string) $request->input('skill', '')),
            'tag' => trim((string) $request->input('tag', '')),
            'difficulty' => trim((string) $request->input('difficulty', '')),
            'sort' => (string) $request->input('sort', 'recent'),
        ];
        $page = max(1, (int) $request->input('page', 1));

        $result = WriteupService::list($filters, $page, 12);
        $categories = WriteupService::categories();
        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $this->view('writeups/index', [
            'title' => 'Writeups — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'categories' => $categories,
            'skills' => $skills,
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();
        $this->renderForm(null, $request);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        if ((string) $request->input('action') === 'preview') {
            $this->renderForm(null, $request, $this->buildPreview($request));
            return;
        }

        try {
            $data = self::extractWriteupData($request);
            $skills = self::parseIntIds((array) $request->input('skill_ids', []));
            $tags = self::parseTags((string) $request->input('tags', ''));
            $challenges = self::parseIntIds((array) $request->input('challenge_ids', []));

            $writeup = WriteupService::create($userId, $data, $skills, $tags, $challenges);
            $this->withSuccess('Writeup created.');
            $this->redirect('/writeups/edit/' . (int) $writeup['id']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->renderForm(null, $request);
        }
    }

    public function show(Request $request): void
    {
        $username = (string) $request->param('username');
        $slug = (string) $request->param('slug');

        try {
            $detail = WriteupService::detail($username, $slug, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Writeup Not Found',
                    'message' => 'This writeup does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/writeups');
            return;
        }

        $this->view('writeups/show', [
            'title' => $detail['writeup']['title'] . ' — Writeup',
            ...$detail,
        ]);
    }

    public function redirectById(Request $request): void
    {
        $id = (int) $request->param('id');
        $writeup = WriteupService::find($id);

        if ($writeup === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Writeup Not Found',
                'message' => 'This writeup does not exist.',
            ]);
            return;
        }

        $user = User::find((int) $writeup['user_id']);
        if ($user === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Writeup Not Found',
                'message' => 'Author not found.',
            ]);
            return;
        }

        $this->redirect('/writeups/' . $user->username . '/' . $writeup['slug']);
    }

    public function editForm(Request $request): void
    {
        Auth::requireLogin();
        $writeup = $this->requireOwnedWriteup($request);
        if ($writeup === null) {
            return;
        }
        $this->renderForm($writeup, $request);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $writeup = $this->requireOwnedWriteup($request);
        if ($writeup === null) {
            return;
        }

        if ((string) $request->input('action') === 'preview') {
            $this->renderForm($writeup, $request, $this->buildPreview($request));
            return;
        }

        try {
            $data = self::extractWriteupData($request);
            $skills = self::parseIntIds((array) $request->input('skill_ids', []));
            $tags = self::parseTags((string) $request->input('tags', ''));
            $challenges = self::parseIntIds((array) $request->input('challenge_ids', []));

            WriteupService::update($writeup, (int) Auth::id(), $data, $skills, $tags, $challenges);
            $this->withSuccess('Writeup updated.');
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->withError('Forbidden.');
            } else {
                $this->withError($e->getMessage());
            }
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/writeups/edit/' . (int) $writeup['id']);
    }

    public function autosave(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $writeup = $this->requireOwnedWriteup($request);
        if ($writeup === null) {
            return;
        }

        $title = (string) $request->input('title', (string) $writeup['title']);
        $content = (string) $request->input('content', (string) $writeup['content']);
        $wantsJson = str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json')
            || (string) $request->input('format') === 'json';

        try {
            WriteupService::autosave($writeup, (int) Auth::id(), $title, $content);
            if ($wantsJson) {
                http_response_code(204);
                return;
            }
            $this->withSuccess('Draft saved.');
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                if ($wantsJson) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Forbidden']);
                    return;
                }
                $this->withError('Forbidden.');
            } else {
                if ($wantsJson) {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                    return;
                }
                $this->withError($e->getMessage());
            }
        } catch (InvalidArgumentException $e) {
            if ($wantsJson) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                return;
            }
            $this->withError($e->getMessage());
        }

        $this->redirect('/writeups/edit/' . (int) $writeup['id']);
    }

    public function publish(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $this->changeStatus($request, 'published', 'Writeup published.');
    }

    public function archive(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $this->changeStatus($request, 'archived', 'Writeup archived.');
    }

    public function react(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $type = (string) $request->input('reaction_type', 'helpful');

        try {
            ContentReactionService::react((int) Auth::id(), 'writeup', $id, $type);
            $this->withSuccess('Reaction recorded.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/writeups'));
        $this->redirect($referer !== '' ? $referer : '/writeups');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function requireOwnedWriteup(Request $request): ?array
    {
        $id = (int) $request->param('id');
        $writeup = WriteupService::find($id);

        if ($writeup === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Writeup Not Found',
                'message' => 'This writeup does not exist.',
            ]);
            return null;
        }

        if ((int) $writeup['user_id'] !== (int) Auth::id()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not own this writeup.',
            ]);
            return null;
        }

        return $writeup;
    }

    private function changeStatus(Request $request, string $status, string $successMsg): void
    {
        $writeup = $this->requireOwnedWriteup($request);
        if ($writeup === null) {
            return;
        }

        try {
            WriteupService::update($writeup, (int) Auth::id(), ['status' => $status]);
            $this->withSuccess($successMsg);
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/writeups/edit/' . (int) $writeup['id']));
        $this->redirect($referer !== '' ? $referer : '/writeups/edit/' . (int) $writeup['id']);
    }

    /**
     * @param array<string, mixed>|null $writeup
     * @param array{html:string,toc:list<array{level:int,id:string,text:string}>}|null $preview
     */
    private function renderForm(?array $writeup, Request $request, ?array $preview = null): void
    {
        $isEdit = $writeup !== null;
        $id = $isEdit ? (int) $writeup['id'] : 0;

        $skillIds = [];
        $tagNames = [];
        $challengeIds = [];

        if ($isEdit) {
            $skillRows = Database::fetchAll(
                'SELECT skill_id FROM writeup_skills WHERE writeup_id = ?',
                [$id]
            );
            $skillIds = array_map(static fn(array $r): int => (int) $r['skill_id'], $skillRows);
            $tagRows = Database::fetchAll(
                'SELECT t.name FROM writeup_tag_map m
                 INNER JOIN writeup_tags t ON t.id = m.tag_id WHERE m.writeup_id = ?',
                [$id]
            );
            $tagNames = array_map(static fn(array $r): string => (string) $r['name'], $tagRows);
            $challengeRows = Database::fetchAll(
                'SELECT challenge_id FROM writeup_challenges WHERE writeup_id = ?',
                [$id]
            );
            $challengeIds = array_map(static fn(array $r): int => (int) $r['challenge_id'], $challengeRows);
        }

        $formData = $isEdit ? $writeup : [];
        if ($request->method() === 'POST') {
            $formData = array_merge($formData, [
                'title' => (string) $request->input('title', ''),
                'slug' => (string) $request->input('slug', ''),
                'short_description' => (string) $request->input('short_description', ''),
                'content' => (string) $request->input('content', ''),
                'category_id' => $request->input('category_id'),
                'difficulty' => (string) $request->input('difficulty', 'beginner'),
                'status' => (string) $request->input('status', 'draft'),
                'visibility' => (string) $request->input('visibility', 'public'),
            ]);
            $skillIds = self::parseIntIds((array) $request->input('skill_ids', []));
            $tagNames = self::parseTags((string) $request->input('tags', ''));
            $challengeIds = self::parseIntIds((array) $request->input('challenge_ids', []));
        }

        $this->view($isEdit ? 'writeups/edit' : 'writeups/create', [
            'title' => ($isEdit ? 'Edit Writeup' : 'New Writeup') . ' — CySkillShare',
            'writeup' => $formData,
            'skillIds' => $skillIds,
            'tagNames' => $tagNames,
            'challengeIds' => $challengeIds,
            'categories' => WriteupService::categories(),
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
            'templates' => config('writeups.templates', []),
            'preview' => $preview,
            'suggestions' => $isEdit && $writeup !== null
                ? WriteupService::qualitySuggestions(
                    $formData,
                    Database::fetchAll(
                        'SELECT s.id, s.name, s.slug FROM writeup_skills ws
                         INNER JOIN skills s ON s.id = ws.skill_id WHERE ws.writeup_id = ?',
                        [$id]
                    ),
                    Database::fetchAll(
                        'SELECT c.id, c.title FROM writeup_challenges wc
                         INNER JOIN challenges c ON c.id = wc.challenge_id WHERE wc.writeup_id = ?',
                        [$id]
                    ),
                    Database::fetchAll(
                        'SELECT p.id, p.title FROM project_writeups pw
                         INNER JOIN projects p ON p.id = pw.project_id WHERE pw.writeup_id = ?',
                        [$id]
                    )
                )
                : null,
        ]);
    }

    /**
     * @return array{html:string,toc:list<array{level:int,id:string,text:string}>}
     */
    private function buildPreview(Request $request): array
    {
        $content = (string) $request->input('content', '');
        return ContentFormatter::renderWithToc($content);
    }

    /**
     * @return array<string, mixed>
     */
    private static function extractWriteupData(Request $request): array
    {
        return [
            'title' => (string) $request->input('title', ''),
            'slug' => (string) $request->input('slug', ''),
            'short_description' => (string) $request->input('short_description', ''),
            'content' => (string) $request->input('content', ''),
            'category_id' => $request->input('category_id'),
            'difficulty' => (string) $request->input('difficulty', 'beginner'),
            'status' => (string) $request->input('status', 'draft'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'change_summary' => (string) $request->input('change_summary', ''),
        ];
    }

    /**
     * @param list<mixed> $ids
     * @return list<int>
     */
    private static function parseIntIds(array $ids): array
    {
        $out = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $out[] = $id;
            }
        }
        return $out;
    }

    /**
     * @return list<string>
     */
    private static function parseTags(string $input): array
    {
        if ($input === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }
}
