<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\RateLimiter;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\Vote;
use InvalidArgumentException;
use RuntimeException;

final class VoteService
{
    /**
     * Toggle / switch vote. Returns final state: 'up'|'down'|null
     */
    public static function vote(int $userId, string $targetType, int $targetId, string $voteType): ?string
    {
        if (!in_array($targetType, Vote::ALLOWED_TARGETS, true)) {
            throw new InvalidArgumentException('Invalid vote target.');
        }
        if (!in_array($voteType, Vote::ALLOWED_TYPES, true)) {
            throw new InvalidArgumentException('Invalid vote type.');
        }

        if (!RateLimiter::attempt($userId, 'vote_created', 60, 60)) {
            throw new RuntimeException('Too many votes. Please slow down.', 429);
        }

        $ownerId = self::ownerId($targetType, $targetId);
        if ($ownerId === null) {
            throw new InvalidArgumentException('Vote target does not exist.');
        }
        if ($ownerId === $userId) {
            throw new RuntimeException('You cannot vote on your own content.', 403);
        }

        $existing = Vote::findUserVote($userId, $targetType, $targetId);

        if ($existing !== null && $existing['vote_type'] === $voteType) {
            Vote::remove($userId, $targetType, $targetId);
            ActivityLogService::log($userId, 'vote_removed', $targetType, $targetId, [
                'vote_type' => $voteType,
            ]);
            return null;
        }

        Vote::upsert($userId, $targetType, $targetId, $voteType);
        ActivityLogService::log($userId, 'vote_created', $targetType, $targetId, [
            'vote_type' => $voteType,
        ]);

        return $voteType;
    }

    private static function ownerId(string $targetType, int $targetId): ?int
    {
        return match ($targetType) {
            'thread' => Thread::findVisible($targetId)?->user_id,
            'reply' => Reply::find($targetId)?->user_id,
            default => null,
        };
    }
}
