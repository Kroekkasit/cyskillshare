<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\SkillCategory;
use App\Services\SkillService;
use App\Services\SkillTreeService;
use InvalidArgumentException;

final class SkillController extends Controller
{
    public function index(Request $request): void
    {
        $search = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $userId = Auth::id();

        $tree = SkillTreeService::tree(
            $userId,
            $search !== '' ? $search : null,
            $category !== '' ? $category : null
        );

        $data = [
            'title' => 'Skill Tree — CySkillShare',
            'tree' => $tree,
            'search' => $search,
            'categoryFilter' => $category,
            'categories' => SkillCategory::allActive(),
            'levels' => \App\Models\SkillLevel::mapByLevel(),
        ];

        if ($userId !== null) {
            $data['stats'] = SkillTreeService::userStats($userId);
        }

        $this->view('skills/index', $data);
    }

    public function show(Request $request): void
    {
        $slug = (string) $request->param('slug');

        try {
            $detail = SkillTreeService::skillDetail($slug, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Skill Not Found',
                    'message' => 'This skill does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/skills');
            return;
        }

        $progress = $detail['progress'];
        $currentLevel = (int) ($progress['level'] ?? 0);
        $nextLevel = min(5, $currentLevel + 1);
        $nextLevelName = $detail['levels'][$nextLevel]->name ?? \App\Models\SkillLevel::nameFor($nextLevel);

        $this->view('skills/show', [
            'title' => $detail['skill']->name . ' — Skill Tree',
            'detail' => $detail,
            'nextLevelName' => $nextLevelName,
            'showProgress' => Auth::check() && $currentLevel < 5,
            'learnArticles' => $detail['learn_articles'] ?? [],
            'learnWriteups' => $detail['learn_writeups'] ?? [],
        ]);
    }

    public function privacy(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $visibility = (string) $request->input('visibility', 'public');

        try {
            SkillService::setSkillsVisibility((int) Auth::id(), $visibility);
            $this->withSuccess('Skills visibility updated.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/profile/' . Auth::user()?->username));
        $this->redirect($referer !== '' ? $referer : '/skills');
    }
}
