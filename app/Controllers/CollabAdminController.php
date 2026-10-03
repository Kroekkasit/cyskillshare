<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\GroupService;
use App\Services\MentorService;
use InvalidArgumentException;
use RuntimeException;

final class CollabAdminController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!GroupService::canManagePlatform() && !MentorService::canVerify()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access collaboration admin.',
            ]);
            return;
        }

        $pendingMentors = [];
        if (MentorService::canVerify()) {
            $pendingMentors = Database::fetchAll(
                "SELECT m.*, u.username, u.full_name
                 FROM mentors m
                 INNER JOIN users u ON u.id = m.user_id
                 WHERE m.verification_status = 'unverified'
                 ORDER BY m.created_at ASC
                 LIMIT 50"
            );
        }

        $groups = [];
        if (GroupService::canManagePlatform()) {
            $groups = Database::fetchAll(
                "SELECT g.*, u.username AS owner_username,
                        (SELECT COUNT(*) FROM collab_group_members m
                         WHERE m.group_id = g.id AND m.status = 'active') AS member_count
                 FROM collab_groups g
                 INNER JOIN users u ON u.id = g.owner_id
                 ORDER BY g.updated_at DESC
                 LIMIT 50"
            );
        }

        $this->view('admin/collaboration', [
            'title' => 'Collaboration Admin — CySkillShare',
            'pendingMentors' => $pendingMentors,
            'groups' => $groups,
            'canVerify' => MentorService::canVerify(),
            'canManagePlatform' => GroupService::canManagePlatform(),
        ]);
    }

    public function verifyMentor(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $note = trim((string) $request->input('note', ''));

        try {
            MentorService::verify($id, (int) Auth::id(), $note);
            $this->withSuccess('Mentor verified.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/collaboration');
    }

    public function suspendGroup(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!GroupService::canManagePlatform()) {
            http_response_code(403);
            $this->withError('Forbidden.');
            $this->redirect('/admin/collaboration');
            return;
        }

        $id = (int) $request->param('id');
        $group = GroupService::find($id);

        if ($group === null) {
            $this->withError('Group not found.');
            $this->redirect('/admin/collaboration');
            return;
        }

        Database::execute(
            "UPDATE collab_groups SET status = 'suspended' WHERE id = ?",
            [$id]
        );

        $this->withSuccess('Group suspended.');
        $this->redirect('/admin/collaboration');
    }
}
