<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

final class Bookmark extends Model
{
    public const ALLOWED_TARGETS = ['thread', 'writeup', 'challenge', 'resource'];

    public static function exists(int $userId, string $targetType, int $targetId): bool
    {
        $row = self::fetch(
            'SELECT id FROM bookmarks WHERE user_id = ? AND target_type = ? AND target_id = ? LIMIT 1',
            [$userId, $targetType, $targetId]
        );
        return $row !== null;
    }

    public static function add(int $userId, string $targetType, int $targetId): void
    {
        if (!in_array($targetType, self::ALLOWED_TARGETS, true)) {
            throw new InvalidArgumentException('Invalid bookmark target type.');
        }

        if ($targetType === 'thread' && Thread::findVisible($targetId) === null) {
            throw new InvalidArgumentException('Bookmark target does not exist.');
        }

        self::execute(
            'INSERT IGNORE INTO bookmarks (user_id, target_type, target_id) VALUES (?, ?, ?)',
            [$userId, $targetType, $targetId]
        );
    }

    public static function remove(int $userId, string $targetType, int $targetId): void
    {
        self::execute(
            'DELETE FROM bookmarks WHERE user_id = ? AND target_type = ? AND target_id = ?',
            [$userId, $targetType, $targetId]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function threadsForUser(int $userId, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;

        return self::fetchAll(
            "SELECT t.*, u.username, c.name AS channel_name, c.slug AS channel_slug,
                    (SELECT COUNT(*) FROM replies r WHERE r.thread_id = t.id AND r.deleted_at IS NULL) AS reply_count,
                    (SELECT COALESCE(SUM(CASE WHEN v.vote_type = 'up' THEN 1 WHEN v.vote_type = 'down' THEN -1 ELSE 0 END), 0)
                     FROM votes v WHERE v.target_type = 'thread' AND v.target_id = t.id) AS score,
                    b.created_at AS bookmarked_at
             FROM bookmarks b
             INNER JOIN threads t ON t.id = b.target_id AND t.deleted_at IS NULL
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN channels c ON c.id = t.channel_id
             WHERE b.user_id = ? AND b.target_type = 'thread'
             ORDER BY b.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [$userId]
        );
    }
}
