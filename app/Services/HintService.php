<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Models\Challenge;
use App\Models\ChallengeHint;
use App\Models\ChallengeSolve;
use InvalidArgumentException;
use RuntimeException;

final class HintService
{
    /**
     * @return array{hint: ChallengeHint, penalty: int, already: bool}
     */
    public static function reveal(int $userId, int $challengeId, int $hintId): array
    {
        $max = (int) config('arena.hint_reveal_max_per_minute', 20);
        if (!RateLimiter::attempt($userId, 'hint_reveal_rl', $max, 60)) {
            throw new RuntimeException('Too many requests. Please wait before revealing more hints.', 429);
        }
        RateLimiter::hit($userId, 'hint_reveal_rl');

        $challenge = Challenge::findPublished($challengeId);
        if ($challenge === null || !$challenge->isSolvable()) {
            throw new InvalidArgumentException('Challenge not available.');
        }

        $hint = ChallengeHint::find($hintId);
        if ($hint === null || $hint->challenge_id !== $challengeId) {
            throw new InvalidArgumentException('Hint not found.');
        }

        // Progressive: previous hints by order must be revealed first
        $prior = Database::fetch(
            'SELECT h.id FROM challenge_hints h
             LEFT JOIN challenge_hint_usage u
               ON u.hint_id = h.id AND u.user_id = ?
             WHERE h.challenge_id = ? AND h.hint_order < ? AND u.id IS NULL
             ORDER BY h.hint_order ASC LIMIT 1',
            [$userId, $challengeId, $hint->hint_order]
        );
        if ($prior !== null) {
            throw new InvalidArgumentException('Reveal earlier hints first.');
        }

        $existing = Database::fetch(
            'SELECT * FROM challenge_hint_usage WHERE user_id = ? AND hint_id = ? LIMIT 1',
            [$userId, $hintId]
        );
        if ($existing !== null) {
            return [
                'hint' => $hint,
                'penalty' => (int) $existing['penalty_applied'],
                'already' => true,
            ];
        }

        $solved = ChallengeSolve::findForUser($challengeId, $userId);
        $penalty = $solved !== null ? 0 : max(0, $hint->point_penalty);

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO challenge_hint_usage (challenge_id, hint_id, user_id, penalty_applied)
                 VALUES (?, ?, ?, ?)',
                [$challengeId, $hintId, $userId, $penalty]
            );

            if ($penalty > 0 && $solved === null) {
                ChallengeScoringService::recordTransaction(
                    $userId,
                    -$penalty,
                    'hint_penalty',
                    $challengeId,
                    null,
                    $hintId
                );
            }

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            // Unique race: treat as already revealed
            $again = Database::fetch(
                'SELECT * FROM challenge_hint_usage WHERE user_id = ? AND hint_id = ? LIMIT 1',
                [$userId, $hintId]
            );
            if ($again !== null) {
                return [
                    'hint' => $hint,
                    'penalty' => (int) $again['penalty_applied'],
                    'already' => true,
                ];
            }
            throw $e;
        }

        ActivityLogService::log($userId, 'hint_revealed', 'challenge_hint', $hintId, [
            'challenge_id' => $challengeId,
            'penalty' => $penalty,
        ]);

        return [
            'hint' => $hint,
            'penalty' => $penalty,
            'already' => false,
        ];
    }

    /**
     * @return array<int, array{penalty_applied:int, revealed_at:string}>
     */
    public static function usageMap(int $userId, int $challengeId): array
    {
        $rows = Database::fetchAll(
            'SELECT hint_id, penalty_applied, revealed_at
             FROM challenge_hint_usage WHERE user_id = ? AND challenge_id = ?',
            [$userId, $challengeId]
        );
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['hint_id']] = [
                'penalty_applied' => (int) $row['penalty_applied'],
                'revealed_at' => (string) $row['revealed_at'],
            ];
        }
        return $map;
    }
}
