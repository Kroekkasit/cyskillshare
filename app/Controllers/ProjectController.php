<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\ProjectService;
use App\Services\ProjectVerificationService;
use InvalidArgumentException;
use RuntimeException;

final class ProjectController extends Controller
{
    /** @var list<string> */
    private const PROJECT_TYPES = [
        'security_tool', 'web_security', 'network_security', 'digital_forensics',
        'malware_analysis', 'reverse_engineering', 'ctf', 'automation', 'research',
        'academic', 'open_source', 'home_lab', 'other',
    ];

    public function index(Request $request): void
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'skill' => trim((string) $request->input('skill', '')),
            'type' => trim((string) $request->input('type', '')),
            'technology' => trim((string) $request->input('technology', '')),
            'verified' => $request->input('verified'),
            'sort' => (string) $request->input('sort', 'recent'),
        ];
        $page = max(1, (int) $request->input('page', 1));

        $result = ProjectService::discover($filters, $page, 12);

        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $this->view('portfolio/projects/index', [
            'title' => 'Discover Projects — CySkillShare',
            'isPortfolio' => true,
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'projectTypes' => self::PROJECT_TYPES,
            'skills' => $skills,
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();
        $this->view('portfolio/projects/form', [
            'title' => 'New Project — CySkillShare',
            'isPortfolio' => true,
            'project' => null,
            'technologies' => [],
            'skillIds' => [],
            'allSkills' => self::allSkills(),
            'projectTypes' => self::PROJECT_TYPES,
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        try {
            $data = self::extractProjectData($request);
            $technologies = self::parseTechnologies((string) $request->input('technologies', ''));
            $skills = self::parseSkills((array) $request->input('skill_ids', []));

            $project = ProjectService::create($userId, $data, $technologies, $skills);
            $this->withSuccess('Project created.');
            $this->redirect('/projects/edit/' . (int) $project['id']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/projects/create');
        }
    }

    public function show(Request $request): void
    {
        $username = (string) $request->param('username');
        $slug = (string) $request->param('slug');

        try {
            $detail = ProjectService::detail($username, $slug, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Project Not Found',
                    'message' => 'This project does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/projects');
            return;
        }

        $this->view('portfolio/projects/show', [
            'title' => $detail['project']['title'] . ' — Project',
            'isPortfolio' => true,
            ...$detail,
        ]);
    }

    public function editForm(Request $request): void
    {
        Auth::requireLogin();
        $id = (int) $request->param('id');
        $project = ProjectService::find($id);

        if ($project === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Project Not Found',
                'message' => 'This project does not exist.',
            ]);
            return;
        }

        try {
            ProjectService::requireOwner($project, (int) Auth::id());
        } catch (RuntimeException $e) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not own this project.',
            ]);
            return;
        }

        $skillRows = ProjectService::skills($id);
        $skillIds = array_map(static fn(array $r): int => (int) $r['id'], $skillRows);

        $images = Database::fetchAll(
            'SELECT id, original_name, caption FROM project_images WHERE project_id = ? ORDER BY display_order, id',
            [$id]
        );

        $this->view('portfolio/projects/form', [
            'title' => 'Edit Project — ' . $project['title'],
            'isPortfolio' => true,
            'project' => $project,
            'technologies' => ProjectService::technologies($id),
            'skillIds' => $skillIds,
            'allSkills' => self::allSkills(),
            'projectTypes' => self::PROJECT_TYPES,
            'images' => $images,
        ]);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $project = ProjectService::find($id);

        if ($project === null) {
            $this->withError('Project not found.');
            $this->redirect('/dashboard/portfolio');
            return;
        }

        try {
            $data = self::extractProjectData($request);
            $technologies = self::parseTechnologies((string) $request->input('technologies', ''));
            $skills = self::parseSkills((array) $request->input('skill_ids', []));

            ProjectService::update($project, (int) Auth::id(), $data, $technologies, $skills);
            $this->withSuccess('Project updated.');
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->withError('You do not own this project.');
            } else {
                $this->withError($e->getMessage());
            }
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/projects/edit/' . $id);
    }

    public function publish(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $this->changePublishStatus($request, 'published', 'Project published.');
    }

    public function archive(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $this->changePublishStatus($request, 'archived', 'Project archived.');
    }

    public function feature(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $project = ProjectService::find($id);

        if ($project === null) {
            $this->withError('Project not found.');
            $this->redirect('/dashboard/portfolio');
            return;
        }

        $featured = !empty($request->input('featured'));

        try {
            ProjectService::setFeatured($project, (int) Auth::id(), $featured);
            $this->withSuccess($featured ? 'Project featured.' : 'Project unfeatured.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/dashboard/portfolio'));
        $this->redirect($referer !== '' ? $referer : '/dashboard/portfolio');
    }

    public function requestVerification(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');

        try {
            ProjectVerificationService::request($id, (int) Auth::id());
            $this->withSuccess('Verification request submitted.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/projects/edit/' . $id);
    }

    public function react(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $type = (string) $request->input('reaction_type', 'helpful');

        try {
            ProjectService::react($id, (int) Auth::id(), $type);
            $this->withSuccess('Reaction recorded.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/projects'));
        $this->redirect($referer !== '' ? $referer : '/projects');
    }

    private function changePublishStatus(Request $request, string $status, string $successMsg): void
    {
        $id = (int) $request->param('id');
        $project = ProjectService::find($id);

        if ($project === null) {
            $this->withError('Project not found.');
            $this->redirect('/dashboard/portfolio');
            return;
        }

        try {
            ProjectService::setPublishStatus($project, (int) Auth::id(), $status);
            $this->withSuccess($successMsg);
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/dashboard/portfolio'));
        $this->redirect($referer !== '' ? $referer : '/dashboard/portfolio');
    }

    /**
     * @return array<string, mixed>
     */
    private static function extractProjectData(Request $request): array
    {
        return [
            'title' => (string) $request->input('title', ''),
            'slug' => (string) $request->input('slug', ''),
            'short_description' => (string) $request->input('short_description', ''),
            'description' => (string) $request->input('description', ''),
            'project_type' => (string) $request->input('project_type', 'other'),
            'status' => (string) $request->input('status', 'planning'),
            'publish_status' => (string) $request->input('publish_status', 'draft'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'repository_url' => (string) $request->input('repository_url', ''),
            'demo_url' => (string) $request->input('demo_url', ''),
            'documentation_url' => (string) $request->input('documentation_url', ''),
            'start_date' => (string) $request->input('start_date', ''),
            'end_date' => (string) $request->input('end_date', ''),
            'display_order' => (int) $request->input('display_order', 0),
        ];
    }

    /**
     * @return list<string>
     */
    private static function parseTechnologies(string $input): array
    {
        if ($input === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }

    /**
     * @param list<mixed> $ids
     * @return list<array{skill_id:int,importance:string}>
     */
    private static function parseSkills(array $ids): array
    {
        $out = [];
        foreach ($ids as $sid) {
            $sid = (int) $sid;
            if ($sid > 0) {
                $out[] = ['skill_id' => $sid, 'importance' => 'secondary'];
            }
        }
        return $out;
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    private static function allSkills(): array
    {
        return Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
        );
    }
}
