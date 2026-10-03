<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class ProjectService
{
    private const TYPES = [
        'security_tool', 'web_security', 'network_security', 'digital_forensics',
        'malware_analysis', 'reverse_engineering', 'ctf', 'automation', 'research',
        'academic', 'open_source', 'home_lab', 'other',
    ];

    /**
     * @return array<string, mixed>|null
     */
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM projects WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findByUserSlug(int $userId, string $slug): ?array
    {
        return Database::fetch(
            'SELECT * FROM projects WHERE user_id = ? AND slug = ? LIMIT 1',
            [$userId, $slug]
        );
    }

    public static function requireOwner(array $project, int $userId): void
    {
        if ((int) $project['user_id'] !== $userId) {
            throw new RuntimeException('Forbidden', 403);
        }
    }

    public static function slugify(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'project';
    }

    public static function uniqueSlug(int $userId, string $base, ?int $excludeId = null): string
    {
        $slug = self::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM projects WHERE user_id = ? AND slug = ?';
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
     * @param list<string> $technologies
     * @param list<array{skill_id:int,importance?:string}> $skills
     */
    public static function create(int $userId, array $data, array $technologies = [], array $skills = []): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '' || mb_strlen($title) > 200) {
            throw new InvalidArgumentException('Invalid title.');
        }

        $type = (string) ($data['project_type'] ?? 'other');
        if (!in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException('Invalid project type.');
        }

        foreach (['repository_url', 'demo_url', 'documentation_url'] as $uk) {
            $u = trim((string) ($data[$uk] ?? ''));
            if ($u !== '' && !PortfolioVisibilityService::isSafeUrl($u)) {
                throw new InvalidArgumentException('Invalid URL: ' . $uk);
            }
            $data[$uk] = $u !== '' ? $u : null;
        }

        $slug = self::uniqueSlug($userId, (string) ($data['slug'] ?? $title));
        $publish = in_array($data['publish_status'] ?? 'draft', ['draft', 'published'], true)
            ? (string) $data['publish_status']
            : 'draft';
        $visibility = in_array($data['visibility'] ?? 'public', ['public', 'community', 'private'], true)
            ? (string) $data['visibility']
            : 'public';
        $status = in_array($data['status'] ?? 'planning', ['planning', 'in_progress', 'completed', 'archived'], true)
            ? (string) $data['status']
            : 'planning';

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO projects
                 (user_id, title, slug, short_description, description, project_type, status,
                  publish_status, visibility, repository_url, demo_url, documentation_url,
                  start_date, end_date, featured, display_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)',
                [
                    $userId,
                    $title,
                    $slug,
                    mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500),
                    (string) ($data['description'] ?? ''),
                    $type,
                    $status,
                    $publish,
                    $visibility,
                    $data['repository_url'],
                    $data['demo_url'],
                    $data['documentation_url'],
                    $data['start_date'] ?: null,
                    $data['end_date'] ?: null,
                    (int) ($data['display_order'] ?? 0),
                ]
            );
            $id = (int) Database::lastInsertId();
            self::syncTechnologies($id, $technologies);
            self::syncSkills($id, $skills);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($userId, 'project_created', 'project', $id);
        if ($publish === 'published') {
            ActivityLogService::log($userId, 'project_published', 'project', $id);
            self::syncEvidence($id);
        }

        $project = self::find($id);
        if ($project === null) {
            throw new RuntimeException('Project missing after create.');
        }
        return $project;
    }

    /**
     * @param array<string, mixed> $data
     * @param list<string> $technologies
     * @param list<array{skill_id:int,importance?:string}> $skills
     */
    public static function update(array $project, int $actorId, array $data, array $technologies = [], array $skills = []): array
    {
        self::requireOwner($project, $actorId);

        $title = trim((string) ($data['title'] ?? $project['title']));
        foreach (['repository_url', 'demo_url', 'documentation_url'] as $uk) {
            $u = trim((string) ($data[$uk] ?? $project[$uk] ?? ''));
            if ($u !== '' && !PortfolioVisibilityService::isSafeUrl($u)) {
                throw new InvalidArgumentException('Invalid URL: ' . $uk);
            }
            $data[$uk] = $u !== '' ? $u : null;
        }

        $type = (string) ($data['project_type'] ?? $project['project_type']);
        if (!in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException('Invalid project type.');
        }

        $publish = (string) ($data['publish_status'] ?? $project['publish_status']);
        if (!in_array($publish, ['draft', 'published', 'archived'], true)) {
            $publish = (string) $project['publish_status'];
        }
        $visibility = (string) ($data['visibility'] ?? $project['visibility']);
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            $visibility = (string) $project['visibility'];
        }
        $status = (string) ($data['status'] ?? $project['status']);
        if (!in_array($status, ['planning', 'in_progress', 'completed', 'archived'], true)) {
            $status = (string) $project['status'];
        }

        $slug = !empty($data['slug'])
            ? self::uniqueSlug($actorId, (string) $data['slug'], (int) $project['id'])
            : (string) $project['slug'];

        Database::beginTransaction();
        try {
            Database::execute(
                'UPDATE projects SET
                   title = ?, slug = ?, short_description = ?, description = ?,
                   project_type = ?, status = ?, publish_status = ?, visibility = ?,
                   repository_url = ?, demo_url = ?, documentation_url = ?,
                   start_date = ?, end_date = ?, display_order = ?
                 WHERE id = ? AND user_id = ?',
                [
                    $title,
                    $slug,
                    mb_substr(trim((string) ($data['short_description'] ?? '')), 0, 500),
                    (string) ($data['description'] ?? ''),
                    $type,
                    $status,
                    $publish,
                    $visibility,
                    $data['repository_url'],
                    $data['demo_url'],
                    $data['documentation_url'],
                    $data['start_date'] ?: null,
                    $data['end_date'] ?: null,
                    (int) ($data['display_order'] ?? 0),
                    (int) $project['id'],
                    $actorId,
                ]
            );
            self::syncTechnologies((int) $project['id'], $technologies);
            self::syncSkills((int) $project['id'], $skills);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'project_updated', 'project', (int) $project['id']);
        if ($publish === 'published' && ($project['publish_status'] ?? '') !== 'published') {
            ActivityLogService::log($actorId, 'project_published', 'project', (int) $project['id']);
        }
        if ($publish === 'published') {
            self::syncEvidence((int) $project['id']);
        }

        $updated = self::find((int) $project['id']);
        if ($updated === null) {
            throw new RuntimeException('Project missing after update.');
        }
        return $updated;
    }

    public static function setFeatured(array $project, int $actorId, bool $featured): void
    {
        self::requireOwner($project, $actorId);
        if ($featured) {
            $limit = (int) config('portfolio.max_featured_projects', 3);
            $count = Database::fetch(
                'SELECT COUNT(*) AS c FROM projects
                 WHERE user_id = ? AND featured = 1 AND id <> ?',
                [$actorId, (int) $project['id']]
            );
            if ((int) ($count['c'] ?? 0) >= $limit) {
                throw new InvalidArgumentException(
                    'You can feature at most ' . $limit . ' projects. Unfeature another first.'
                );
            }
            if (($project['publish_status'] ?? '') !== 'published') {
                throw new InvalidArgumentException('Only published projects can be featured.');
            }
        }
        Database::execute(
            'UPDATE projects SET featured = ? WHERE id = ? AND user_id = ?',
            [$featured ? 1 : 0, (int) $project['id'], $actorId]
        );
        ActivityLogService::log($actorId, $featured ? 'project_featured' : 'project_updated', 'project', (int) $project['id']);
    }

    public static function setPublishStatus(array $project, int $actorId, string $status): void
    {
        self::requireOwner($project, $actorId);
        if (!in_array($status, ['draft', 'published', 'archived'], true)) {
            throw new InvalidArgumentException('Invalid status.');
        }
        Database::execute(
            'UPDATE projects SET publish_status = ? WHERE id = ? AND user_id = ?',
            [$status, (int) $project['id'], $actorId]
        );
        if ($status === 'published') {
            ActivityLogService::log($actorId, 'project_published', 'project', (int) $project['id']);
            self::syncEvidence((int) $project['id']);
        }
    }

    /**
     * @param list<string> $technologies
     */
    public static function syncTechnologies(int $projectId, array $technologies): void
    {
        Database::execute('DELETE FROM project_technologies WHERE project_id = ?', [$projectId]);
        $seen = [];
        foreach ($technologies as $tech) {
            $tech = trim((string) $tech);
            $tech = preg_replace('/\s+/', ' ', $tech) ?? $tech;
            if ($tech === '' || mb_strlen($tech) > 80 || isset($seen[mb_strtolower($tech)])) {
                continue;
            }
            $seen[mb_strtolower($tech)] = true;
            Database::execute(
                'INSERT INTO project_technologies (project_id, technology) VALUES (?, ?)',
                [$projectId, $tech]
            );
        }
    }

    /**
     * @param list<array{skill_id:int,importance?:string}> $skills
     */
    public static function syncSkills(int $projectId, array $skills): void
    {
        Database::execute('DELETE FROM project_skills WHERE project_id = ?', [$projectId]);
        $seen = [];
        foreach ($skills as $row) {
            $sid = (int) ($row['skill_id'] ?? 0);
            if ($sid <= 0 || isset($seen[$sid])) {
                continue;
            }
            $imp = (string) ($row['importance'] ?? 'secondary');
            if (!in_array($imp, ['primary', 'secondary', 'supporting'], true)) {
                $imp = 'secondary';
            }
            $weight = match ($imp) {
                'primary' => 1.0,
                'supporting' => 0.4,
                default => 0.7,
            };
            Database::execute(
                'INSERT INTO project_skills (project_id, skill_id, importance, weight) VALUES (?, ?, ?, ?)',
                [$projectId, $sid, $imp, $weight]
            );
            $seen[$sid] = true;
        }
    }

    public static function syncEvidence(int $projectId): void
    {
        $project = self::find($projectId);
        if ($project === null || ($project['publish_status'] ?? '') !== 'published') {
            return;
        }
        $skills = Database::fetchAll(
            'SELECT skill_id FROM project_skills WHERE project_id = ?',
            [$projectId]
        );
        $ids = array_map(static fn(array $r): int => (int) $r['skill_id'], $skills);
        if ($ids === []) {
            return;
        }
        SkillEvidenceService::recordProjectEvidence(
            (int) $project['user_id'],
            $projectId,
            (string) $project['title'],
            $ids
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featuredForUser(int $userId, ?int $viewerId, int $limit = 3): array
    {
        $limit = max(1, min(6, $limit));
        $rows = Database::fetchAll(
            "SELECT * FROM projects
             WHERE user_id = ? AND featured = 1 AND publish_status = 'published'
             ORDER BY display_order ASC, updated_at DESC
             LIMIT {$limit}",
            [$userId]
        );
        return self::enrichVisible($rows, $viewerId);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function publishedForUser(int $userId, ?int $viewerId): array
    {
        $rows = Database::fetchAll(
            "SELECT * FROM projects
             WHERE user_id = ? AND publish_status = 'published'
             ORDER BY featured DESC, display_order ASC, updated_at DESC",
            [$userId]
        );
        return self::enrichVisible($rows, $viewerId);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private static function enrichVisible(array $rows, ?int $viewerId): array
    {
        $out = [];
        foreach ($rows as $row) {
            if (!PortfolioVisibilityService::canViewProject($row, $viewerId)) {
                continue;
            }
            $row['technologies'] = self::technologies((int) $row['id']);
            $row['skills'] = self::skills((int) $row['id']);
            $row['verified'] = self::isVerified((int) $row['id']);
            $out[] = $row;
        }
        return $out;
    }

    /**
     * @return list<string>
     */
    public static function technologies(int $projectId): array
    {
        $rows = Database::fetchAll(
            'SELECT technology FROM project_technologies WHERE project_id = ? ORDER BY technology',
            [$projectId]
        );
        return array_map(static fn(array $r): string => (string) $r['technology'], $rows);
    }

    /**
     * @return list<array{id:int,name:string,slug:string,importance:string}>
     */
    public static function skills(int $projectId): array
    {
        return Database::fetchAll(
            'SELECT s.id, s.name, s.slug, ps.importance
             FROM project_skills ps
             INNER JOIN skills s ON s.id = ps.skill_id
             WHERE ps.project_id = ?
             ORDER BY FIELD(ps.importance, \'primary\',\'secondary\',\'supporting\'), s.name',
            [$projectId]
        );
    }

    public static function isVerified(int $projectId): bool
    {
        $row = Database::fetch(
            "SELECT id FROM project_verifications
             WHERE project_id = ? AND status = 'verified'
             ORDER BY verified_at DESC LIMIT 1",
            [$projectId]
        );
        return $row !== null;
    }

    /**
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function discover(array $filters, int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset = ($page - 1) * $perPage;
        $viewerId = Auth::id();

        $where = ["p.publish_status = 'published'"];
        $params = [];

        if ($viewerId === null) {
            $where[] = "p.visibility = 'public'";
        } else {
            $where[] = "(p.visibility = 'public' OR p.visibility = 'community' OR p.user_id = ?)";
            $params[] = $viewerId;
        }

        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM project_skills ps
                INNER JOIN skills s ON s.id = ps.skill_id
                WHERE ps.project_id = p.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['type']) && in_array($filters['type'], self::TYPES, true)) {
            $where[] = 'p.project_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['technology'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM project_technologies pt
                WHERE pt.project_id = p.id AND pt.technology = ?
            )';
            $params[] = (string) $filters['technology'];
        }
        if (!empty($filters['verified'])) {
            $where[] = "EXISTS (
                SELECT 1 FROM project_verifications pv
                WHERE pv.project_id = p.id AND pv.status = 'verified'
            )";
        }
        if (!empty($filters['search'])) {
            $q = '%' . (string) $filters['search'] . '%';
            $where[] = '(p.title LIKE ? OR p.short_description LIKE ? OR p.description LIKE ?)';
            array_push($params, $q, $q, $q);
        }

        $orderMap = [
            'recent' => 'p.updated_at DESC',
            'featured' => 'p.featured DESC, p.updated_at DESC',
            'popular' => 'p.view_count DESC, p.updated_at DESC',
        ];
        $order = $orderMap[(string) ($filters['sort'] ?? 'recent')] ?? $orderMap['recent'];
        $whereSql = implode(' AND ', $where);

        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM projects p WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $items = Database::fetchAll(
            "SELECT p.*, u.username
             FROM projects p
             INNER JOIN users u ON u.id = p.user_id AND u.status = 'active'
             WHERE {$whereSql}
             ORDER BY {$order}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        foreach ($items as &$item) {
            $item['technologies'] = self::technologies((int) $item['id']);
            $item['skills'] = self::skills((int) $item['id']);
            $item['verified'] = self::isVerified((int) $item['id']);
        }
        unset($item);

        return ['items' => $items, 'total' => $total, 'page' => $page];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(string $username, string $slug, ?int $viewerId): array
    {
        $user = \App\Models\User::findByUsername($username);
        if ($user === null) {
            throw new InvalidArgumentException('Project not found.', 404);
        }
        $project = self::findByUserSlug($user->id, $slug);
        if ($project === null || !PortfolioVisibilityService::canViewProject($project, $viewerId)) {
            throw new InvalidArgumentException('Project not found.', 404);
        }

        if ($viewerId !== $user->id) {
            Database::execute('UPDATE projects SET view_count = view_count + 1 WHERE id = ?', [(int) $project['id']]);
            PortfolioAnalyticsService::increment($user->id, 'project_view');
        }

        $verification = Database::fetch(
            "SELECT pv.*, u.username AS verifier_username
             FROM project_verifications pv
             LEFT JOIN users u ON u.id = pv.verified_by
             WHERE pv.project_id = ?
             ORDER BY FIELD(pv.status,'verified','pending','rejected'), pv.id DESC
             LIMIT 1",
            [(int) $project['id']]
        );

        $challenges = Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, pc.relationship_type
             FROM project_challenges pc
             INNER JOIN challenges c ON c.id = pc.challenge_id AND c.status = 'published'
             WHERE pc.project_id = ?",
            [(int) $project['id']]
        );

        $images = Database::fetchAll(
            'SELECT id, original_name, caption, display_order FROM project_images
             WHERE project_id = ? ORDER BY display_order, id',
            [(int) $project['id']]
        );

        $reactions = Database::fetchAll(
            'SELECT reaction_type, COUNT(*) AS cnt FROM project_reactions
             WHERE project_id = ? GROUP BY reaction_type',
            [(int) $project['id']]
        );

        return [
            'user' => $user,
            'project' => $project,
            'technologies' => self::technologies((int) $project['id']),
            'skills' => self::skills((int) $project['id']),
            'images' => $images,
            'verification' => $verification,
            'challenges' => $challenges,
            'reactions' => $reactions,
            'is_owner' => $viewerId === $user->id,
        ];
    }

    public static function react(int $projectId, int $userId, string $type): void
    {
        if (!in_array($type, ['helpful', 'interesting', 'impressive'], true)) {
            throw new InvalidArgumentException('Invalid reaction.');
        }
        $project = self::find($projectId);
        if ($project === null || !PortfolioVisibilityService::canViewProject($project, $userId)) {
            throw new InvalidArgumentException('Project not found.', 404);
        }
        Database::execute(
            'INSERT IGNORE INTO project_reactions (project_id, user_id, reaction_type) VALUES (?, ?, ?)',
            [$projectId, $userId, $type]
        );
    }
}
