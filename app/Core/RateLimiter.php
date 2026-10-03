<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\ActivityLog;

/**
 * Simple application-level rate limiter using activity_logs counts.
 */
final class RateLimiter
{
    /**
     * @return true if allowed, false if limited
     */
    public static function attempt(?int $userId, string $action, int $maxAttempts, int $decaySeconds): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $since = date('Y-m-d H:i:s', time() - $decaySeconds);

        if ($userId !== null) {
            $row = Database::fetch(
                'SELECT COUNT(*) AS cnt FROM activity_logs
                 WHERE user_id = ? AND action = ? AND created_at >= ?',
                [$userId, $action, $since]
            );
        } else {
            $row = Database::fetch(
                'SELECT COUNT(*) AS cnt FROM activity_logs
                 WHERE user_id IS NULL AND action = ? AND ip_address = ? AND created_at >= ?',
                [$action, $ip, $since]
            );
        }

        $count = (int) ($row['cnt'] ?? 0);
        return $count < $maxAttempts;
    }

    public static function hit(?int $userId, string $action): void
    {
        // Rely on ActivityLogService for real actions; this records limiter-only probes when needed.
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'target_type' => 'rate_limit',
            'target_id' => null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT'])
                ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 512)
                : null,
            'metadata' => null,
        ]);
    }
}
