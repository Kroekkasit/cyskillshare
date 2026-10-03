<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

final class Vote extends Model
{
    public const ALLOWED_TARGETS = ['thread', 'reply'];
    public const ALLOWED_TYPES = ['up', 'down'];

    /**
     * @return array<string, mixed>|null
     */
    public static function findUserVote(int $userId, string $targetType, int $targetId): ?array
    {
        return self::fetch(
            'SELECT * FROM votes WHERE user_id = ? AND target_type = ? AND target_id = ? LIMIT 1',
            [$userId, $targetType, $targetId]
        );
    }

    public static function upsert(int $userId, string $targetType, int $targetId, string $voteType): void
    {
        if (!in_array($targetType, self::ALLOWED_TARGETS, true) || !in_array($voteType, self::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Invalid vote.');
        }

        self::execute(
            'INSERT INTO votes (user_id, target_type, target_id, vote_type)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE vote_type = VALUES(vote_type)',
            [$userId, $targetType, $targetId, $voteType]
        );
    }

    public static function remove(int $userId, string $targetType, int $targetId): void
    {
        self::execute(
            'DELETE FROM votes WHERE user_id = ? AND target_type = ? AND target_id = ?',
            [$userId, $targetType, $targetId]
        );
    }

    public static function score(string $targetType, int $targetId): int
    {
        $row = self::fetch(
            "SELECT COALESCE(SUM(CASE WHEN vote_type = 'up' THEN 1 WHEN vote_type = 'down' THEN -1 ELSE 0 END), 0) AS score
             FROM votes WHERE target_type = ? AND target_id = ?",
            [$targetType, $targetId]
        );
        return (int) ($row['score'] ?? 0);
    }

    /** @deprecated use VoteService */
    public static function cast(int $userId, string $targetType, int $targetId, string $voteType): void
    {
        self::upsert($userId, $targetType, $targetId, $voteType);
    }
}
