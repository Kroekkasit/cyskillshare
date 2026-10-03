<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Models\Challenge;
use App\Models\ChallengeSolve;
use InvalidArgumentException;
use RuntimeException;

final class ChallengeSubmissionService
{
    /**
     * @return array{correct: bool, points: int, already_solved: bool, message: string}
     */
    public static function submit(int $userId, int $challengeId, string $submittedFlag): array
    {
        $max = (int) config('arena.flag_submit_max_per_minute', 8);
        if (!RateLimiter::attempt($userId, 'challenge_attempt_rl', $max, 60)) {
            throw new RuntimeException('Too many submissions. Please wait and try again.', 429);
        }
        // Also count per challenge key
        if (!RateLimiter::attempt($userId, 'challenge_attempt_rl_' . $challengeId, $max, 60)) {
            throw new RuntimeException('Too many submissions. Please wait and try again.', 429);
        }
        RateLimiter::hit($userId, 'challenge_attempt_rl');
        RateLimiter::hit($userId, 'challenge_attempt_rl_' . $challengeId);

        $meta = Challenge::fetchFlagHash($challengeId);
        if ($meta === null
            || ($meta['status'] ?? '') !== 'published'
            || !(bool) ($meta['is_active'] ?? false)
        ) {
            throw new InvalidArgumentException('Challenge not available.');
        }

        $flag = $submittedFlag;
        // Do not trim in a way that changes semantics; only reject empty
        if ($flag === '') {
            throw new InvalidArgumentException('Flag cannot be empty.');
        }
        if (mb_strlen($flag) > 500) {
            throw new InvalidArgumentException('Flag is too long.');
        }

        $caseSensitive = (bool) ($meta['case_sensitive'] ?? true);
        $submittedHash = ChallengeScoringService::hashFlag($flag, $caseSensitive);
        $expectedHash = (string) $meta['flag_hash'];
        $correct = hash_equals($expectedHash, $submittedHash);

        $existingSolve = ChallengeSolve::findForUser($challengeId, $userId);
        if ($existingSolve !== null) {
            self::recordAttempt($challengeId, $userId, $submittedHash, $correct, 0);
            ActivityLogService::log($userId, 'challenge_attempted', 'challenge', $challengeId, [
                'correct' => $correct,
                'already_solved' => true,
            ]);
            return [
                'correct' => $correct,
                'points' => 0,
                'already_solved' => true,
                'message' => $correct
                    ? 'You already solved this challenge.'
                    : 'Incorrect flag. You already solved this challenge previously.',
            ];
        }

        $attemptNumber = self::nextAttemptNumber($challengeId, $userId);

        if (!$correct) {
            self::recordAttempt($challengeId, $userId, $submittedHash, false, 0, $attemptNumber);
            ActivityLogService::log($userId, 'challenge_attempted', 'challenge', $challengeId, [
                'correct' => false,
            ]);
            return [
                'correct' => false,
                'points' => 0,
                'already_solved' => false,
                'message' => 'Incorrect flag. Keep investigating and try again.',
            ];
        }

        $base = (int) $meta['points'];
        $penalty = ChallengeScoringService::totalHintPenalty($challengeId, $userId);
        $awarded = ChallengeScoringService::awardPoints($base, $penalty);
        $hintsUsed = self::hintsUsedCount($challengeId, $userId);

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO challenge_submissions
                 (challenge_id, user_id, submitted_flag_hash, is_correct, points_awarded, attempt_number)
                 VALUES (?, ?, ?, 1, ?, ?)',
                [$challengeId, $userId, $submittedHash, $awarded, $attemptNumber]
            );

            Database::execute(
                'INSERT INTO challenge_solves
                 (challenge_id, user_id, points_awarded, hints_used)
                 VALUES (?, ?, ?, ?)',
                [$challengeId, $userId, $awarded, $hintsUsed]
            );

            ChallengeScoringService::recordTransaction(
                $userId,
                $awarded,
                'challenge_solved',
                $challengeId
            );

            Database::execute(
                'UPDATE challenges SET first_solved_at = COALESCE(first_solved_at, NOW()) WHERE id = ?',
                [$challengeId]
            );

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            // Unique constraint race — another request solved first
            $race = ChallengeSolve::findForUser($challengeId, $userId);
            if ($race !== null) {
                return [
                    'correct' => true,
                    'points' => 0,
                    'already_solved' => true,
                    'message' => 'You already solved this challenge.',
                ];
            }
            throw $e;
        }

        ActivityLogService::log($userId, 'challenge_solved', 'challenge', $challengeId, [
            'points' => $awarded,
        ]);
        ActivityLogService::log($userId, 'challenge_attempted', 'challenge', $challengeId, [
            'correct' => true,
        ]);

        NotificationService::create(
            $userId,
            'challenge_solved',
            'Challenge solved!',
            'You earned ' . $awarded . ' Arena points.',
            'challenge',
            $challengeId
        );

        // Skill Tree evidence (Prompt 4) — never fails the solve if mapping is empty/errors
        try {
            SkillEvidenceService::recordChallengeEvidence($userId, $challengeId);
        } catch (\Throwable $e) {
            \App\Core\ErrorHandler::log('Skill evidence after solve failed: ' . $e->getMessage());
        }

        return [
            'correct' => true,
            'points' => $awarded,
            'already_solved' => false,
            'message' => 'Challenge solved! +' . $awarded . ' Arena points.',
        ];
    }

    private static function nextAttemptNumber(int $challengeId, int $userId): int
    {
        $row = Database::fetch(
            'SELECT COALESCE(MAX(attempt_number), 0) AS mx
             FROM challenge_submissions WHERE challenge_id = ? AND user_id = ?',
            [$challengeId, $userId]
        );
        return ((int) ($row['mx'] ?? 0)) + 1;
    }

    private static function hintsUsedCount(int $challengeId, int $userId): int
    {
        $row = Database::fetch(
            'SELECT COUNT(*) AS cnt FROM challenge_hint_usage WHERE challenge_id = ? AND user_id = ?',
            [$challengeId, $userId]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    private static function recordAttempt(
        int $challengeId,
        int $userId,
        string $hash,
        bool $correct,
        int $points,
        ?int $attemptNumber = null
    ): void {
        $n = $attemptNumber ?? self::nextAttemptNumber($challengeId, $userId);
        Database::execute(
            'INSERT INTO challenge_submissions
             (challenge_id, user_id, submitted_flag_hash, is_correct, points_awarded, attempt_number)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$challengeId, $userId, $hash, $correct ? 1 : 0, $points, $n]
        );
    }
}
