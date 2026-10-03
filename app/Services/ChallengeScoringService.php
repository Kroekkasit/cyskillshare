<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class ChallengeScoringService
{
    public static function hashFlag(string $flag, bool $caseSensitive): string
    {
        $normalized = $caseSensitive ? $flag : mb_strtolower($flag, 'UTF-8');
        return hash('sha256', $normalized);
    }

    public static function totalHintPenalty(int $challengeId, int $userId): int
    {
        $row = Database::fetch(
            'SELECT COALESCE(SUM(penalty_applied), 0) AS total
             FROM challenge_hint_usage
             WHERE challenge_id = ? AND user_id = ?',
            [$challengeId, $userId]
        );
        return (int) ($row['total'] ?? 0);
    }

    public static function awardPoints(int $basePoints, int $hintPenalty): int
    {
        return max(0, $basePoints - $hintPenalty);
    }

    public static function recordTransaction(
        int $userId,
        int $points,
        string $reason,
        ?int $challengeId = null,
        ?int $eventId = null,
        ?int $hintId = null
    ): void {
        Database::execute(
            'INSERT INTO arena_point_transactions
             (user_id, challenge_id, event_id, hint_id, points, reason)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$userId, $challengeId, $eventId, $hintId, $points, $reason]
        );
    }

    public static function userArenaPoints(int $userId): int
    {
        $row = Database::fetch(
            'SELECT COALESCE(SUM(points_awarded), 0) AS total FROM challenge_solves WHERE user_id = ?',
            [$userId]
        );
        return (int) ($row['total'] ?? 0);
    }
}
