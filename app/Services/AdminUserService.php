<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Role;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

final class AdminUserService
{
    /**
     * @return array{rows: list<array<string, mixed>>, total: int, page: int, per_page: int, pages: int}
     */
    public static function paginate(
        int $page = 1,
        int $perPage = 30,
        ?string $q = null,
        ?string $status = null,
        ?string $role = null
    ): array {
        $page = max(1, $page);
        $perPage = max(10, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if ($status !== null && $status !== '' && in_array($status, ['active', 'suspended', 'banned'], true)) {
            $where[] = 'u.status = ?';
            $params[] = $status;
        }

        if ($q !== null && trim($q) !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($q)) . '%';
            $where[] = '(u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ? OR u.student_id LIKE ?)';
            array_push($params, $like, $like, $like, $like);
        }

        $join = '';
        if ($role !== null && $role !== '') {
            $join = 'INNER JOIN user_roles urf ON urf.user_id = u.id
                     INNER JOIN roles rf ON rf.id = urf.role_id AND rf.name = ?';
            array_unshift($params, $role);
        }

        $sqlWhere = implode(' AND ', $where);

        $countRow = Database::fetch(
            "SELECT COUNT(DISTINCT u.id) AS cnt
             FROM users u
             {$join}
             WHERE {$sqlWhere}",
            $params
        );
        $total = (int) ($countRow['cnt'] ?? 0);

        $rows = Database::fetchAll(
            "SELECT u.id, u.username, u.email, u.full_name, u.status, u.created_at, u.last_login_at,
                    GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS roles
             FROM users u
             {$join}
             LEFT JOIN user_roles ur ON ur.user_id = u.id
             LEFT JOIN roles r ON r.id = ur.role_id
             WHERE {$sqlWhere}
             GROUP BY u.id, u.username, u.email, u.full_name, u.status, u.created_at, u.last_login_at
             ORDER BY u.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function detail(int $userId): ?array
    {
        $row = Database::fetch(
            'SELECT id, username, email, full_name, student_id, year_level, program, bio,
                    status, skills_visibility, created_at, updated_at, last_login_at
             FROM users WHERE id = ? LIMIT 1',
            [$userId]
        );
        if ($row === null) {
            return null;
        }

        $user = User::find($userId);
        $row['roles'] = $user?->roleNames() ?? [];
        $row['discussion_count'] = $user?->discussionCount() ?? 0;
        $row['reply_count'] = $user?->replyCount() ?? 0;

        $row['recent_activity'] = Database::fetchAll(
            'SELECT id, action, target_type, target_id, ip_address, created_at
             FROM activity_logs
             WHERE user_id = ?
             ORDER BY id DESC
             LIMIT 25',
            [$userId]
        );

        return $row;
    }

    public static function setStatus(int $userId, string $status, int $actorId): void
    {
        if ($userId === $actorId) {
            throw new RuntimeException('You cannot change your own account status.', 400);
        }

        $user = User::find($userId);
        if ($user === null) {
            throw new InvalidArgumentException('User not found.');
        }

        $user->updateStatus($status);
        ActivityLogService::log($actorId, 'user_status_changed', 'user', $userId, [
            'status' => $status,
        ]);
    }

    public static function assignRole(int $userId, string $roleName, int $actorId): void
    {
        $user = User::find($userId);
        if ($user === null) {
            throw new InvalidArgumentException('User not found.');
        }
        if (Role::findByName($roleName) === null) {
            throw new InvalidArgumentException('Unknown role.');
        }

        $user->assignRole($roleName);
        ActivityLogService::log($actorId, 'role_assigned', 'user', $userId, [
            'role' => $roleName,
        ]);

        if ($userId === Auth::id()) {
            Auth::refreshRoles();
        }
    }

    public static function removeRole(int $userId, string $roleName, int $actorId): void
    {
        $user = User::find($userId);
        if ($user === null) {
            throw new InvalidArgumentException('User not found.');
        }
        if (Role::findByName($roleName) === null) {
            throw new InvalidArgumentException('Unknown role.');
        }

        if ($roleName === 'admin') {
            $admins = Database::fetch(
                "SELECT COUNT(*) AS cnt
                 FROM user_roles ur
                 INNER JOIN roles r ON r.id = ur.role_id
                 WHERE r.name = 'admin'"
            );
            if ((int) ($admins['cnt'] ?? 0) <= 1 && $user->hasRole('admin')) {
                throw new RuntimeException('Cannot remove the last admin role.', 400);
            }
            if ($userId === $actorId) {
                throw new RuntimeException('You cannot remove your own admin role.', 400);
            }
        }

        $user->removeRole($roleName);
        ActivityLogService::log($actorId, 'role_removed', 'user', $userId, [
            'role' => $roleName,
        ]);

        if ($userId === Auth::id()) {
            Auth::refreshRoles();
        }
    }
}
