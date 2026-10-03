<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

/**
 * Polymorphic bookmarks — target_type must be validated against an allowlist.
 */
final class Bookmark extends Model
{
    public const ALLOWED_TARGETS = ['thread', 'writeup', 'challenge', 'resource'];

    public static function add(int $userId, string $targetType, int $targetId): void
    {
        if (!in_array($targetType, self::ALLOWED_TARGETS, true)) {
            throw new InvalidArgumentException('Invalid bookmark target type.');
        }

        // Phase 1: only threads are fully implemented; schema supports future types.
        if ($targetType === 'thread' && Thread::find($targetId) === null) {
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
}
