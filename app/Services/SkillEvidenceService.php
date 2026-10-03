<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Challenge;
use App\Models\SkillEvidence;
use InvalidArgumentException;
use RuntimeException;

/**
 * Central evidence recorder for Skill Tree.
 * Future Labs/Projects/Writeups should call record*() methods here.
 */
final class SkillEvidenceService
{
    public static function canVerify(): bool
    {
        $roles = config('skills.verifier_roles', ['instructor', 'mentor', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    /**
     * Called after a successful Arena challenge solve.
     */
    public static function recordChallengeEvidence(int $userId, int $challengeId): void
    {
        $challenge = Challenge::find($challengeId);
        if ($challenge === null) {
            return;
        }

        $mappings = Database::fetchAll(
            'SELECT skill_id, weight FROM challenge_skills WHERE challenge_id = ?',
            [$challengeId]
        );
        if ($mappings === []) {
            return;
        }

        $diffMap = config('skills.difficulty_strength', []);
        $strength = 2;
        if (is_array($diffMap) && isset($diffMap[$challenge->difficulty])) {
            $strength = max(1, min(5, (int) $diffMap[$challenge->difficulty]));
        }

        $evidenceType = in_array($challenge->difficulty, ['hard', 'expert'], true)
            ? 'challenge_hard_solved'
            : 'challenge_solved';

        foreach ($mappings as $map) {
            $skillId = (int) $map['skill_id'];
            $weight = (float) $map['weight'];
            $effectiveStrength = max(1, min(5, (int) round($strength * max(0.3, min(1.5, $weight)))));

            self::upsertEvidence([
                'user_id' => $userId,
                'skill_id' => $skillId,
                'evidence_type' => $evidenceType === 'challenge_hard_solved' && $weight < 0.5
                    ? 'challenge_solved'
                    : $evidenceType,
                'source_type' => 'challenge',
                'source_id' => $challengeId,
                'title' => $challenge->title,
                'description' => 'Solved Arena challenge (' . $challenge->difficulty . ')',
                'strength' => $effectiveStrength,
                'status' => 'accepted',
            ]);

            SkillProgressService::recalculateUserSkill($userId, $skillId);
        }
    }

    /**
     * Best-answer evidence for the reply author, using thread_skills mappings.
     */
    public static function recordBestAnswerEvidence(int $replyUserId, int $threadId, int $replyId, string $threadTitle): void
    {
        $mappings = Database::fetchAll(
            'SELECT skill_id, weight FROM thread_skills WHERE thread_id = ?',
            [$threadId]
        );
        if ($mappings === []) {
            return;
        }

        foreach ($mappings as $map) {
            $skillId = (int) $map['skill_id'];
            self::upsertEvidence([
                'user_id' => $replyUserId,
                'skill_id' => $skillId,
                'evidence_type' => 'community_best_answer',
                'source_type' => 'reply',
                'source_id' => $replyId,
                'title' => 'Best answer: ' . mb_strimwidth($threadTitle, 0, 80, '…'),
                'description' => 'Marked as best answer in Community',
                'strength' => 3,
                'status' => 'accepted',
            ]);
            SkillProgressService::recalculateUserSkill($replyUserId, $skillId);
        }
    }

    /**
     * Remove best-answer evidence when unmarked, then recalculate.
     */
    public static function revokeBestAnswerEvidence(int $replyId): void
    {
        $rows = Database::fetchAll(
            "SELECT id, user_id, skill_id FROM skill_evidence
             WHERE source_type = 'reply' AND source_id = ? AND evidence_type = 'community_best_answer'",
            [$replyId]
        );
        foreach ($rows as $row) {
            Database::execute('DELETE FROM skill_evidence WHERE id = ?', [(int) $row['id']]);
            SkillProgressService::recalculateUserSkill((int) $row['user_id'], (int) $row['skill_id']);
        }
    }

    /**
     * Hook for future writeup system.
     */
    public static function recordWriteupEvidence(int $userId, int $writeupId, string $title, array $skillIds): void
    {
        foreach ($skillIds as $skillId) {
            self::upsertEvidence([
                'user_id' => $userId,
                'skill_id' => (int) $skillId,
                'evidence_type' => 'writeup',
                'source_type' => 'writeup',
                'source_id' => $writeupId,
                'title' => $title,
                'description' => 'Technical writeup',
                'strength' => 3,
                'status' => 'accepted',
            ]);
            SkillProgressService::recalculateUserSkill($userId, (int) $skillId);
        }
    }

    /**
     * Hook for future project system — pending until verified.
     */
    public static function recordProjectEvidence(int $userId, int $projectId, string $title, array $skillIds): void
    {
        foreach ($skillIds as $skillId) {
            self::upsertEvidence([
                'user_id' => $userId,
                'skill_id' => (int) $skillId,
                'evidence_type' => 'project',
                'source_type' => 'project',
                'source_id' => $projectId,
                'title' => $title,
                'description' => 'Project submission (pending verification)',
                'strength' => 3,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Record evidence after a Cyber Lab completion (idempotent per skill mapping).
     */
    public static function recordLabEvidence(int $userId, int $labId): void
    {
        $lab = Database::fetch('SELECT id, title, difficulty FROM labs WHERE id = ? LIMIT 1', [$labId]);
        if ($lab === null) {
            return;
        }

        $mappings = Database::fetchAll(
            'SELECT skill_id, weight FROM lab_skills WHERE lab_id = ?',
            [$labId]
        );
        if ($mappings === []) {
            return;
        }

        $labDiffMap = [
            'beginner' => 2,
            'intermediate' => 3,
            'advanced' => 4,
            'expert' => 5,
        ];
        $strength = $labDiffMap[(string) ($lab['difficulty'] ?? 'intermediate')] ?? 3;

        foreach ($mappings as $map) {
            $skillId = (int) $map['skill_id'];
            $weight = (float) $map['weight'];
            $effective = max(1, min(5, (int) round($strength * max(0.3, min(1.5, $weight)))));
            self::upsertEvidence([
                'user_id' => $userId,
                'skill_id' => $skillId,
                'evidence_type' => 'lab',
                'source_type' => 'lab',
                'source_id' => $labId,
                'title' => (string) $lab['title'],
                'description' => 'Completed Cyber Lab (' . $lab['difficulty'] . ')',
                'strength' => $effective,
                'status' => 'accepted',
            ]);
            SkillProgressService::recalculateUserSkill($userId, $skillId);
        }
    }

    /**
     * Manual evidence submitted by user (pending review).
     */
    public static function recordManualEvidence(
        int $userId,
        int $skillId,
        string $title,
        string $description
    ): SkillEvidence {
        if ($title === '' || mb_strlen($title) > 255) {
            throw new InvalidArgumentException('Invalid title.');
        }
        self::upsertEvidence([
            'user_id' => $userId,
            'skill_id' => $skillId,
            'evidence_type' => 'manual',
            'source_type' => 'manual',
            'source_id' => null,
            // Unique constraint needs a source_id — use negative time-based id via separate insert
            'title' => $title,
            'description' => $description,
            'strength' => 2,
            'status' => 'pending',
        ], true);

        $row = Database::fetch(
            "SELECT * FROM skill_evidence
             WHERE user_id = ? AND skill_id = ? AND evidence_type = 'manual' AND status = 'pending'
             ORDER BY id DESC LIMIT 1",
            [$userId, $skillId]
        );
        if ($row === null) {
            throw new RuntimeException('Failed to create evidence.');
        }
        ActivityLogService::log($userId, 'evidence_created', 'skill_evidence', (int) $row['id']);
        return new SkillEvidence($row);
    }

    public static function verify(int $evidenceId, int $verifierId, ?string $note = null): void
    {
        if (!self::canVerify()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $ev = SkillEvidence::find($evidenceId);
        if ($ev === null) {
            throw new InvalidArgumentException('Evidence not found.');
        }
        if ($ev->user_id === $verifierId) {
            throw new RuntimeException('You cannot verify your own evidence.', 403);
        }
        if ($ev->status === 'accepted' && $ev->verified_by !== null) {
            return;
        }

        Database::execute(
            "UPDATE skill_evidence
             SET status = 'accepted', verified_by = ?, verified_at = NOW(),
                 verification_note = ?, strength = GREATEST(strength, 5)
             WHERE id = ?",
            [$verifierId, $note, $evidenceId]
        );

        ActivityLogService::log($verifierId, 'evidence_verified', 'skill_evidence', $evidenceId);
        NotificationService::create(
            $ev->user_id,
            'evidence_verified',
            'Evidence verified',
            'Your skill evidence “' . mb_strimwidth($ev->title, 0, 60, '…') . '” was verified.',
            'skill_evidence',
            $evidenceId
        );

        SkillProgressService::recalculateUserSkill($ev->user_id, $ev->skill_id);
    }

    public static function reject(int $evidenceId, int $verifierId, string $reason): void
    {
        if (!self::canVerify()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $ev = SkillEvidence::find($evidenceId);
        if ($ev === null) {
            throw new InvalidArgumentException('Evidence not found.');
        }
        if ($ev->user_id === $verifierId) {
            throw new RuntimeException('You cannot reject your own evidence.', 403);
        }

        Database::execute(
            "UPDATE skill_evidence
             SET status = 'rejected', verified_by = ?, verified_at = NOW(), verification_note = ?
             WHERE id = ?",
            [$verifierId, mb_substr($reason, 0, 500), $evidenceId]
        );

        ActivityLogService::log($verifierId, 'evidence_rejected', 'skill_evidence', $evidenceId, [
            'reason_len' => mb_strlen($reason),
        ]);
        NotificationService::create(
            $ev->user_id,
            'evidence_rejected',
            'Evidence not accepted',
            'Your evidence “' . mb_strimwidth($ev->title, 0, 60, '…') . '” was not accepted.',
            'skill_evidence',
            $evidenceId
        );

        SkillProgressService::recalculateUserSkill($ev->user_id, $ev->skill_id);
    }

    /**
     * @param array{
     *   user_id:int,skill_id:int,evidence_type:string,source_type:string,
     *   source_id:?int,title:string,description:?string,strength:int,status:string
     * } $data
     */
    private static function upsertEvidence(array $data, bool $manualUnique = false): void
    {
        if ($manualUnique) {
            // Manual evidence: unique source_id via auto id placeholder using UNIX timestamp + random
            $sourceId = (int) (time() % 1000000000) * 10 + random_int(0, 9);
            Database::execute(
                'INSERT INTO skill_evidence
                 (user_id, skill_id, evidence_type, source_type, source_id, title, description, strength, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $data['user_id'],
                    $data['skill_id'],
                    $data['evidence_type'],
                    $data['source_type'],
                    $sourceId,
                    $data['title'],
                    $data['description'],
                    $data['strength'],
                    $data['status'],
                ]
            );
            return;
        }

        Database::execute(
            'INSERT INTO skill_evidence
             (user_id, skill_id, evidence_type, source_type, source_id, title, description, strength, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               title = VALUES(title),
               description = VALUES(description),
               strength = VALUES(strength),
               status = IF(status = \'rejected\', status, VALUES(status))',
            [
                $data['user_id'],
                $data['skill_id'],
                $data['evidence_type'],
                $data['source_type'],
                $data['source_id'],
                $data['title'],
                $data['description'],
                $data['strength'],
                $data['status'],
            ]
        );

        ActivityLogService::log($data['user_id'], 'evidence_created', 'skill', $data['skill_id'], [
            'source_type' => $data['source_type'],
            'source_id' => $data['source_id'],
            'evidence_type' => $data['evidence_type'],
        ]);
    }

    public static function sourceUrl(string $sourceType, ?int $sourceId): ?string
    {
        if ($sourceId === null) {
            return null;
        }
        return match ($sourceType) {
            'challenge' => '/arena/challenges/' . $sourceId,
            'thread' => '/thread/' . $sourceId,
            'reply' => null,
            'writeup' => '/writeups/id/' . $sourceId,
            'project' => null, // resolved via project detail when username known
            'lab' => '/labs/id/' . $sourceId,
            default => null,
        };
    }

    /**
     * Create missing challenge evidence from existing Arena solves (idempotent).
     */
    public static function backfillFromSolves(?int $userId = null): int
    {
        if ($userId !== null) {
            $rows = Database::fetchAll(
                'SELECT user_id, challenge_id FROM challenge_solves WHERE user_id = ?',
                [$userId]
            );
        } else {
            $rows = Database::fetchAll('SELECT user_id, challenge_id FROM challenge_solves');
        }

        $n = 0;
        foreach ($rows as $row) {
            self::recordChallengeEvidence((int) $row['user_id'], (int) $row['challenge_id']);
            $n++;
        }
        return $n;
    }
}
