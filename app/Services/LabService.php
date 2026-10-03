<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class LabService
{
    public static function canManage(): bool
    {
        $roles = config('labs.manager_roles', ['instructor', 'moderator', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function canManageInfra(): bool
    {
        $roles = config('labs.infra_roles', ['admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function slugify(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'lab';
    }

    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = self::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM labs WHERE slug = ?';
            $params = [$candidate];
            if ($excludeId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $excludeId;
            }
            $sql .= ' LIMIT 1';
            if (Database::fetch($sql, $params) === null) {
                return $candidate;
            }
            $candidate = $slug . '-' . $i;
            $i++;
        }
    }

    /** @return list<array<string, mixed>> */
    public static function categories(): array
    {
        return Database::fetchAll(
            'SELECT * FROM lab_categories WHERE is_active = 1 ORDER BY display_order, name'
        );
    }

    /** @return list<array<string, mixed>> */
    public static function templates(): array
    {
        return Database::fetchAll(
            'SELECT id, name, slug, description, runtime_type FROM lab_templates WHERE is_active = 1 ORDER BY name'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM labs WHERE id = ? LIMIT 1', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM labs WHERE slug = ? LIMIT 1', [$slug]);
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function list(array $filters, int $page = 1, int $perPage = 12, ?int $viewerId = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $where = ["l.status = 'published'"];
        $params = [];

        if ($viewerId === null) {
            $where[] = "l.visibility = 'public'";
        } else {
            $where[] = "(l.visibility IN ('public','community') OR l.author_id = ?)";
            $params[] = $viewerId;
        }

        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = (string) $filters['category'];
        }
        if (!empty($filters['difficulty']) && in_array($filters['difficulty'], ['beginner','intermediate','advanced','expert'], true)) {
            $where[] = 'l.difficulty = ?';
            $params[] = $filters['difficulty'];
        }
        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM lab_skills ls INNER JOIN skills s ON s.id = ls.skill_id
                WHERE ls.lab_id = l.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(l.title LIKE ? OR l.short_description LIKE ?)';
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], (string) $filters['search']) . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['featured'])) {
            $where[] = 'l.featured = 1';
        }

        $sort = (string) ($filters['sort'] ?? 'recent');
        $order = match ($sort) {
            'popular' => 'l.start_count DESC, l.published_at DESC',
            'completed' => 'l.completion_count DESC, l.published_at DESC',
            'difficulty' => "FIELD(l.difficulty,'beginner','intermediate','advanced','expert'), l.title",
            default => 'l.published_at DESC, l.id DESC',
        };

        $whereSql = implode(' AND ', $where);
        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM labs l
             LEFT JOIN lab_categories c ON c.id = l.category_id
             WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT l.id, l.title, l.slug, l.short_description, l.difficulty, l.estimated_minutes,
                    l.featured, l.environment_type, l.completion_count, l.start_count,
                    c.name AS category_name, c.slug AS category_slug
             FROM labs l
             LEFT JOIN lab_categories c ON c.id = l.category_id
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
    public static function detail(string $slug, ?int $viewerId): array
    {
        $lab = self::findBySlug($slug);
        if ($lab === null) {
            throw new InvalidArgumentException('Lab not found.', 404);
        }

        $isStaff = self::canManage();
        $isAuthor = $viewerId !== null && (int) $lab['author_id'] === $viewerId;
        if (($lab['status'] ?? '') !== 'published' && !$isStaff && !$isAuthor) {
            throw new InvalidArgumentException('Lab not found.', 404);
        }
        if (($lab['status'] ?? '') === 'published'
            && !ContentVisibilityService::canView((string) $lab['visibility'], (int) $lab['author_id'], $viewerId, 'published')
            && !$isStaff
        ) {
            throw new InvalidArgumentException('Lab not available.', 403);
        }

        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug, ls.weight FROM lab_skills ls
             INNER JOIN skills s ON s.id = ls.skill_id WHERE ls.lab_id = ? ORDER BY s.name',
            [(int) $lab['id']]
        );
        $prereqs = Database::fetchAll(
            'SELECT s.id, s.name, s.slug, lp.minimum_level FROM lab_prerequisites lp
             INNER JOIN skills s ON s.id = lp.skill_id WHERE lp.lab_id = ?',
            [(int) $lab['id']]
        );
        $tasks = Database::fetchAll(
            'SELECT id, title, slug, task_type, display_order, required, points
             FROM lab_tasks WHERE lab_id = ? ORDER BY display_order, id',
            [(int) $lab['id']]
        );
        $services = Database::fetchAll(
            'SELECT id, name, service_type, protocol, display_port, display_order
             FROM lab_services WHERE lab_id = ? AND is_student_visible = 1 ORDER BY display_order, id',
            [(int) $lab['id']]
        );
        $writeups = Database::fetchAll(
            "SELECT w.title, w.slug, u.username, w.reading_time
             FROM writeup_labs wl
             INNER JOIN writeups w ON w.id = wl.writeup_id AND w.status = 'published' AND w.visibility = 'public' AND w.deleted_at IS NULL
             INNER JOIN users u ON u.id = w.user_id
             WHERE wl.lab_id = ?
             ORDER BY w.published_at DESC LIMIT 6",
            [(int) $lab['id']]
        );

        $activeInstance = null;
        $completion = null;
        if ($viewerId !== null) {
            $activeInstance = Database::fetch(
                "SELECT id, status, provision_state, expires_at, started_at
                 FROM lab_instances
                 WHERE lab_id = ? AND user_id = ?
                   AND status IN ('queued','provisioning','running','paused')
                 ORDER BY id DESC LIMIT 1",
                [(int) $lab['id'], $viewerId]
            );
            $completion = Database::fetch(
                'SELECT * FROM lab_completions WHERE lab_id = ? AND user_id = ? LIMIT 1',
                [(int) $lab['id'], $viewerId]
            );
        }

        $required = 0;
        $optional = 0;
        foreach ($tasks as $t) {
            if ((int) $t['required'] === 1) {
                $required++;
            } else {
                $optional++;
            }
        }

        return [
            'lab' => $lab,
            'skills' => $skills,
            'prerequisites' => $prereqs,
            'tasks' => $tasks,
            'services' => $services,
            'writeups' => $writeups,
            'active_instance' => $activeInstance,
            'completion' => $completion,
            'task_counts' => ['required' => $required, 'optional' => $optional, 'total' => count($tasks)],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function create(int $authorId, array $data, array $skillIds = []): array
    {
        if (!self::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $title = trim((string) ($data['title'] ?? ''));
        $description = (string) ($data['description'] ?? '');
        if ($title === '' || $description === '') {
            throw new InvalidArgumentException('Title and description are required.');
        }
        $slugBase = trim((string) ($data['slug'] ?? ''));
        if ($slugBase === '') {
            $slugBase = $title;
        }
        $slug = self::uniqueSlug($slugBase);
        $lifetime = max(15, min(
            (int) config('labs.max_lifetime_minutes', 180),
            (int) ($data['lifetime_minutes'] ?? config('labs.default_lifetime_minutes', 60))
        ));

        Database::execute(
            'INSERT INTO labs
             (title, slug, short_description, description, learning_objectives, category_id, template_id,
              difficulty, estimated_minutes, status, visibility, author_id, lifetime_minutes,
              cpu_limit, memory_mb, disk_mb, allow_pause, prerequisite_mode, environment_type, max_points)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $title,
                $slug,
                mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500) ?: null,
                $description,
                (string) ($data['learning_objectives'] ?? '') ?: null,
                !empty($data['category_id']) ? (int) $data['category_id'] : null,
                !empty($data['template_id']) ? (int) $data['template_id'] : null,
                in_array($data['difficulty'] ?? '', ['beginner','intermediate','advanced','expert'], true)
                    ? $data['difficulty'] : 'beginner',
                max(5, min(600, (int) ($data['estimated_minutes'] ?? 45))),
                in_array($data['status'] ?? '', ['draft','review','published','archived'], true)
                    ? $data['status'] : 'draft',
                in_array($data['visibility'] ?? '', ['public','community','private'], true)
                    ? $data['visibility'] : 'public',
                $authorId,
                $lifetime,
                (float) (self::canManageInfra() ? ($data['cpu_limit'] ?? 1) : 1),
                (int) (self::canManageInfra() ? ($data['memory_mb'] ?? 512) : 512),
                (int) (self::canManageInfra() ? ($data['disk_mb'] ?? 1024) : 1024),
                !empty($data['allow_pause']) ? 1 : 0,
                ($data['prerequisite_mode'] ?? '') === 'required' ? 'required' : 'recommended',
                mb_substr((string) ($data['environment_type'] ?? 'browser'), 0, 60),
                max(0, min(1000, (int) ($data['max_points'] ?? 100))),
            ]
        );
        $id = (int) Database::lastInsertId();
        self::syncSkills($id, $skillIds);
        if (($data['status'] ?? '') === 'published') {
            Database::execute('UPDATE labs SET published_at = NOW() WHERE id = ? AND published_at IS NULL', [$id]);
        }
        ActivityLogService::log($authorId, 'lab_created', 'lab', $id);
        $row = self::find($id);
        if ($row === null) {
            throw new RuntimeException('Lab missing after create.');
        }
        return $row;
    }

    /**
     * @param array<string, mixed> $lab
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function update(array $lab, int $actorId, array $data, array $skillIds = []): array
    {
        if (!self::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $title = trim((string) ($data['title'] ?? $lab['title']));
        $description = (string) ($data['description'] ?? $lab['description']);
        if ($title === '' || $description === '') {
            throw new InvalidArgumentException('Title and description are required.');
        }
        if (array_key_exists('slug', $data)) {
            $slugBase = trim((string) $data['slug']);
            $slug = $slugBase !== ''
                ? self::uniqueSlug($slugBase, (int) $lab['id'])
                : (string) $lab['slug'];
        } else {
            $slug = (string) $lab['slug'];
        }
        $status = (string) ($data['status'] ?? $lab['status']);
        if (!in_array($status, ['draft','review','published','archived'], true)) {
            $status = (string) $lab['status'];
        }

        $cpu = self::canManageInfra() ? (float) ($data['cpu_limit'] ?? $lab['cpu_limit']) : (float) $lab['cpu_limit'];
        $mem = self::canManageInfra() ? (int) ($data['memory_mb'] ?? $lab['memory_mb']) : (int) $lab['memory_mb'];
        $disk = self::canManageInfra() ? (int) ($data['disk_mb'] ?? $lab['disk_mb']) : (int) $lab['disk_mb'];

        Database::execute(
            'UPDATE labs SET
               title=?, slug=?, short_description=?, description=?, learning_objectives=?,
               category_id=?, template_id=?, difficulty=?, estimated_minutes=?, status=?, visibility=?,
               lifetime_minutes=?, cpu_limit=?, memory_mb=?, disk_mb=?, allow_pause=?,
               prerequisite_mode=?, environment_type=?, max_points=?,
               published_at = CASE WHEN ? = \'published\' AND published_at IS NULL THEN NOW() ELSE published_at END
             WHERE id = ?',
            [
                $title,
                $slug,
                mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500) ?: null,
                $description,
                (string) ($data['learning_objectives'] ?? '') ?: null,
                !empty($data['category_id']) ? (int) $data['category_id'] : null,
                !empty($data['template_id']) ? (int) $data['template_id'] : null,
                in_array($data['difficulty'] ?? $lab['difficulty'], ['beginner','intermediate','advanced','expert'], true)
                    ? ($data['difficulty'] ?? $lab['difficulty']) : $lab['difficulty'],
                max(5, min(600, (int) ($data['estimated_minutes'] ?? $lab['estimated_minutes']))),
                $status,
                in_array($data['visibility'] ?? $lab['visibility'], ['public','community','private'], true)
                    ? ($data['visibility'] ?? $lab['visibility']) : $lab['visibility'],
                max(15, min((int) config('labs.max_lifetime_minutes', 180), (int) ($data['lifetime_minutes'] ?? $lab['lifetime_minutes']))),
                $cpu,
                $mem,
                $disk,
                !empty($data['allow_pause']) ? 1 : 0,
                ($data['prerequisite_mode'] ?? $lab['prerequisite_mode']) === 'required' ? 'required' : 'recommended',
                mb_substr((string) ($data['environment_type'] ?? $lab['environment_type']), 0, 60),
                max(0, min(1000, (int) ($data['max_points'] ?? $lab['max_points']))),
                $status,
                (int) $lab['id'],
            ]
        );
        self::syncSkills((int) $lab['id'], $skillIds);
        ActivityLogService::log($actorId, 'lab_updated', 'lab', (int) $lab['id']);
        if ($status === 'published' && ($lab['status'] ?? '') !== 'published') {
            ActivityLogService::log($actorId, 'lab_published', 'lab', (int) $lab['id']);
        }
        $row = self::find((int) $lab['id']);
        if ($row === null) {
            throw new RuntimeException('Lab missing after update.');
        }
        return $row;
    }

    public static function setFeatured(int $labId, int $actorId, bool $featured): void
    {
        if (!self::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        Database::execute('UPDATE labs SET featured = ? WHERE id = ?', [$featured ? 1 : 0, $labId]);
        ActivityLogService::log($actorId, 'lab_featured', 'lab', $labId, ['featured' => $featured]);
    }

    /** @param list<int> $skillIds */
    public static function syncSkills(int $labId, array $skillIds): void
    {
        Database::execute('DELETE FROM lab_skills WHERE lab_id = ?', [$labId]);
        foreach ($skillIds as $skillId) {
            $skillId = (int) $skillId;
            if ($skillId <= 0) {
                continue;
            }
            Database::execute(
                'INSERT IGNORE INTO lab_skills (lab_id, skill_id, weight) VALUES (?, ?, 1.00)',
                [$labId, $skillId]
            );
        }
    }

    /** @return list<array<string, mixed>> */
    public static function adminList(int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        return Database::fetchAll(
            "SELECT l.id, l.title, l.slug, l.status, l.featured, l.difficulty, l.updated_at,
                    u.username, l.start_count, l.completion_count
             FROM labs l
             INNER JOIN users u ON u.id = l.author_id
             ORDER BY l.updated_at DESC
             LIMIT {$limit}"
        );
    }
}
