<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\GroupService;
use InvalidArgumentException;

/**
 * Thin wrapper around GroupService with group_type locked to ctf.
 */
final class TeamController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'type' => 'ctf',
            'skill' => trim((string) $request->input('skill', '')),
            'search' => trim((string) $request->input('search', '')),
        ];
        $page = max(1, (int) $request->input('page', 1));

        $result = GroupService::list($filters, $page, 12, Auth::id());
        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $this->view('teams/index', [
            'title' => 'CTF Teams — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'skills' => $skills,
            'basePath' => '/teams',
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();
        $this->view('teams/create', [
            'title' => 'Create CTF Team — CySkillShare',
            'group' => null,
            'skillIds' => [],
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
            'forcedType' => 'ctf',
            'basePath' => '/teams',
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        try {
            $data = [
                'name' => (string) $request->input('name', ''),
                'description' => (string) $request->input('description', ''),
                'slug' => (string) $request->input('slug', ''),
                'visibility' => (string) $request->input('visibility', 'community'),
                'join_policy' => (string) $request->input('join_policy', 'approval'),
                'max_members' => (int) $request->input('max_members', 20),
                'group_type' => 'ctf',
            ];
            $skillIds = [];
            foreach ((array) $request->input('skill_ids', []) as $sid) {
                $sid = (int) $sid;
                if ($sid > 0) {
                    $skillIds[] = $sid;
                }
            }
            $group = GroupService::create($userId, $data, $skillIds);
            $this->withSuccess('CTF team created.');
            $this->redirect('/teams/' . $group['slug']);
        } catch (\InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/teams/create');
        } catch (\RuntimeException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/teams/create');
        }
    }

    public function show(Request $request): void
    {
        $slug = (string) $request->param('slug');
        $viewerId = Auth::id();

        try {
            $detail = GroupService::detail($slug, $viewerId);
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Team Not Found',
                    'message' => 'This CTF team does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/teams');
            return;
        }

        if (($detail['group']['group_type'] ?? '') !== 'ctf') {
            $this->redirect('/groups/' . $slug);
            return;
        }

        $owner = Database::fetch(
            'SELECT username FROM users WHERE id = ? LIMIT 1',
            [(int) $detail['group']['owner_id']]
        );
        $detail['group']['owner_username'] = (string) ($owner['username'] ?? '');

        $this->view('teams/show', [
            'title' => $detail['group']['name'] . ' — CTF Team',
            'basePath' => '/teams',
            ...$detail,
            'pendingRequests' => [],
            'pendingInvitation' => null,
        ]);
    }
}
