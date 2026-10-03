<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\ContentFormatter;
use App\Core\Database;
use App\Core\RateLimiter;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

final class WriteupService
{
    /**
     * @return array<string, mixed>|null
     */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM writeups WHERE id = ? AND deleted_at IS NULL LIMIT 1', [$id]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findByUserSlug(int $userId, string $slug): ?array
    {
        return Database::fetch(
            'SELECT * FROM writeups WHERE user_id = ? AND slug = ? AND deleted_at IS NULL LIMIT 1',
            [$userId, $slug]
        );
    }

    public static function slugify(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'writeup';
    }

    public static function uniqueSlug(int $userId, string $base, ?int $excludeId = null): string
    {
        $slug = self::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM writeups WHERE user_id = ? AND slug = ? AND deleted_at IS NULL';
            $params = [$userId, $candidate];
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
     * @param list<string> $tags
     * @param list<int> $challengeIds
     */
    public static function create(int $userId, array $data, array $skillIds = [], array $tags = [], array $challengeIds = []): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '' || mb_strlen($title) > 200) {
            throw new InvalidArgumentException('Invalid title.');
        }
        $content = (string) ($data['content'] ?? '');
        $max = (int) config('writeups.max_content_length', 200000);
        if ($content === '' || mb_strlen($content) > $max) {
            throw new InvalidArgumentException('Invalid content length.');
        }

        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true)
            ? (string) $data['status']
            : 'draft';
        $visibility = in_array($data['visibility'] ?? 'public', ['public', 'community', 'private'], true)
            ? (string) $data['visibility']
            : 'public';
        $difficulty = in_array($data['difficulty'] ?? 'beginner', ['beginner', 'intermediate', 'advanced', 'expert'], true)
            ? (string) $data['difficulty']
            : 'beginner';

        $slugBase = trim((string) ($data['slug'] ?? ''));
        if ($slugBase === '') {
            $slugBase = $title;
        }
        $slug = self::uniqueSlug($userId, $slugBase);
        $reading = ContentFormatter::readingTimeMinutes($content);

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO writeups
                 (user_id, category_id, title, slug, short_description, content, difficulty,
                  status, visibility, reading_time, published_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $userId,
                    !empty($data['category_id']) ? (int) $data['category_id'] : null,
                    $title,
                    $slug,
                    mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500) ?: null,
                    $content,
                    $difficulty,
                    $status,
                    $visibility,
                    $reading,
                    $status === 'published' ? date('Y-m-d H:i:s') : null,
                ]
            );
            $id = (int) Database::lastInsertId();
            self::syncSkills($id, $skillIds);
            self::syncTags($id, $tags);
            self::syncChallenges($id, $challengeIds);
            if (!empty($data['project_id'])) {
                Database::execute(
                    'INSERT IGNORE INTO project_writeups (project_id, writeup_id) VALUES (?, ?)',
                    [(int) $data['project_id'], $id]
                );
            }
            if (!empty($data['thread_id'])) {
                Database::execute(
                    'INSERT IGNORE INTO writeup_threads (writeup_id, thread_id) VALUES (?, ?)',
                    [$id, (int) $data['thread_id']]
                );
            }
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($userId, 'writeup_created', 'writeup', $id);
        if ($status === 'published') {
            ActivityLogService::log($userId, 'writeup_published', 'writeup', $id);
            self::syncEvidence($id);
        }

        $row = self::find($id);
        if ($row === null) {
            throw new RuntimeException('Writeup missing after create.');
        }
        return $row;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     * @param list<string> $tags
     * @param list<int> $challengeIds
     */
    public static function update(array $writeup, int $actorId, array $data, array $skillIds = [], array $tags = [], array $challengeIds = []): array
    {
        if ((int) $writeup['user_id'] !== $actorId && !ContentVisibilityService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $content = (string) ($data['content'] ?? $writeup['content']);
        $title = trim((string) ($data['title'] ?? $writeup['title']));
        $wasPublished = ($writeup['status'] ?? '') === 'published';

        // Snapshot previous published version on significant edit
        if ($wasPublished && ($content !== (string) $writeup['content'] || $title !== (string) $writeup['title'])) {
            $ver = (int) (Database::fetch(
                'SELECT COALESCE(MAX(version), 0) AS v FROM writeup_versions WHERE writeup_id = ?',
                [(int) $writeup['id']]
            )['v'] ?? 0) + 1;
            Database::execute(
                'INSERT INTO writeup_versions (writeup_id, version, title, content, edited_by, change_summary)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    (int) $writeup['id'],
                    $ver,
                    (string) $writeup['title'],
                    (string) $writeup['content'],
                    $actorId,
                    mb_substr((string) ($data['change_summary'] ?? 'Updated writeup'), 0, 500),
                ]
            );
        }

        $status = (string) ($data['status'] ?? $writeup['status']);
        if (!in_array($status, ['draft', 'published', 'archived', 'under_review'], true)) {
            $status = (string) $writeup['status'];
        }
        $visibility = (string) ($data['visibility'] ?? $writeup['visibility']);
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            $visibility = (string) $writeup['visibility'];
        }
        $difficulty = (string) ($data['difficulty'] ?? $writeup['difficulty']);
        if (!in_array($difficulty, ['beginner', 'intermediate', 'advanced', 'expert'], true)) {
            $difficulty = (string) $writeup['difficulty'];
        }

        if (array_key_exists('slug', $data)) {
            $slugBase = trim((string) $data['slug']);
            $slug = $slugBase !== ''
                ? self::uniqueSlug((int) $writeup['user_id'], $slugBase, (int) $writeup['id'])
                : (string) $writeup['slug'];
        } else {
            $slug = (string) $writeup['slug'];
        }

        Database::execute(
            'UPDATE writeups SET
               title = ?, slug = ?, short_description = ?, content = ?, category_id = ?,
               difficulty = ?, status = ?, visibility = ?, reading_time = ?,
               published_at = CASE
                 WHEN ? = \'published\' AND published_at IS NULL THEN NOW()
                 ELSE published_at
               END
             WHERE id = ?',
            [
                $title,
                $slug,
                mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500) ?: null,
                $content,
                !empty($data['category_id']) ? (int) $data['category_id'] : null,
                $difficulty,
                $status,
                $visibility,
                ContentFormatter::readingTimeMinutes($content),
                $status,
                (int) $writeup['id'],
            ]
        );

        self::syncSkills((int) $writeup['id'], $skillIds);
        self::syncTags((int) $writeup['id'], $tags);
        self::syncChallenges((int) $writeup['id'], $challengeIds);

        ActivityLogService::log($actorId, 'writeup_updated', 'writeup', (int) $writeup['id']);
        if ($status === 'published' && !$wasPublished) {
            ActivityLogService::log($actorId, 'writeup_published', 'writeup', (int) $writeup['id']);
            NotificationService::create(
                (int) $writeup['user_id'],
                'writeup_published',
                'Writeup published',
                '“' . mb_strimwidth($title, 0, 80, '…') . '” is now published.',
                'writeup',
                (int) $writeup['id']
            );
        }
        if ($status === 'published') {
            self::syncEvidence((int) $writeup['id']);
        }
        if ($status === 'archived') {
            ActivityLogService::log($actorId, 'writeup_archived', 'writeup', (int) $writeup['id']);
        }

        $row = self::find((int) $writeup['id']);
        if ($row === null) {
            throw new RuntimeException('Writeup missing after update.');
        }
        return $row;
    }

    public static function autosave(array $writeup, int $actorId, string $title, string $content): void
    {
        if ((int) $writeup['user_id'] !== $actorId) {
            throw new RuntimeException('Forbidden', 403);
        }
        $min = (int) config('writeups.autosave_min_interval_seconds', 8);
        if (!RateLimiter::attempt($actorId, 'writeup_autosave_' . (int) $writeup['id'], 1, $min)) {
            return; // silently skip — debounce
        }
        RateLimiter::hit($actorId, 'writeup_autosave_' . (int) $writeup['id']);

        $max = (int) config('writeups.max_content_length', 200000);
        if (mb_strlen($content) > $max) {
            throw new InvalidArgumentException('Content too long.');
        }

        Database::execute(
            'UPDATE writeups SET title = ?, content = ?, reading_time = ? WHERE id = ? AND user_id = ?',
            [
                mb_substr(trim($title) !== '' ? trim($title) : (string) $writeup['title'], 0, 200),
                $content,
                ContentFormatter::readingTimeMinutes($content),
                (int) $writeup['id'],
                $actorId,
            ]
        );
    }

    /**
     * @param list<int> $skillIds
     */
    public static function syncSkills(int $writeupId, array $skillIds): void
    {
        Database::execute('DELETE FROM writeup_skills WHERE writeup_id = ?', [$writeupId]);
        $max = (int) config('writeups.max_skills', 8);
        $n = 0;
        $seen = [];
        foreach ($skillIds as $sid) {
            $sid = (int) $sid;
            if ($sid <= 0 || isset($seen[$sid]) || $n >= $max) {
                continue;
            }
            Database::execute(
                'INSERT INTO writeup_skills (writeup_id, skill_id, weight) VALUES (?, ?, 1)',
                [$writeupId, $sid]
            );
            $seen[$sid] = true;
            $n++;
        }
    }

    /**
     * @param list<string> $tags
     */
    public static function syncTags(int $writeupId, array $tags): void
    {
        Database::execute('DELETE FROM writeup_tag_map WHERE writeup_id = ?', [$writeupId]);
        $max = (int) config('writeups.max_tags', 12);
        $n = 0;
        $seen = [];
        foreach ($tags as $raw) {
            if ($n >= $max) {
                break;
            }
            $slug = strtolower(trim((string) $raw));
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
            $slug = trim($slug, '-');
            if ($slug === '' || isset($seen[$slug])) {
                continue;
            }
            $tag = Database::fetch('SELECT id FROM writeup_tags WHERE slug = ? LIMIT 1', [$slug]);
            if ($tag === null) {
                Database::execute(
                    'INSERT INTO writeup_tags (name, slug) VALUES (?, ?)',
                    [$slug, $slug]
                );
                $tagId = (int) Database::lastInsertId();
            } else {
                $tagId = (int) $tag['id'];
            }
            Database::execute(
                'INSERT IGNORE INTO writeup_tag_map (writeup_id, tag_id) VALUES (?, ?)',
                [$writeupId, $tagId]
            );
            $seen[$slug] = true;
            $n++;
        }
    }

    /**
     * @param list<int> $challengeIds
     */
    public static function syncChallenges(int $writeupId, array $challengeIds): void
    {
        Database::execute('DELETE FROM writeup_challenges WHERE writeup_id = ?', [$writeupId]);
        foreach ($challengeIds as $cid) {
            $cid = (int) $cid;
            if ($cid <= 0) {
                continue;
            }
            Database::execute(
                'INSERT IGNORE INTO writeup_challenges (writeup_id, challenge_id) VALUES (?, ?)',
                [$writeupId, $cid]
            );
        }
    }

    public static function syncEvidence(int $writeupId): void
    {
        $w = self::find($writeupId);
        if ($w === null || ($w['status'] ?? '') !== 'published') {
            return;
        }
        $skills = Database::fetchAll(
            'SELECT skill_id FROM writeup_skills WHERE writeup_id = ?',
            [$writeupId]
        );
        $ids = array_map(static fn(array $r): int => (int) $r['skill_id'], $skills);
        if ($ids === []) {
            return;
        }
        // Strength by difficulty — shallow articles are not strong evidence
        $strength = match ((string) $w['difficulty']) {
            'expert' => 4,
            'advanced' => 3,
            'intermediate' => 3,
            default => 2,
        };
        SkillEvidenceService::recordWriteupEvidence(
            (int) $w['user_id'],
            $writeupId,
            (string) $w['title'],
            $ids
        );
        // Soft-adjust strength for this writeup's evidence
        Database::execute(
            "UPDATE skill_evidence SET strength = ?
             WHERE source_type = 'writeup' AND source_id = ? AND user_id = ?",
            [$strength, $writeupId, (int) $w['user_id']]
        );
        foreach ($ids as $sid) {
            SkillProgressService::recalculateUserSkill((int) $w['user_id'], $sid);
        }
    }

    /**
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function list(array $filters, int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset = ($page - 1) * $perPage;
        $viewerId = Auth::id();

        $where = ["w.deleted_at IS NULL", "w.status = 'published'"];
        $params = [];
        if ($viewerId === null) {
            $where[] = "w.visibility = 'public'";
        } else {
            $where[] = "(w.visibility = 'public' OR w.visibility = 'community' OR w.user_id = ?)";
            $params[] = $viewerId;
        }

        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = (string) $filters['category'];
        }
        if (!empty($filters['difficulty']) && in_array($filters['difficulty'], ['beginner', 'intermediate', 'advanced', 'expert'], true)) {
            $where[] = 'w.difficulty = ?';
            $params[] = $filters['difficulty'];
        }
        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM writeup_skills ws
                INNER JOIN skills s ON s.id = ws.skill_id
                WHERE ws.writeup_id = w.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['tag'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM writeup_tag_map wtm
                INNER JOIN writeup_tags t ON t.id = wtm.tag_id
                WHERE wtm.writeup_id = w.id AND t.slug = ?
            )';
            $params[] = (string) $filters['tag'];
        }
        if (!empty($filters['featured'])) {
            $where[] = 'w.featured = 1';
        }
        if (!empty($filters['search'])) {
            $q = trim((string) $filters['search']);
            $where[] = '(MATCH(w.title, w.short_description, w.content) AGAINST (? IN NATURAL LANGUAGE MODE) OR w.title LIKE ?)';
            $params[] = $q;
            $params[] = '%' . $q . '%';
        }

        $orderMap = [
            'recent' => 'w.published_at DESC, w.id DESC',
            'popular' => 'w.view_count DESC, w.published_at DESC',
            'helpful' => 'w.helpful_count DESC, w.published_at DESC',
            'featured' => 'w.featured DESC, w.published_at DESC',
        ];
        $order = $orderMap[(string) ($filters['sort'] ?? 'recent')] ?? $orderMap['recent'];
        $whereSql = implode(' AND ', $where);

        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c
             FROM writeups w
             LEFT JOIN writeup_categories c ON c.id = w.category_id
             WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $items = Database::fetchAll(
            "SELECT w.id, w.title, w.slug, w.short_description, w.difficulty, w.reading_time,
                    w.view_count, w.helpful_count, w.featured, w.published_at, w.user_id,
                    u.username, c.name AS category_name, c.slug AS category_slug
             FROM writeups w
             INNER JOIN users u ON u.id = w.user_id
             LEFT JOIN writeup_categories c ON c.id = w.category_id
             WHERE {$whereSql}
             ORDER BY {$order}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'page' => $page];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(string $username, string $slug, ?int $viewerId): array
    {
        $user = User::findByUsername($username);
        if ($user === null) {
            throw new InvalidArgumentException('Writeup not found.', 404);
        }
        $writeup = self::findByUserSlug($user->id, $slug);
        if ($writeup === null
            || !ContentVisibilityService::canView(
                (string) $writeup['visibility'],
                (int) $writeup['user_id'],
                $viewerId,
                (string) $writeup['status']
            )
        ) {
            throw new InvalidArgumentException('Writeup not found.', 404);
        }

        if ($viewerId !== (int) $writeup['user_id']) {
            Database::execute('UPDATE writeups SET view_count = view_count + 1 WHERE id = ?', [(int) $writeup['id']]);
        }

        $rendered = ContentFormatter::renderWithToc((string) $writeup['content']);
        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug FROM writeup_skills ws
             INNER JOIN skills s ON s.id = ws.skill_id WHERE ws.writeup_id = ?',
            [(int) $writeup['id']]
        );
        $tags = Database::fetchAll(
            'SELECT t.name, t.slug FROM writeup_tag_map m
             INNER JOIN writeup_tags t ON t.id = m.tag_id WHERE m.writeup_id = ?',
            [(int) $writeup['id']]
        );
        $challenges = Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, cat.name AS category_name
             FROM writeup_challenges wc
             INNER JOIN challenges c ON c.id = wc.challenge_id AND c.status = 'published'
             LEFT JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE wc.writeup_id = ?",
            [(int) $writeup['id']]
        );
        $projects = Database::fetchAll(
            "SELECT p.id, p.title, p.slug, u.username
             FROM project_writeups pw
             INNER JOIN projects p ON p.id = pw.project_id AND p.publish_status = 'published'
             INNER JOIN users u ON u.id = p.user_id
             WHERE pw.writeup_id = ?",
            [(int) $writeup['id']]
        );
        $category = $writeup['category_id']
            ? Database::fetch('SELECT * FROM writeup_categories WHERE id = ?', [(int) $writeup['category_id']])
            : null;

        return [
            'writeup' => $writeup,
            'author' => $user,
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
            'skills' => $skills,
            'tags' => $tags,
            'challenges' => $challenges,
            'projects' => $projects,
            'category' => $category,
            'is_owner' => $viewerId === (int) $writeup['user_id'],
            'suggestions' => self::qualitySuggestions($writeup, $skills, $challenges, $projects),
        ];
    }

    /**
     * @param array<string, mixed> $writeup
     * @param list<array<string, mixed>> $skills
     * @param list<array<string, mixed>> $challenges
     * @param list<array<string, mixed>> $projects
     * @return array{done:list<string>,missing:list<string>}
     */
    public static function qualitySuggestions(array $writeup, array $skills, array $challenges, array $projects): array
    {
        $content = (string) $writeup['content'];
        $checks = [
            'Clear introduction / summary' => mb_strlen((string) ($writeup['short_description'] ?? '')) > 20,
            'Structured headings' => (bool) preg_match('/^## /m', $content),
            'Technical code examples' => str_contains($content, '```'),
            'Related skill' => $skills !== [],
            'Lessons learned' => (bool) preg_match('/lessons learned/i', $content),
            'References' => (bool) (preg_match('/https?:\/\//', $content) || preg_match('/^## .*[Rr]eference/m', $content)),
            'Linked challenge or project' => $challenges !== [] || $projects !== [],
        ];
        $done = [];
        $missing = [];
        foreach ($checks as $label => $ok) {
            if ($ok) {
                $done[] = $label;
            } else {
                $missing[] = $label;
            }
        }
        return ['done' => $done, 'missing' => $missing];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function categories(): array
    {
        return Database::fetchAll(
            'SELECT * FROM writeup_categories WHERE is_active = 1 ORDER BY display_order, name'
        );
    }

    public static function setFeatured(int $writeupId, int $actorId, bool $featured): void
    {
        if (!ContentVisibilityService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        Database::execute('UPDATE writeups SET featured = ? WHERE id = ?', [$featured ? 1 : 0, $writeupId]);
        ActivityLogService::log($actorId, 'writeup_featured', 'writeup', $writeupId, [
            'featured' => $featured,
        ]);
    }
}
