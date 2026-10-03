<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use InvalidArgumentException;

final class UserBlockService
{
    public static function block(int $blockerId, int $blockedId): void
    {
        if ($blockerId === $blockedId) {
            throw new InvalidArgumentException('You cannot block yourself.');
        }
        Database::execute(
            'INSERT IGNORE INTO user_blocks (blocker_id, blocked_id) VALUES (?, ?)',
            [$blockerId, $blockedId]
        );
        ActivityLogService::log($blockerId, 'user_blocked', 'user', $blockedId);
    }

    public static function unblock(int $blockerId, int $blockedId): void
    {
        Database::execute(
            'DELETE FROM user_blocks WHERE blocker_id = ? AND blocked_id = ?',
            [$blockerId, $blockedId]
        );
    }

    public static function isBlocked(int $blockerId, int $blockedId): bool
    {
        $row = Database::fetch(
            'SELECT 1 AS x FROM user_blocks WHERE blocker_id = ? AND blocked_id = ? LIMIT 1',
            [$blockerId, $blockedId]
        );
        return $row !== null;
    }

    public static function isBlockedEither(int $a, int $b): bool
    {
        return self::isBlocked($a, $b) || self::isBlocked($b, $a);
    }

    /** @return list<int> */
    public static function blockedIdsFor(int $userId): array
    {
        $rows = Database::fetchAll(
            'SELECT blocked_id AS id FROM user_blocks WHERE blocker_id = ?
             UNION
             SELECT blocker_id AS id FROM user_blocks WHERE blocked_id = ?',
            [$userId, $userId]
        );
        return array_map(static fn(array $r): int => (int) $r['id'], $rows);
    }
}
