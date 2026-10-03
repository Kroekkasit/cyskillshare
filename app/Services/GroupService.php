<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class GroupService
{
    public static function canManagePlatform(): bool
    {
        $roles = config('collaboration.manager_roles', ['instructor', 'moderator', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'group';
    }

    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = self::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM collab_groups WHERE slug = ?';
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

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM collab_groups WHERE id = ? LIMIT 1', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM collab_groups WHERE slug = ? LIMIT 1', [$slug]);
    }

    public static function memberRole(int $groupId, int $userId): ?string
    {
        $row = Database::fetch(
            "SELECT role FROM collab_group_members
             WHERE group_id = ? AND user_id = ? AND status = 'active' LIMIT 1",
            [$groupId, $userId]
        );
        return $row ? (string) $row['role'] : null;
    }

    public static function isMember(int $groupId, int $userId): bool
    {
        return self::memberRole($groupId, $userId) !== null;
    }

    public static function canManageGroup(array $group, int $userId): bool
    {
        if (self::canManagePlatform()) {
            return true;
        }
        $role = self::memberRole((int) $group['id'], $userId);
        return in_array($role, ['owner', 'admin', 'captain', 'co_captain'], true);
    }

    public static function canView(array $group, ?int $viewerId): bool
    {
        if (($group['status'] ?? '') === 'suspended' && !self::canManagePlatform()) {
            return false;
        }
        if (($group['status'] ?? '') === 'archived' && !self::isMember((int) $group['id'], (int) ($viewerId ?? 0)) && !self::canManagePlatform()) {
            return false;
        }
        if ($viewerId !== null && self::isMember((int) $group['id'], $viewerId)) {
            return true;
        }
        return match ($group['visibility'] ?? '') {
            'public' => true,
            'community' => $viewerId !== null,
            'private' => $viewerId !== null && (
                self::isMember((int) $group['id'], $viewerId) || self::canManagePlatform()
            ),
            default => false,
        };
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function list(array $filters, int $page = 1, int $perPage = 12, ?int $viewerId = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(40, $perPage));
        $where = ["g.status = 'active'"];
        $params = [];

        if ($viewerId === null) {
            $where[] = "g.visibility = 'public'";
        } else {
            $where[] = "(g.visibility IN ('public','community') OR EXISTS (
                SELECT 1 FROM collab_group_members m
                WHERE m.group_id = g.id AND m.user_id = ? AND m.status = 'active'
            ))";
            $params[] = $viewerId;
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['study','ctf','project','course','general'], true)) {
            $where[] = 'g.group_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM collab_group_skills gs
                INNER JOIN skills s ON s.id = gs.skill_id
                WHERE gs.group_id = g.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['search'])) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], (string) $filters['search']) . '%';
            $where[] = '(g.name LIKE ? OR g.description LIKE ?)';
            $params[] = $like;
            $params[] = $like;
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM collab_groups g WHERE {$whereSql}",
            $params
        )['c'] ?? 0);
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT g.*, u.username AS owner_username,
                    (SELECT COUNT(*) FROM collab_group_members m
                     WHERE m.group_id = g.id AND m.status = 'active') AS member_count
             FROM collab_groups g
             INNER JOIN users u ON u.id = g.owner_id
             WHERE {$whereSql}
             ORDER BY g.featured DESC, g.updated_at DESC
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
        $group = self::findBySlug($slug);
        if ($group === null || !self::canView($group, $viewerId)) {
            throw new InvalidArgumentException('Group not found.', 404);
        }

        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug FROM collab_group_skills gs
             INNER JOIN skills s ON s.id = gs.skill_id WHERE gs.group_id = ? ORDER BY s.name',
            [(int) $group['id']]
        );
        $members = Database::fetchAll(
            "SELECT m.role, m.joined_at, u.id AS user_id, u.username, u.full_name
             FROM collab_group_members m
             INNER JOIN users u ON u.id = m.user_id
             WHERE m.group_id = ? AND m.status = 'active'
             ORDER BY FIELD(m.role,'owner','captain','co_captain','admin','moderator','mentor','member'), u.username",
            [(int) $group['id']]
        );
        $goals = Database::fetchAll(
            "SELECT * FROM collab_group_goals WHERE group_id = ? ORDER BY FIELD(status,'active','completed','cancelled'), id DESC",
            [(int) $group['id']]
        );
        $activities = Database::fetchAll(
            "SELECT a.*, u.username AS creator_username
             FROM collab_group_activities a
             INNER JOIN users u ON u.id = a.created_by
             WHERE a.group_id = ?
             ORDER BY COALESCE(a.scheduled_at, a.created_at) DESC
             LIMIT 20",
            [(int) $group['id']]
        );
        $resources = Database::fetchAll(
            'SELECT r.*, u.username AS creator_username FROM collab_group_resources r
             INNER JOIN users u ON u.id = r.created_by
             WHERE r.group_id = ? ORDER BY r.created_at DESC LIMIT 30',
            [(int) $group['id']]
        );
        $roles = Database::fetchAll(
            'SELECT * FROM collab_group_roles WHERE group_id = ? ORDER BY id',
            [(int) $group['id']]
        );

        $viewerRole = $viewerId ? self::memberRole((int) $group['id'], $viewerId) : null;
        $pendingJoin = null;
        if ($viewerId && !$viewerRole) {
            $pendingJoin = Database::fetch(
                "SELECT id FROM collab_group_join_requests
                 WHERE group_id = ? AND user_id = ? AND status = 'pending' LIMIT 1",
                [(int) $group['id'], $viewerId]
            );
        }

        $stats = self::aggregateStats($group, $members);

        return [
            'group' => $group,
            'skills' => $skills,
            'members' => $members,
            'goals' => $goals,
            'activities' => $activities,
            'resources' => $resources,
            'roles' => $roles,
            'viewer_role' => $viewerRole,
            'pending_join' => $pendingJoin,
            'stats' => $stats,
            'can_manage' => $viewerId !== null && self::canManageGroup($group, $viewerId),
        ];
    }

    /**
     * @param list<array<string,mixed>> $members
     * @return array<string, mixed>
     */
    public static function aggregateStats(array $group, array $members): array
    {
        $userIds = array_map(static fn(array $m): int => (int) $m['user_id'], $members);
        $solved = 0;
        $labs = 0;
        $writeups = 0;
        if ($userIds !== []) {
            $in = implode(',', array_fill(0, count($userIds), '?'));
            $solved = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM challenge_solves WHERE user_id IN ({$in})",
                $userIds
            )['c'] ?? 0);
            $labs = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM lab_completions WHERE user_id IN ({$in})",
                $userIds
            )['c'] ?? 0);
            $writeups = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM writeups
                 WHERE user_id IN ({$in}) AND status = 'published' AND deleted_at IS NULL",
                $userIds
            )['c'] ?? 0);
        }

        $categoryStrength = [];
        if (($group['group_type'] ?? '') === 'ctf' && $userIds !== []) {
            $in = implode(',', array_fill(0, count($userIds), '?'));
            $rows = Database::fetchAll(
                "SELECT cc.name, COUNT(*) AS cnt
                 FROM challenge_solves cs
                 INNER JOIN challenges c ON c.id = cs.challenge_id
                 INNER JOIN challenge_categories cc ON cc.id = c.category_id
                 WHERE cs.user_id IN ({$in})
                 GROUP BY cc.id
                 ORDER BY cnt DESC
                 LIMIT 6",
                $userIds
            );
            foreach ($rows as $r) {
                $categoryStrength[] = ['name' => (string) $r['name'], 'count' => (int) $r['cnt']];
            }
        }

        return [
            'member_count' => count($members),
            'challenges_solved' => $solved,
            'labs_completed' => $labs,
            'writeups' => $writeups,
            'category_strength' => $categoryStrength,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function create(int $ownerId, array $data, array $skillIds = []): array
    {
        $maxOwned = (int) config('collaboration.max_open_groups_per_user', 10);
        $owned = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM collab_groups WHERE owner_id = ? AND status = 'active'",
            [$ownerId]
        )['c'] ?? 0);
        if ($owned >= $maxOwned) {
            throw new RuntimeException('You own the maximum number of active groups.', 429);
        }

        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        if ($name === '' || $description === '') {
            throw new InvalidArgumentException('Name and description are required.');
        }
        $types = ['study','ctf','project','course','general'];
        $type = in_array($data['group_type'] ?? '', $types, true) ? (string) $data['group_type'] : 'study';
        $slugBase = trim((string) ($data['slug'] ?? ''));
        if ($slugBase === '') {
            $slugBase = $name;
        }
        $slug = self::uniqueSlug($slugBase);
        $maxMembers = max(2, min(
            (int) config('collaboration.max_group_members', 50),
            (int) ($data['max_members'] ?? 20)
        ));

        $specs = null;
        if (!empty($data['specializations']) && is_array($data['specializations'])) {
            $specs = json_encode(array_values(array_map('strval', $data['specializations'])));
        }

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO collab_groups
                 (name, slug, description, group_type, owner_id, visibility, join_policy, max_members, specializations)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $name,
                    $slug,
                    $description,
                    $type,
                    $ownerId,
                    in_array($data['visibility'] ?? '', ['public','community','private'], true)
                        ? $data['visibility'] : 'community',
                    in_array($data['join_policy'] ?? '', ['open','approval','invite_only'], true)
                        ? $data['join_policy'] : 'approval',
                    $maxMembers,
                    $specs,
                ]
            );
            $id = (int) Database::lastInsertId();
            $ownerRole = $type === 'ctf' ? 'captain' : 'owner';
            Database::execute(
                'INSERT INTO collab_group_members (group_id, user_id, role, status) VALUES (?, ?, ?, \'active\')',
                [$id, $ownerId, $ownerRole]
            );
            self::syncSkills($id, $skillIds);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($ownerId, 'group_created', 'collab_group', $id, ['type' => $type]);
        $row = self::find($id);
        if ($row === null) {
            throw new RuntimeException('Group missing after create.');
        }
        return $row;
    }

    /** @param list<int> $skillIds */
    public static function syncSkills(int $groupId, array $skillIds): void
    {
        Database::execute('DELETE FROM collab_group_skills WHERE group_id = ?', [$groupId]);
        foreach ($skillIds as $skillId) {
            $skillId = (int) $skillId;
            if ($skillId <= 0) {
                continue;
            }
            Database::execute(
                'INSERT IGNORE INTO collab_group_skills (group_id, skill_id, weight) VALUES (?, ?, 1.00)',
                [$groupId, $skillId]
            );
        }
    }

    /**
     * @param array<string, mixed> $group
     * @param array<string, mixed> $data
     * @param list<int> $skillIds
     */
    public static function update(array $group, int $actorId, array $data, array $skillIds = []): array
    {
        if (!self::canManageGroup($group, $actorId)) {
            throw new RuntimeException('Forbidden', 403);
        }
        $name = trim((string) ($data['name'] ?? $group['name']));
        $description = trim((string) ($data['description'] ?? $group['description']));
        if ($name === '' || $description === '') {
            throw new InvalidArgumentException('Name and description are required.');
        }
        $slug = (string) $group['slug'];
        if (array_key_exists('slug', $data)) {
            $base = trim((string) $data['slug']);
            if ($base !== '') {
                $slug = self::uniqueSlug($base, (int) $group['id']);
            }
        }
        Database::execute(
            'UPDATE collab_groups SET name=?, slug=?, description=?, visibility=?, join_policy=?, max_members=?
             WHERE id = ?',
            [
                $name,
                $slug,
                $description,
                in_array($data['visibility'] ?? $group['visibility'], ['public','community','private'], true)
                    ? ($data['visibility'] ?? $group['visibility']) : $group['visibility'],
                in_array($data['join_policy'] ?? $group['join_policy'], ['open','approval','invite_only'], true)
                    ? ($data['join_policy'] ?? $group['join_policy']) : $group['join_policy'],
                max(2, min((int) config('collaboration.max_group_members', 50), (int) ($data['max_members'] ?? $group['max_members']))),
                (int) $group['id'],
            ]
        );
        self::syncSkills((int) $group['id'], $skillIds);
        ActivityLogService::log($actorId, 'group_updated', 'collab_group', (int) $group['id']);
        return self::find((int) $group['id']) ?? $group;
    }
}
