<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Session;
use App\Services\LabCleanupService;
use App\Services\LabService;
use App\Services\LabTaskService;
use InvalidArgumentException;
use RuntimeException;

final class LabAdminController extends Controller
{
    public function index(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('admin/labs/index', [
            'title' => 'Manage Labs — CySkillShare',
            'labs' => LabService::adminList(),
        ]);
    }

    public function createForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->renderForm(null, $request);
    }

    public function create(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $data = $this->extractLabData($request);
        $skillIds = $this->parseIntIds((array) $request->input('skill_ids', []));

        try {
            $lab = LabService::create((int) Auth::id(), $data, $skillIds);
            $this->withSuccess('Lab created.');
            $this->redirect('/admin/labs/' . (int) $lab['id'] . '/edit');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect('/admin/labs/create');
        }
    }

    public function editForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

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

        $this->renderForm($lab, $request);
    }

    public function update(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

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

        $data = $this->extractLabData($request);
        $skillIds = $this->parseIntIds((array) $request->input('skill_ids', []));

        try {
            LabService::update($lab, (int) Auth::id(), $data, $skillIds);
            $this->withSuccess('Lab updated.');
            $this->redirect('/admin/labs/' . $id . '/edit');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect('/admin/labs/' . $id . '/edit');
        }
    }

    public function publish(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();
        $this->setStatus($request, 'published');
    }

    public function archive(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();
        $this->setStatus($request, 'archived');
    }

    public function feature(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

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

        $featured = !empty($lab['featured']);
        LabService::setFeatured($id, (int) Auth::id(), !$featured);
        $this->withSuccess($featured ? 'Lab unfeatured.' : 'Lab featured.');
        $this->redirect('/admin/labs');
    }

    public function tasksForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

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

        $this->view('admin/labs/tasks', [
            'title' => 'Lab Tasks — ' . (string) $lab['title'],
            'lab' => $lab,
            'tasks' => LabTaskService::forLabAdmin($id),
        ]);
    }

    public function addTask(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

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

        $taskData = [
            'title' => trim((string) $request->input('title', '')),
            'description' => (string) $request->input('description', ''),
            'task_type' => (string) $request->input('task_type', 'question'),
            'display_order' => (int) $request->input('display_order', 0),
            'required' => $request->input('required'),
            'points' => (int) $request->input('points', 10),
        ];

        $validationType = (string) $request->input('validation_type', 'exact');
        $answerValue = trim((string) $request->input('answer_value', ''));
        $validation = null;
        if ($answerValue !== '') {
            $validation = [
                'validation_type' => $validationType,
                'validation_config' => ['value' => $answerValue],
            ];
        } elseif ($validationType !== '') {
            $validation = [
                'validation_type' => $validationType,
                'validation_config' => [],
            ];
        }

        try {
            LabTaskService::create($id, $taskData, $validation);
            $this->withSuccess('Task added.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/labs/' . $id . '/tasks');
    }

    public function sweep(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $count = LabCleanupService::sweepExpired(50);
        $this->withSuccess("Cleaned up {$count} expired lab instance(s).");
        $this->redirect('/admin/labs');
    }

    /**
     * @param array<string, mixed>|null $lab
     */
    private function renderForm(?array $lab, Request $request): void
    {
        $isEdit = $lab !== null;
        $id = $isEdit ? (int) $lab['id'] : 0;

        $skillIds = [];
        if ($isEdit) {
            $rows = Database::fetchAll(
                'SELECT skill_id FROM lab_skills WHERE lab_id = ?',
                [$id]
            );
            $skillIds = array_map(static fn(array $r): int => (int) $r['skill_id'], $rows);
        }

        $formData = $isEdit ? $lab : [];
        if ($request->method() === 'POST') {
            $formData = array_merge($formData, $this->extractLabData($request));
            $skillIds = $this->parseIntIds((array) $request->input('skill_ids', []));
        }

        $analytics = null;
        if ($isEdit) {
            $analytics = [
                'starts' => (int) ($lab['start_count'] ?? 0),
                'completions' => (int) ($lab['completion_count'] ?? 0),
            ];
        }

        $this->view('admin/labs/form', [
            'title' => ($isEdit ? 'Edit Lab' : 'New Lab') . ' — Admin',
            'lab' => $formData,
            'skillIds' => $skillIds,
            'categories' => LabService::categories(),
            'templates' => LabService::templates(),
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
            'analytics' => $analytics,
            'isEdit' => $isEdit,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function extractLabData(Request $request): array
    {
        return [
            'title' => trim((string) $request->input('title', '')),
            'slug' => trim((string) $request->input('slug', '')),
            'short_description' => trim((string) $request->input('short_description', '')),
            'description' => (string) $request->input('description', ''),
            'learning_objectives' => (string) $request->input('learning_objectives', ''),
            'category_id' => $request->input('category_id'),
            'template_id' => $request->input('template_id'),
            'difficulty' => (string) $request->input('difficulty', 'beginner'),
            'estimated_minutes' => (int) $request->input('estimated_minutes', 45),
            'status' => (string) $request->input('status', 'draft'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'lifetime_minutes' => (int) $request->input('lifetime_minutes', 60),
            'environment_type' => (string) $request->input('environment_type', 'browser'),
            'max_points' => (int) $request->input('max_points', 100),
            'prerequisite_mode' => (string) $request->input('prerequisite_mode', 'recommended'),
            'allow_pause' => $request->input('allow_pause'),
        ];
    }

    private function setStatus(Request $request, string $status): void
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

        try {
            LabService::update($lab, (int) Auth::id(), ['status' => $status], []);
            $this->withSuccess('Lab status updated to ' . $status . '.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/labs/' . $id . '/edit');
    }

    /** @param list<mixed> $raw @return list<int> */
    private function parseIntIds(array $raw): array
    {
        $ids = [];
        foreach ($raw as $v) {
            $id = (int) $v;
            if ($id > 0) {
                $ids[] = $id;
            }
        }
        return array_values(array_unique($ids));
    }

    private function guardManage(): bool
    {
        if (!Auth::check()) {
            Auth::requireLogin();
        }

        if (!LabService::canManage()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to manage Cyber Labs.',
            ]);
            return false;
        }

        return true;
    }
}
