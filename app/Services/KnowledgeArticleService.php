<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\ContentFormatter;
use App\Core\Database;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

final class KnowledgeArticleService
{
    /**
     * @return array<string, mixed>|null
     */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM knowledge_articles WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM knowledge_articles WHERE slug = ? LIMIT 1', [$slug]);
    }

    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = WriteupService::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM knowledge_articles WHERE slug = ?';
            $params = [$candidate];
            if ($excludeId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $excludeId;
            }
            if (Database::fetch($sql . ' LIMIT 1', $params) === null) {
                return $candidate;
            }
            $candidate = $slug . '-' . $i;
            $i++;
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function create(int $authorId, array $data, array $skillIds = []): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $content = (string) ($data['content'] ?? '');
        if ($title === '' || $content === '') {
            throw new InvalidArgumentException('Title and content are required.');
        }

        $isOfficial = ContentVisibilityService::canManage() && Auth::hasAnyRole(['instructor', 'admin'])
            && !empty($data['is_official']);

        $slugBase = trim((string) ($data['slug'] ?? ''));
        if ($slugBase === '') {
            $slugBase = $title;
        }
        $slug = self::uniqueSlug($slugBase);
        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO knowledge_articles
                 (author_id, category_id, title, slug, summary, content, difficulty, status, visibility, is_official, version)
                 VALUES (?, ?, ?, ?, ?, ?, ?, \'draft\', ?, ?, 1)',
                [
                    $authorId,
                    !empty($data['category_id']) ? (int) $data['category_id'] : null,
                    $title,
                    $slug,
                    mb_substr(trim((string) ($data['summary'] ?? '')), 0, 500) ?: null,
                    $content,
                    in_array($data['difficulty'] ?? '', ['beginner', 'intermediate', 'advanced', 'expert'], true)
                        ? $data['difficulty'] : 'beginner',
                    in_array($data['visibility'] ?? '', ['public', 'community', 'private'], true)
                        ? $data['visibility'] : 'public',
                    $isOfficial ? 1 : 0,
                ]
            );
            $id = (int) Database::lastInsertId();
            Database::execute(
                'INSERT INTO knowledge_article_versions (article_id, version, content, title, edited_by, change_summary)
                 VALUES (?, 1, ?, ?, ?, ?)',
                [$id, $content, $title, $authorId, 'Initial draft']
            );
            Database::execute(
                'INSERT IGNORE INTO knowledge_article_contributors (article_id, user_id, role) VALUES (?, ?, \'author\')',
                [$id, $authorId]
            );
            self::syncSkills($id, $skillIds);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($authorId, 'article_created', 'knowledge_article', $id);
        $row = self::find($id);
        if ($row === null) {
            throw new RuntimeException('Article missing after create.');
        }
        return $row;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function update(array $article, int $actorId, array $data, array $skillIds = []): array
    {
        $isAuthor = (int) $article['author_id'] === $actorId;
        if (!$isAuthor && !ContentVisibilityService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $title = trim((string) ($data['title'] ?? $article['title']));
        $content = (string) ($data['content'] ?? $article['content']);
        $contentChanged = $content !== (string) $article['content'] || $title !== (string) $article['title'];

        if ($contentChanged) {
            $ver = (int) $article['version'] + 1;
            Database::execute(
                'INSERT INTO knowledge_article_versions (article_id, version, content, title, edited_by, change_summary)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    (int) $article['id'],
                    $ver,
                    $content,
                    $title,
                    $actorId,
                    mb_substr((string) ($data['change_summary'] ?? 'Content update'), 0, 500),
                ]
            );
            Database::execute(
                'UPDATE knowledge_articles SET version = ? WHERE id = ?',
                [$ver, (int) $article['id']]
            );
        }

        Database::execute(
            'UPDATE knowledge_articles SET
               title = ?, summary = ?, content = ?, category_id = ?, difficulty = ?, visibility = ?
             WHERE id = ?',
            [
                $title,
                mb_substr(trim((string) ($data['summary'] ?? '')), 0, 500) ?: null,
                $content,
                !empty($data['category_id']) ? (int) $data['category_id'] : null,
                in_array($data['difficulty'] ?? '', ['beginner', 'intermediate', 'advanced', 'expert'], true)
                    ? $data['difficulty'] : $article['difficulty'],
                in_array($data['visibility'] ?? '', ['public', 'community', 'private'], true)
                    ? $data['visibility'] : $article['visibility'],
                (int) $article['id'],
            ]
        );
        self::syncSkills((int) $article['id'], $skillIds);
        ActivityLogService::log($actorId, 'article_updated', 'knowledge_article', (int) $article['id']);

        $row = self::find((int) $article['id']);
        if ($row === null) {
            throw new RuntimeException('Article missing after update.');
        }
        return $row;
    }

    public static function submitForReview(array $article, int $actorId): void
    {
        if ((int) $article['author_id'] !== $actorId) {
            throw new RuntimeException('Forbidden', 403);
        }
        Database::execute(
            "UPDATE knowledge_articles SET status = 'under_review' WHERE id = ?",
            [(int) $article['id']]
        );
        Database::execute(
            "INSERT INTO knowledge_article_reviews (article_id, reviewer_id, status, review_note)
             VALUES (?, ?, 'pending', NULL)",
            [(int) $article['id'], $actorId]
        );
        ActivityLogService::log($actorId, 'article_submitted', 'knowledge_article', (int) $article['id']);
    }

    public static function review(int $articleId, int $reviewerId, string $status, string $note = ''): void
    {
        if (!ContentVisibilityService::canReview()) {
            throw new RuntimeException('Forbidden', 403);
        }
        if (!in_array($status, ['approved', 'changes_requested', 'rejected'], true)) {
            throw new InvalidArgumentException('Invalid review status.');
        }
        $article = self::find($articleId);
        if ($article === null) {
            throw new InvalidArgumentException('Article not found.');
        }

        Database::execute(
            "INSERT INTO knowledge_article_reviews (article_id, reviewer_id, status, review_note)
             VALUES (?, ?, ?, ?)",
            [$articleId, $reviewerId, $status, mb_substr($note, 0, 2000)]
        );

        if ($status === 'approved') {
            Database::execute(
                "UPDATE knowledge_articles SET status = 'published', published_at = COALESCE(published_at, NOW())
                 WHERE id = ?",
                [$articleId]
            );
            Database::execute(
                "INSERT IGNORE INTO knowledge_article_contributors (article_id, user_id, role)
                 VALUES (?, ?, 'reviewer')",
                [$articleId, $reviewerId]
            );
            ActivityLogService::log($reviewerId, 'article_published', 'knowledge_article', $articleId);
            NotificationService::create(
                (int) $article['author_id'],
                'article_approved',
                'Knowledge article approved',
                '“' . mb_strimwidth((string) $article['title'], 0, 80, '…') . '” was approved.',
                'knowledge_article',
                $articleId
            );
        } elseif ($status === 'changes_requested') {
            Database::execute(
                "UPDATE knowledge_articles SET status = 'draft' WHERE id = ?",
                [$articleId]
            );
            NotificationService::create(
                (int) $article['author_id'],
                'article_changes',
                'Changes requested on your article',
                mb_strimwidth($note !== '' ? $note : 'Please revise your knowledge article.', 0, 200, '…'),
                'knowledge_article',
                $articleId
            );
        } else {
            Database::execute(
                "UPDATE knowledge_articles SET status = 'archived' WHERE id = ?",
                [$articleId]
            );
            NotificationService::create(
                (int) $article['author_id'],
                'article_rejected',
                'Knowledge article not approved',
                mb_strimwidth($note !== '' ? $note : 'Your article was not approved.', 0, 200, '…'),
                'knowledge_article',
                $articleId
            );
        }
        ActivityLogService::log($reviewerId, 'article_reviewed', 'knowledge_article', $articleId, [
            'status' => $status,
        ]);
    }

    /**
     * @param list<int> $skillIds
     */
    public static function syncSkills(int $articleId, array $skillIds): void
    {
        Database::execute('DELETE FROM knowledge_article_skills WHERE article_id = ?', [$articleId]);
        $seen = [];
        foreach ($skillIds as $sid) {
            $sid = (int) $sid;
            if ($sid <= 0 || isset($seen[$sid])) {
                continue;
            }
            Database::execute(
                'INSERT INTO knowledge_article_skills (article_id, skill_id, weight) VALUES (?, ?, 1)',
                [$articleId, $sid]
            );
            $seen[$sid] = true;
        }
    }

    /**
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset = ($page - 1) * $perPage;
        $viewerId = Auth::id();

        $where = ["a.status = 'published'"];
        $params = [];
        if ($viewerId === null) {
            $where[] = "a.visibility = 'public'";
        } else {
            $where[] = "(a.visibility IN ('public','community') OR a.author_id = ?)";
            $params[] = $viewerId;
        }
        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = (string) $filters['category'];
        }
        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM knowledge_article_skills kas
                INNER JOIN skills s ON s.id = kas.skill_id
                WHERE kas.article_id = a.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['search'])) {
            $q = trim((string) $filters['search']);
            $where[] = '(MATCH(a.title, a.summary, a.content) AGAINST (? IN NATURAL LANGUAGE MODE) OR a.title LIKE ?)';
            $params[] = $q;
            $params[] = '%' . $q . '%';
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM knowledge_articles a
             LEFT JOIN writeup_categories c ON c.id = a.category_id
             WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $items = Database::fetchAll(
            "SELECT a.id, a.title, a.slug, a.summary, a.difficulty, a.featured, a.is_official,
                    a.view_count, a.helpful_count, a.published_at, a.author_id,
                    u.username, c.name AS category_name, c.slug AS category_slug
             FROM knowledge_articles a
             INNER JOIN users u ON u.id = a.author_id
             LEFT JOIN writeup_categories c ON c.id = a.category_id
             WHERE {$whereSql}
             ORDER BY a.featured DESC, a.published_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'page' => $page];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(string $slug, ?int $viewerId): array
    {
        $article = self::findBySlug($slug);
        if ($article === null
            || !ContentVisibilityService::canView(
                (string) $article['visibility'],
                (int) $article['author_id'],
                $viewerId,
                (string) $article['status']
            )
        ) {
            throw new InvalidArgumentException('Article not found.', 404);
        }

        if ($viewerId !== (int) $article['author_id']) {
            Database::execute(
                'UPDATE knowledge_articles SET view_count = view_count + 1 WHERE id = ?',
                [(int) $article['id']]
            );
        }

        $rendered = ContentFormatter::renderWithToc((string) $article['content']);
        $author = User::find((int) $article['author_id']);
        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug FROM knowledge_article_skills kas
             INNER JOIN skills s ON s.id = kas.skill_id WHERE kas.article_id = ?',
            [(int) $article['id']]
        );
        $sources = Database::fetchAll(
            'SELECT * FROM knowledge_article_sources WHERE article_id = ? ORDER BY id',
            [(int) $article['id']]
        );
        $contributors = Database::fetchAll(
            'SELECT u.username, kac.role FROM knowledge_article_contributors kac
             INNER JOIN users u ON u.id = kac.user_id WHERE kac.article_id = ?
             ORDER BY FIELD(kac.role,\'author\',\'editor\',\'reviewer\',\'contributor\'), u.username',
            [(int) $article['id']]
        );
        $versions = Database::fetchAll(
            'SELECT version, change_summary, created_at, edited_by FROM knowledge_article_versions
             WHERE article_id = ? ORDER BY version DESC LIMIT 20',
            [(int) $article['id']]
        );
        $category = $article['category_id']
            ? Database::fetch('SELECT * FROM writeup_categories WHERE id = ?', [(int) $article['category_id']])
            : null;

        return [
            'article' => $article,
            'author' => $author,
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
            'skills' => $skills,
            'sources' => $sources,
            'contributors' => $contributors,
            'versions' => $versions,
            'category' => $category,
            'is_owner' => $viewerId === (int) $article['author_id'],
            'can_review' => ContentVisibilityService::canReview(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function version(int $articleId, int $version): ?array
    {
        return Database::fetch(
            'SELECT * FROM knowledge_article_versions WHERE article_id = ? AND version = ? LIMIT 1',
            [$articleId, $version]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function pendingReviews(): array
    {
        return Database::fetchAll(
            "SELECT a.id, a.title, a.slug, a.updated_at, u.username
             FROM knowledge_articles a
             INNER JOIN users u ON u.id = a.author_id
             WHERE a.status = 'under_review'
             ORDER BY a.updated_at ASC"
        );
    }

    /**
     * Learning materials for a skill page.
     *
     * @return array{articles:list<array<string,mixed>>,writeups:list<array<string,mixed>>}
     */
    public static function forSkill(string $skillSlug, ?int $viewerId = null): array
    {
        $articles = Database::fetchAll(
            "SELECT a.title, a.slug, a.difficulty, a.is_official
             FROM knowledge_articles a
             INNER JOIN knowledge_article_skills kas ON kas.article_id = a.id
             INNER JOIN skills s ON s.id = kas.skill_id AND s.slug = ?
             WHERE a.status = 'published' AND a.visibility = 'public'
             ORDER BY FIELD(a.difficulty,'beginner','intermediate','advanced','expert'), a.title
             LIMIT 12",
            [$skillSlug]
        );
        $writeups = Database::fetchAll(
            "SELECT w.title, w.slug, w.difficulty, u.username, w.reading_time
             FROM writeups w
             INNER JOIN writeup_skills ws ON ws.writeup_id = w.id
             INNER JOIN skills s ON s.id = ws.skill_id AND s.slug = ?
             INNER JOIN users u ON u.id = w.user_id
             WHERE w.status = 'published' AND w.visibility = 'public' AND w.deleted_at IS NULL
             ORDER BY w.published_at DESC
             LIMIT 8",
            [$skillSlug]
        );
        return ['articles' => $articles, 'writeups' => $writeups];
    }
}
