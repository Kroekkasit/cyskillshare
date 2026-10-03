<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\RecruitmentService;
use InvalidArgumentException;
use RuntimeException;

final class RecruitmentController extends Controller
{
    /** @var list<string> */
    private const LOOKING_FOR = ['study', 'ctf', 'project', 'mentor', 'general'];

    public function index(Request $request): void
    {
        $items = RecruitmentService::listOpen(30);

        $this->view('recruitment/index', [
            'title' => 'Recruitment — CySkillShare',
            'items' => $items,
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $myGroups = Database::fetchAll(
            "SELECT g.id, g.name, g.slug FROM collab_groups g
             INNER JOIN collab_group_members m ON m.group_id = g.id AND m.user_id = ? AND m.status = 'active'
             WHERE g.status = 'active'
             ORDER BY g.name",
            [$userId]
        );

        $this->view('recruitment/create', [
            'title' => 'Post Recruitment — CySkillShare',
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
            'myGroups' => $myGroups,
            'lookingFor' => self::LOOKING_FOR,
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $data = [
            'title' => (string) $request->input('title', ''),
            'description' => (string) $request->input('description', ''),
            'looking_for' => (string) $request->input('looking_for', 'general'),
            'group_id' => $request->input('group_id'),
        ];

        $skillIds = [];
        foreach ((array) $request->input('skill_ids', []) as $sid) {
            $sid = (int) $sid;
            if ($sid > 0) {
                $skillIds[] = $sid;
            }
        }

        try {
            $post = RecruitmentService::create($userId, $data, $skillIds);
            $this->withSuccess('Recruitment post created.');
            $this->redirect('/recruitment');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/recruitment/create');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/recruitment/create');
        }
    }

    public function apply(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $message = trim((string) $request->input('message', ''));

        try {
            RecruitmentService::apply($id, (int) Auth::id(), $message);
            $this->withSuccess('Application submitted.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/recruitment');
    }
}
