<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\User;
use App\Services\GroupMemberService;
use App\Services\GroupService;
use InvalidArgumentException;
use RuntimeException;

final class GroupController extends Controller
{
    /** @var list<string> */
    private const GROUP_TYPES = ['study', 'ctf', 'project', 'general'];

    public function index(Request $request): void
    {
        $filters = [
            'type' => trim((string) $request->input('type', '')),
            'skill' => trim((string) $request->input('skill', '')),
            'search' => trim((string) $request->input('search', '')),
        ];
        if ($filters['type'] !== '' && !in_array($filters['type'], self::GROUP_TYPES, true)) {
            $filters['type'] = '';
        }
        $page = max(1, (int) $request->input('page', 1));

        $result = GroupService::list($filters, $page, 12, Auth::id());
        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $this->view('groups/index', [
            'title' => 'Study Groups — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'groupTypes' => self::GROUP_TYPES,
            'skills' => $skills,
            'basePath' => '/groups',
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();
        $this->view('groups/create', [
            'title' => 'Create Group — CySkillShare',
            'group' => null,
            'skillIds' => [],
            'allSkills' => self::allSkills(),
            'groupTypes' => self::GROUP_TYPES,
            'forcedType' => null,
            'basePath' => '/groups',
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        try {
            $data = self::extractGroupData($request);
            $skills = self::parseSkillIds((array) $request->input('skill_ids', []));
            $group = GroupService::create($userId, $data, $skills);
            $this->withSuccess('Group created.');
            $this->redirect('/groups/' . $group['slug']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/groups/create');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/groups/create');
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
                    'title' => 'Group Not Found',
                    'message' => 'This group does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/groups');
            return;
        }

        $owner = Database::fetch(
            'SELECT username FROM users WHERE id = ? LIMIT 1',
            [(int) $detail['group']['owner_id']]
        );
        $detail['group']['owner_username'] = (string) ($owner['username'] ?? '');

        $pendingRequests = [];
        $pendingInvitation = null;
        $groupId = (int) $detail['group']['id'];

        if (!empty($detail['can_manage'])) {
            $pendingRequests = Database::fetchAll(
                "SELECT jr.id, jr.message, jr.created_at, u.username, u.full_name
                 FROM collab_group_join_requests jr
                 INNER JOIN users u ON u.id = jr.user_id
                 WHERE jr.group_id = ? AND jr.status = 'pending'
                 ORDER BY jr.created_at ASC",
                [$groupId]
            );
        }

        if ($viewerId !== null && empty($detail['viewer_role'])) {
            $pendingInvitation = Database::fetch(
                "SELECT id FROM collab_group_invitations
                 WHERE group_id = ? AND invitee_id = ? AND status = 'pending' AND expires_at > NOW()
                 LIMIT 1",
                [$groupId, $viewerId]
            );
        }

        $this->view('groups/show', [
            'title' => $detail['group']['name'] . ' — Group',
            'basePath' => '/groups',
            ...$detail,
            'pendingRequests' => $pendingRequests,
            'pendingInvitation' => $pendingInvitation,
        ]);
    }

    public function join(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $group = GroupService::find($id);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/groups');
            return;
        }

        try {
            $message = trim((string) $request->input('message', ''));
            $result = GroupMemberService::join($group, (int) Auth::id(), $message);
            $msg = match ($result) {
                'joined' => 'You joined the group.',
                'requested' => 'Join request submitted.',
                'pending' => 'Your join request is already pending.',
                'already_member' => 'You are already a member.',
                default => 'Done.',
            };
            $this->withSuccess($msg);
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/groups/' . $group['slug']);
    }

    public function leave(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $group = GroupService::find($id);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/groups');
            return;
        }

        try {
            GroupMemberService::leave($group, (int) Auth::id());
            $this->withSuccess('You left the group.');
            $this->redirect('/groups');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/groups/' . $group['slug']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/groups/' . $group['slug']);
        }
    }

    public function invite(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $group = GroupService::find($id);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/groups');
            return;
        }

        $username = trim((string) $request->input('username', ''));
        $invitee = User::findByUsername($username);
        if ($invitee === null) {
            $this->withError('User not found.');
            $this->redirect('/groups/' . $group['slug']);
            return;
        }

        try {
            GroupMemberService::invite($group, (int) Auth::id(), $invitee->id);
            $this->withSuccess('Invitation sent to @' . $invitee->username . '.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/groups/' . $group['slug']);
    }

    public function reviewJoin(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $groupId = (int) $request->param('id');
        $requestId = (int) $request->param('requestId');
        $decision = (string) $request->input('decision', '');
        $group = GroupService::find($groupId);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/groups');
            return;
        }

        try {
            GroupMemberService::reviewJoinRequest($requestId, (int) Auth::id(), $decision);
            $this->withSuccess($decision === 'approved' ? 'Join request approved.' : 'Join request declined.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/groups/' . $group['slug']);
    }

    public function respondInvite(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $invitationId = (int) $request->param('id');
        $decision = (string) $request->input('decision', '');

        $inv = Database::fetch(
            'SELECT group_id FROM collab_group_invitations WHERE id = ? LIMIT 1',
            [$invitationId]
        );
        $group = $inv ? GroupService::find((int) $inv['group_id']) : null;

        try {
            GroupMemberService::respondInvitation($invitationId, (int) Auth::id(), $decision);
            $this->withSuccess($decision === 'accepted' ? 'Invitation accepted.' : 'Invitation declined.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        if ($group !== null) {
            $this->redirect('/groups/' . $group['slug']);
        } else {
            $this->redirect('/groups');
        }
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $group = GroupService::find($id);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/groups');
            return;
        }

        try {
            $data = self::extractGroupData($request, false);
            $skills = self::parseSkillIds((array) $request->input('skill_ids', []));
            $updated = GroupService::update($group, (int) Auth::id(), $data, $skills);
            $this->withSuccess('Group updated.');
            $this->redirect('/groups/' . $updated['slug']);
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
            $this->redirect('/groups/' . $group['slug']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/groups/' . $group['slug']);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected static function extractGroupData(Request $request, bool $includeType = true): array
    {
        $data = [
            'name' => (string) $request->input('name', ''),
            'description' => (string) $request->input('description', ''),
            'slug' => (string) $request->input('slug', ''),
            'visibility' => (string) $request->input('visibility', 'community'),
            'join_policy' => (string) $request->input('join_policy', 'approval'),
            'max_members' => (int) $request->input('max_members', 20),
        ];
        if ($includeType) {
            $data['group_type'] = (string) $request->input('group_type', 'study');
        }
        return $data;
    }

    /**
     * @param list<mixed> $ids
     * @return list<int>
     */
    protected static function parseSkillIds(array $ids): array
    {
        $out = [];
        foreach ($ids as $sid) {
            $sid = (int) $sid;
            if ($sid > 0) {
                $out[] = $sid;
            }
        }
        return $out;
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    protected static function allSkills(): array
    {
        return Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
        );
    }
}
