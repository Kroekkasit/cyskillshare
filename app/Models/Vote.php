<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

/**
 * Polymorphic votes — target_type must be validated against an allowlist.
 */
final class Vote extends Model
{
    public const ALLOWED_TARGETS = ['thread', 'reply'];
    public const ALLOWED_TYPES = ['up', 'down'];

    public static function cast(int $userId, string $targetType, int $targetId, string $voteType): void
    {
        if (!in_array($targetType, self::ALLOWED_TARGETS, true)) {
            throw new InvalidArgumentException('Invalid vote target type.');
        }
        if (!in_array($voteType, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Invalid vote type.');
        }
        if (!self::targetExists($targetType, $targetId)) {
            throw new InvalidArgumentException('Vote target does not exist.');
        }

        self::execute(
            'INSERT INTO votes (user_id, target_type, target_id, vote_type)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE vote_type = VALUES(vote_type)',
            [$userId, $targetType, $targetId, $voteType]
        );
    }

    private static function targetExists(string $targetType, int $targetId): bool
    {
        return match ($targetType) {
            'thread' => Thread::find($targetId) !== null,
            'reply' => Reply::find($targetId) !== null,
            default => false,
        };
    }
}
