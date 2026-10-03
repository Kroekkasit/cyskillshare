<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\ContentFormatter;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\ContentReactionService;
use App\Services\ContentVisibilityService;
use App\Services\KnowledgeArticleService;
use App\Services\WriteupService;
use InvalidArgumentException;
use RuntimeException;

final class KnowledgeController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'category' => trim((string) $request->input('category', '')),
            'skill' => trim((string) $request->input('skill', '')),
        ];
        $page = max(1, (int) $request->input('page', 1));

        $result = KnowledgeArticleService::list($filters, $page, 20);
        $categories = WriteupService::categories();

        $categoryGroups = Database::fetchAll(
            "SELECT c.id, c.name, c.slug, c.description,
                    COUNT(a.id) AS article_count
             FROM writeup_categories c
             LEFT JOIN knowledge_articles a ON a.category_id = c.id AND a.status = 'published'
             WHERE c.is_active = 1
             GROUP BY c.id, c.name, c.slug, c.description
             ORDER BY c.display_order, c.name"
        );

        $this->view('knowledge/index', [
            'title' => 'Knowledge Base — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'categoryGroups' => $categoryGroups,
        ]);
    }

    public function category(Request $request): void
    {
        $slug = (string) $request->param('slug');
        $category = Database::fetch(
            'SELECT * FROM writeup_categories WHERE slug = ? AND is_active = 1 LIMIT 1',
            [$slug]
        );

        if ($category === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Category Not Found',
                'message' => 'This knowledge category does not exist.',
            ]);
            return;
        }

        $page = max(1, (int) $request->input('page', 1));
        $result = KnowledgeArticleService::list(['category' => $slug], $page, 20);

        $this->view('knowledge/category', [
            'title' => $category['name'] . ' — Knowledge Base',
            'category' => $category,
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
        ]);
    }

    public function show(Request $request): void
    {
        $slug = (string) $request->param('slug');

        try {
            $detail = KnowledgeArticleService::detail($slug, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Article Not Found',
                    'message' => 'This knowledge article does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/knowledge');
            return;
        }

        $this->view('knowledge/show', [
            'title' => $detail['article']['title'] . ' — Knowledge Base',
            ...$detail,
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

        if ((string) $request->input('action') === 'preview') {
            $this->renderForm(null, $request, $this->buildPreview($request));
            return;
        }

        try {
            $data = self::extractArticleData($request);
            $skills = self::parseIntIds((array) $request->input('skill_ids', []));

            $article = KnowledgeArticleService::create((int) Auth::id(), $data, $skills);
            $this->withSuccess('Article created as draft.');
            $this->redirect('/knowledge/edit/' . (int) $article['id']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $this->renderForm(null, $request);
        }
    }

    public function editForm(Request $request): void
    {
        Auth::requireLogin();
        $article = $this->requireEditableArticle($request);
        if ($article === null) {
            return;
        }
        $this->renderForm($article, $request);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $article = $this->requireEditableArticle($request);
        if ($article === null) {
            return;
        }

        if ((string) $request->input('action') === 'preview') {
            $this->renderForm($article, $request, $this->buildPreview($request));
            return;
        }

        try {
            $data = self::extractArticleData($request);
            $skills = self::parseIntIds((array) $request->input('skill_ids', []));

            KnowledgeArticleService::update($article, (int) Auth::id(), $data, $skills);
            $this->withSuccess('Article updated.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/knowledge/edit/' . (int) $article['id']);
    }

    public function submitReview(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $article = $this->requireEditableArticle($request);
        if ($article === null) {
            return;
        }

        try {
            KnowledgeArticleService::submitForReview($article, (int) Auth::id());
            $this->withSuccess('Article submitted for review.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        }

        $this->redirect('/knowledge/edit/' . (int) $article['id']);
    }

    public function history(Request $request): void
    {
        Auth::requireLogin();
        $id = (int) $request->param('id');
        $article = KnowledgeArticleService::find($id);

        if ($article === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Article Not Found',
                'message' => 'This article does not exist.',
            ]);
            return;
        }

        $viewerId = Auth::id();
        $isOwner = $viewerId === (int) $article['author_id'];
        if (!$isOwner && !ContentVisibilityService::canReview()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You cannot view version history for this article.',
            ]);
            return;
        }

        $versions = Database::fetchAll(
            'SELECT v.*, u.username AS editor_username
             FROM knowledge_article_versions v
             LEFT JOIN users u ON u.id = v.edited_by
             WHERE v.article_id = ?
             ORDER BY v.version DESC',
            [$id]
        );

        $this->view('knowledge/history', [
            'title' => 'Version History — ' . $article['title'],
            'article' => $article,
            'versions' => $versions,
        ]);
    }

    public function versionShow(Request $request): void
    {
        Auth::requireLogin();
        $id = (int) $request->param('id');
        $version = (int) $request->param('version');
        $article = KnowledgeArticleService::find($id);

        if ($article === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Article Not Found',
                'message' => 'This article does not exist.',
            ]);
            return;
        }

        $viewerId = Auth::id();
        $isOwner = $viewerId === (int) $article['author_id'];
        if (!$isOwner && !ContentVisibilityService::canReview()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You cannot view this version.',
            ]);
            return;
        }

        $ver = KnowledgeArticleService::version($id, $version);
        if ($ver === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Version Not Found',
                'message' => 'This version does not exist.',
            ]);
            return;
        }

        $rendered = ContentFormatter::renderWithToc((string) $ver['content']);

        $this->view('knowledge/version', [
            'title' => $article['title'] . ' — v' . $version,
            'article' => $article,
            'version' => $ver,
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
        ]);
    }

    public function react(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $type = (string) $request->input('reaction_type', 'helpful');

        try {
            ContentReactionService::react((int) Auth::id(), 'knowledge_article', $id, $type);
            $this->withSuccess('Reaction recorded.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/knowledge'));
        $this->redirect($referer !== '' ? $referer : '/knowledge');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function requireEditableArticle(Request $request): ?array
    {
        $id = (int) $request->param('id');
        $article = KnowledgeArticleService::find($id);

        if ($article === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Article Not Found',
                'message' => 'This article does not exist.',
            ]);
            return null;
        }

        $userId = (int) Auth::id();
        if ((int) $article['author_id'] !== $userId && !ContentVisibilityService::canManage()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You cannot edit this article.',
            ]);
            return null;
        }

        return $article;
    }

    /**
     * @param array<string, mixed>|null $article
     * @param array{html:string,toc:list<array{level:int,id:string,text:string}>}|null $preview
     */
    private function renderForm(?array $article, Request $request, ?array $preview = null): void
    {
        $isEdit = $article !== null;
        $id = $isEdit ? (int) $article['id'] : 0;

        $skillIds = [];
        if ($isEdit) {
            $skillRows = Database::fetchAll(
                'SELECT skill_id FROM knowledge_article_skills WHERE article_id = ?',
                [$id]
            );
            $skillIds = array_map(static fn(array $r): int => (int) $r['skill_id'], $skillRows);
        }

        $formData = $isEdit ? $article : [];
        if ($request->method() === 'POST') {
            $formData = array_merge($formData, [
                'title' => (string) $request->input('title', ''),
                'slug' => (string) $request->input('slug', ''),
                'summary' => (string) $request->input('summary', ''),
                'content' => (string) $request->input('content', ''),
                'category_id' => $request->input('category_id'),
                'difficulty' => (string) $request->input('difficulty', 'beginner'),
                'visibility' => (string) $request->input('visibility', 'public'),
                'is_official' => $request->input('is_official'),
            ]);
            $skillIds = self::parseIntIds((array) $request->input('skill_ids', []));
        }

        $this->view('knowledge/form', [
            'title' => ($isEdit ? 'Edit Article' : 'New Article') . ' — Knowledge Base',
            'article' => $formData,
            'isEdit' => $isEdit,
            'skillIds' => $skillIds,
            'categories' => WriteupService::categories(),
            'allSkills' => Database::fetchAll(
                'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC'
            ),
            'preview' => $preview,
            'canManage' => ContentVisibilityService::canManage(),
        ]);
    }

    /**
     * @return array{html:string,toc:list<array{level:int,id:string,text:string}>}
     */
    private function buildPreview(Request $request): array
    {
        return ContentFormatter::renderWithToc((string) $request->input('content', ''));
    }

    /**
     * @return array<string, mixed>
     */
    private static function extractArticleData(Request $request): array
    {
        return [
            'title' => (string) $request->input('title', ''),
            'slug' => (string) $request->input('slug', ''),
            'summary' => (string) $request->input('summary', ''),
            'content' => (string) $request->input('content', ''),
            'category_id' => $request->input('category_id'),
            'difficulty' => (string) $request->input('difficulty', 'beginner'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'is_official' => $request->input('is_official'),
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
}
