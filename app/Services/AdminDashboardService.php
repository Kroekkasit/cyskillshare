<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;

/**
 * Platform admin dashboard aggregates and tool directory.
 */
final class AdminDashboardService
{
    public static function requireAdmin(): void
    {
        Auth::requireRole('admin');
    }

    /**
     * @return array<string, int>
     */
    public static function stats(): array
    {
        $count = static function (string $sql): int {
            $row = Database::fetch($sql);
            return (int) ($row['cnt'] ?? 0);
        };

        return [
            'users' => $count('SELECT COUNT(*) AS cnt FROM users'),
            'users_active' => $count("SELECT COUNT(*) AS cnt FROM users WHERE status = 'active'"),
            'users_suspended' => $count("SELECT COUNT(*) AS cnt FROM users WHERE status = 'suspended'"),
            'users_banned' => $count("SELECT COUNT(*) AS cnt FROM users WHERE status = 'banned'"),
            'threads' => $count('SELECT COUNT(*) AS cnt FROM threads WHERE deleted_at IS NULL'),
            'replies' => $count('SELECT COUNT(*) AS cnt FROM replies WHERE deleted_at IS NULL'),
            'activity_logs' => $count('SELECT COUNT(*) AS cnt FROM activity_logs'),
            'failed_logins_24h' => $count(
                "SELECT COUNT(*) AS cnt FROM activity_logs
                 WHERE action = 'failed_login' AND created_at >= (NOW() - INTERVAL 24 HOUR)"
            ),
            'logins_24h' => $count(
                "SELECT COUNT(*) AS cnt FROM activity_logs
                 WHERE action = 'login' AND created_at >= (NOW() - INTERVAL 24 HOUR)"
            ),
            'csrf_rejected_24h' => $count(
                "SELECT COUNT(*) AS cnt FROM activity_logs
                 WHERE action = 'csrf_rejected' AND created_at >= (NOW() - INTERVAL 24 HOUR)"
            ),
            'pending_reports' => $count("SELECT COUNT(*) AS cnt FROM reports WHERE status = 'pending'"),
            'pending_evidence' => self::safeCount(
                "SELECT COUNT(*) AS cnt FROM skill_evidence WHERE status = 'pending'"
            ),
            'challenges' => self::safeCount("SELECT COUNT(*) AS cnt FROM challenges WHERE status = 'published'"),
            'labs' => self::safeCount("SELECT COUNT(*) AS cnt FROM labs WHERE status = 'published'"),
        ];
    }

    private static function safeCount(string $sql): int
    {
        try {
            $row = Database::fetch($sql);
            return (int) ($row['cnt'] ?? 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * @return list<array{title: string, description: string, href: string, roles: list<string>}>
     */
    public static function tools(): array
    {
        return [
            [
                'title' => 'Activity Logs',
                'description' => 'Security audit trail — login, failed login, CRUD, CSRF, uploads.',
                'href' => '/admin/activity',
                'roles' => ['admin'],
            ],
            [
                'title' => 'Users & Roles',
                'description' => 'Manage accounts, suspend/ban, assign RBAC roles.',
                'href' => '/admin/users',
                'roles' => ['admin'],
            ],
            [
                'title' => 'Moderation',
                'description' => 'Review community reports.',
                'href' => '/moderation/reports',
                'roles' => ['moderator', 'admin'],
            ],
            [
                'title' => 'Skill Tree',
                'description' => 'Manage skills, requirements, and recalculate progress.',
                'href' => '/admin/skills',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'Skill Verification',
                'description' => 'Accept or reject pending skill evidence.',
                'href' => '/skills/verification',
                'roles' => ['instructor', 'mentor', 'admin'],
            ],
            [
                'title' => 'Arena Challenges',
                'description' => 'Create and publish CTF challenges and events.',
                'href' => '/arena/admin/challenges',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'Cyber Labs',
                'description' => 'Manage lab catalog, tasks, and cleanup.',
                'href' => '/admin/labs',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'Writeups',
                'description' => 'Feature community writeups.',
                'href' => '/admin/writeups',
                'roles' => ['instructor', 'moderator', 'admin'],
            ],
            [
                'title' => 'Knowledge Review',
                'description' => 'Review submitted knowledge articles.',
                'href' => '/admin/knowledge/review',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'Portfolio Admin',
                'description' => 'Feature portfolios and override visibility.',
                'href' => '/admin/portfolio',
                'roles' => ['admin'],
            ],
            [
                'title' => 'Project Verification',
                'description' => 'Verify student portfolio projects.',
                'href' => '/admin/projects/verification',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'Collaboration',
                'description' => 'Verify mentors and suspend groups.',
                'href' => '/admin/collaboration',
                'roles' => ['instructor', 'admin'],
            ],
            [
                'title' => 'System Status',
                'description' => 'Runtime config, database ping, session settings.',
                'href' => '/admin/system',
                'roles' => ['admin'],
            ],
        ];
    }

    /**
     * @return list<array{title: string, description: string, href: string, roles: list<string>}>
     */
    public static function toolsForCurrentUser(): array
    {
        $out = [];
        foreach (self::tools() as $tool) {
            if (Auth::hasAnyRole($tool['roles'])) {
                $out[] = $tool;
            }
        }
        return $out;
    }
}
