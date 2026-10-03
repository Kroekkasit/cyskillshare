<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SkillLevel;
use App\Services\SkillEvidenceService;
use App\Services\SkillProgressService;
use App\Services\SkillService;
use InvalidArgumentException;
use RuntimeException;

final class SkillAdminController extends Controller
{
    public function index(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $categories = Database::fetchAll(
            'SELECT * FROM skill_categories ORDER BY display_order ASC, name ASC'
        );

        $grouped = [];
        foreach ($categories as $cat) {
            $skills = Database::fetchAll(
                'SELECT * FROM skills WHERE category_id = ? ORDER BY display_order ASC, name ASC',
                [(int) $cat['id']]
            );
            $grouped[] = [
                'category' => $cat,
                'skills' => $skills,
            ];
        }

        $this->view('skills/admin/index', [
            'title' => 'Manage Skills — CySkillShare',
            'grouped' => $grouped,
            'canRecalculate' => SkillService::canRecalculate(),
        ]);
    }

    public function createForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('skills/admin/form', [
            'title' => 'New Skill — CySkillShare',
            'skill' => null,
            'categories' => $this->allCategories(),
            'allSkills' => $this->allSkillsList(),
        ]);
    }

    public function create(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $data = $this->extractSkillData($request);

        $validator = Validator::make($data, [
            'name' => 'required|string|min_length:2|max_length:150',
            'description' => 'required|string|min_length:5|max_length:5000',
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), $data);
            $this->redirect('/admin/skills/create');
        }

        try {
            $skill = SkillService::createSkill($data, (int) Auth::id());
            $this->withSuccess('Skill created.');
            $this->redirect('/admin/skills/' . $skill->id . '/edit');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect('/admin/skills/create');
        }
    }

    public function editForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $id = (int) $request->param('id');
        $skill = Skill::find($id);

        if ($skill === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Skill Not Found',
                'message' => 'This skill does not exist.',
            ]);
            return;
        }

        $this->view('skills/admin/form', [
            'title' => 'Edit Skill — CySkillShare',
            'skill' => $skill,
            'categories' => $this->allCategories(),
            'allSkills' => $this->allSkillsList($skill->id),
        ]);
    }

    public function update(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $skill = Skill::find($id);

        if ($skill === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Skill Not Found',
                'message' => 'This skill does not exist.',
            ]);
            return;
        }

        $data = $this->extractSkillData($request);

        $validator = Validator::make($data, [
            'name' => 'required|string|min_length:2|max_length:150',
            'description' => 'required|string|min_length:5|max_length:5000',
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), $data);
            $this->redirect('/admin/skills/' . $id . '/edit');
        }

        try {
            SkillService::updateSkill($skill, $data, (int) Auth::id());
            $this->withSuccess('Skill updated.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', $data);
        }

        $this->redirect('/admin/skills/' . $id . '/edit');
    }

    public function requirementsForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $id = (int) $request->param('id');
        $skill = Skill::find($id);

        if ($skill === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Skill Not Found',
                'message' => 'This skill does not exist.',
            ]);
            return;
        }

        $requirements = Database::fetchAll(
            'SELECT * FROM skill_requirements WHERE skill_id = ? ORDER BY target_level ASC, id ASC',
            [$id]
        );

        $prerequisites = $skill->prerequisites();
        $levels = SkillLevel::mapByLevel();
        $evidenceTypes = config('skills.evidence_types', []);
        $allSkills = $this->allSkillsList($id);

        $this->view('skills/admin/requirements', [
            'title' => 'Requirements — ' . $skill->name,
            'skill' => $skill,
            'requirements' => $requirements,
            'prerequisites' => $prerequisites,
            'levels' => $levels,
            'evidenceTypes' => is_array($evidenceTypes) ? $evidenceTypes : [],
            'allSkills' => $allSkills,
        ]);
    }

    public function addRequirement(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $skill = Skill::find($id);

        if ($skill === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Skill Not Found',
                'message' => 'This skill does not exist.',
            ]);
            return;
        }

        $data = [
            'target_level' => (int) $request->input('target_level', 1),
            'evidence_type' => (string) $request->input('evidence_type', ''),
            'minimum_count' => (int) $request->input('minimum_count', 1),
            'minimum_difficulty' => (string) $request->input('minimum_difficulty', ''),
            'is_required' => $request->input('is_required'),
            'weight' => (float) $request->input('weight', 1),
            'description' => trim((string) $request->input('description', '')),
        ];

        try {
            SkillService::addRequirement($id, $data, (int) Auth::id());
            $this->withSuccess('Requirement added.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/skills/' . $id . '/requirements');
    }

    public function deleteRequirement(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $reqId = (int) $request->param('id');
        $row = Database::fetch('SELECT skill_id FROM skill_requirements WHERE id = ?', [$reqId]);
        $skillId = $row ? (int) $row['skill_id'] : 0;

        try {
            SkillService::deleteRequirement($reqId, (int) Auth::id());
            $this->withSuccess('Requirement deleted.');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect($skillId > 0 ? '/admin/skills/' . $skillId . '/requirements' : '/admin/skills');
    }

    public function addPrerequisite(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $prereqId = (int) $request->input('prerequisite_skill_id', 0);
        $minLevel = (int) $request->input('minimum_level', 1);

        try {
            SkillService::addPrerequisite($id, $prereqId, $minLevel, (int) Auth::id());
            $this->withSuccess('Prerequisite added.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/skills/' . $id . '/requirements');
    }

    public function recalculateForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        if (!SkillService::canRecalculate()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to recalculate skill progress.',
            ]);
            return;
        }

        $this->view('skills/admin/recalculate', [
            'title' => 'Recalculate Progress — CySkillShare',
        ]);
    }

    public function recalculate(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        if (!SkillService::canRecalculate()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to recalculate skill progress.',
            ]);
            return;
        }

        $userId = trim((string) $request->input('user_id', ''));

        if ($userId !== '') {
            $uid = (int) $userId;
            $backfilled = SkillEvidenceService::backfillFromSolves($uid);
            $count = SkillProgressService::recalculateUser($uid);
            $this->withSuccess(
                "Backfilled {$backfilled} solve(s); recalculated {$count} skill(s) for user #{$uid}."
            );
        } else {
            $backfilled = SkillEvidenceService::backfillFromSolves();
            $count = SkillProgressService::recalculateAll();
            $this->withSuccess(
                "Backfilled {$backfilled} solve(s); recalculated {$count} user/skill pair(s)."
            );
        }

        $this->redirect('/admin/skills/recalculate');
    }

    private function guardManage(): bool
    {
        if (!Auth::check()) {
            Auth::requireLogin();
        }

        if (!SkillService::canManage()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to manage the skill tree.',
            ]);
            return false;
        }

        return true;
    }

    /**
     * @return list<SkillCategory>
     */
    private function allCategories(): array
    {
        $rows = Database::fetchAll(
            'SELECT * FROM skill_categories ORDER BY display_order ASC, name ASC'
        );
        return array_map(static fn(array $r): SkillCategory => new SkillCategory($r), $rows);
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    private function allSkillsList(?int $excludeId = null): array
    {
        $sql = 'SELECT id, name, slug FROM skills';
        $params = [];
        if ($excludeId !== null) {
            $sql .= ' WHERE id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' ORDER BY name ASC';
        return Database::fetchAll($sql, $params);
    }

    /**
     * @return array<string, mixed>
     */
    private function extractSkillData(Request $request): array
    {
        return [
            'name' => trim((string) $request->input('name', '')),
            'description' => trim((string) $request->input('description', '')),
            'category_id' => (int) $request->input('category_id', 0),
            'parent_skill_id' => $request->input('parent_skill_id'),
            'icon' => trim((string) $request->input('icon', '')) ?: null,
            'display_order' => (int) $request->input('display_order', 0),
            'is_active' => $request->input('is_active'),
            'is_gated' => $request->input('is_gated'),
        ];
    }
}
