<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\SkillLevel;
use App\Services\ActivityLogService;
use App\Services\NotificationService;

/**
 * Evidence-driven skill progress.
 * Source of truth = skill_evidence (accepted).
 * user_skills is a materialized cache.
 */
final class SkillProgressService
{
    /**
     * Recalculate one user+skill from accepted evidence + requirements.
     *
     * @return array{level:int, progress:int, evidence_count:int, level_changed:bool}
     */
    public static function recalculateUserSkill(int $userId, int $skillId): array
    {
        $evidence = Database::fetchAll(
            "SELECT e.*, c.difficulty AS challenge_difficulty
             FROM skill_evidence e
             LEFT JOIN challenges c
               ON e.source_type = 'challenge' AND e.source_id = c.id
             WHERE e.user_id = ? AND e.skill_id = ? AND e.status = 'accepted'
             ORDER BY e.created_at ASC",
            [$userId, $skillId]
        );

        $evidenceCount = count($evidence);
        $requirements = Database::fetchAll(
            'SELECT * FROM skill_requirements WHERE skill_id = ? ORDER BY target_level ASC, id ASC',
            [$skillId]
        );

        $previous = Database::fetch(
            'SELECT current_level FROM user_skills WHERE user_id = ? AND skill_id = ? LIMIT 1',
            [$userId, $skillId]
        );
        $previousLevel = (int) ($previous['current_level'] ?? 0);

        if ($evidenceCount === 0) {
            self::upsertUserSkill($userId, $skillId, 0, 0, 0, null);
            return [
                'level' => 0,
                'progress' => 0,
                'evidence_count' => 0,
                'level_changed' => $previousLevel !== 0,
            ];
        }

        $level = 0;
        if ($requirements !== []) {
            for ($candidate = 1; $candidate <= 5; $candidate++) {
                $levelReqs = array_values(array_filter(
                    $requirements,
                    static fn(array $r): bool => (int) $r['target_level'] === $candidate
                        && (bool) ($r['is_required'] ?? 1)
                ));
                if ($levelReqs === []) {
                    // No requirements for this level — do not auto-grant
                    break;
                }
                if (self::requirementsSatisfied($levelReqs, $evidence)) {
                    $level = $candidate;
                } else {
                    break;
                }
            }
        } else {
            // Fallback when no requirements configured: strength-weighted score → level thresholds
            $score = self::evidenceScore($evidence);
            $level = self::levelFromScore($score);
        }

        $nextLevel = min(5, $level + 1);
        $progress = 100;
        if ($level < 5) {
            $nextReqs = array_values(array_filter(
                $requirements,
                static fn(array $r): bool => (int) $r['target_level'] === $nextLevel
                    && (bool) ($r['is_required'] ?? 1)
            ));
            if ($nextReqs !== []) {
                $progress = self::requirementProgressPercent($nextReqs, $evidence);
            } else {
                $score = self::evidenceScore($evidence);
                $levels = SkillLevel::mapByLevel();
                $curMin = $levels[$level]->minimum_score ?? 0;
                $nextMin = $levels[$nextLevel]->minimum_score ?? 100;
                $span = max(1, $nextMin - $curMin);
                $progress = (int) max(0, min(99, round((($score - $curMin) / $span) * 100)));
            }
        }

        $lastAt = (string) ($evidence[array_key_last($evidence)]['created_at'] ?? date('Y-m-d H:i:s'));
        self::upsertUserSkill($userId, $skillId, $level, $progress, $evidenceCount, $lastAt);

        $changed = $level !== $previousLevel;
        if ($changed && $level > $previousLevel) {
            $name = SkillLevel::nameFor($level);
            $skill = Database::fetch('SELECT name FROM skills WHERE id = ?', [$skillId]);
            NotificationService::create(
                $userId,
                'skill_level_changed',
                'Skill progress: ' . ($skill['name'] ?? 'Skill'),
                'You reached ' . $name . ' based on your evidence.',
                'skill',
                $skillId
            );
            ActivityLogService::log($userId, 'skill_level_changed', 'skill', $skillId, [
                'from' => $previousLevel,
                'to' => $level,
            ]);
        }

        return [
            'level' => $level,
            'progress' => $progress,
            'evidence_count' => $evidenceCount,
            'level_changed' => $changed,
        ];
    }

    /**
     * @param list<array<string, mixed>> $requirements
     * @param list<array<string, mixed>> $evidence
     */
    public static function requirementsSatisfied(array $requirements, array $evidence): bool
    {
        foreach ($requirements as $req) {
            if (self::countMatching($req, $evidence) < (int) $req['minimum_count']) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param list<array<string, mixed>> $requirements
     * @param list<array<string, mixed>> $evidence
     * @return list<array{description:string,met:bool,have:int,need:int,evidence_type:string}>
     */
    public static function requirementChecklist(array $requirements, array $evidence): array
    {
        $out = [];
        foreach ($requirements as $req) {
            $have = self::countMatching($req, $evidence);
            $need = (int) $req['minimum_count'];
            $desc = (string) ($req['description'] ?: self::defaultRequirementLabel($req));
            $out[] = [
                'description' => $desc,
                'met' => $have >= $need,
                'have' => $have,
                'need' => $need,
                'evidence_type' => (string) $req['evidence_type'],
            ];
        }
        return $out;
    }

    /**
     * @param list<array<string, mixed>> $requirements
     * @param list<array<string, mixed>> $evidence
     */
    private static function requirementProgressPercent(array $requirements, array $evidence): int
    {
        if ($requirements === []) {
            return 0;
        }
        $parts = 0;
        $met = 0.0;
        foreach ($requirements as $req) {
            $need = max(1, (int) $req['minimum_count']);
            $have = min($need, self::countMatching($req, $evidence));
            $met += $have / $need;
            $parts++;
        }
        return (int) max(0, min(99, round(($met / $parts) * 100)));
    }

    /**
     * @param array<string, mixed> $req
     * @param list<array<string, mixed>> $evidence
     */
    private static function countMatching(array $req, array $evidence): int
    {
        $type = (string) $req['evidence_type'];
        $minDiff = $req['minimum_difficulty'] ?? null;
        $count = 0;

        $diffRank = ['easy' => 1, 'medium' => 2, 'hard' => 3, 'expert' => 4];

        foreach ($evidence as $ev) {
            $evType = (string) $ev['evidence_type'];
            if ($type === 'challenge_solved' || $type === 'challenge_hard_solved') {
                if ($evType !== 'challenge_solved' && $evType !== 'challenge_hard_solved') {
                    continue;
                }
                if ($minDiff !== null && $minDiff !== '') {
                    $d = (string) ($ev['challenge_difficulty'] ?? 'easy');
                    $need = $diffRank[(string) $minDiff] ?? 1;
                    $have = $diffRank[$d] ?? 1;
                    if ($have < $need) {
                        continue;
                    }
                }
                if ($type === 'challenge_hard_solved') {
                    $d = (string) ($ev['challenge_difficulty'] ?? 'easy');
                    if (($diffRank[$d] ?? 1) < 3) {
                        continue;
                    }
                }
                $count++;
                continue;
            }

            if ($evType === $type) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param array<string, mixed> $req
     */
    private static function defaultRequirementLabel(array $req): string
    {
        $n = (int) $req['minimum_count'];
        $type = str_replace('_', ' ', (string) $req['evidence_type']);
        $diff = $req['minimum_difficulty'] ?? null;
        if ($diff) {
            return $n . ' ' . ucfirst((string) $diff) . ' challenge' . ($n === 1 ? '' : 's');
        }
        return $n . ' ' . $type;
    }

    /**
     * @param list<array<string, mixed>> $evidence
     */
    private static function evidenceScore(array $evidence): int
    {
        $typesSeen = [];
        $score = 0.0;
        foreach ($evidence as $ev) {
            $strength = (int) $ev['strength'];
            $score += $strength * 4;
            $typesSeen[(string) $ev['evidence_type']] = true;
            $diff = $ev['challenge_difficulty'] ?? null;
            if ($diff === 'hard' || $diff === 'expert') {
                $score += 6;
            } elseif ($diff === 'medium') {
                $score += 3;
            }
        }
        // Diversity bonus
        $score += count($typesSeen) * 5;
        return (int) max(0, min(100, round($score)));
    }

    private static function levelFromScore(int $score): int
    {
        $levels = SkillLevel::mapByLevel();
        $level = 0;
        foreach ($levels as $lvl => $def) {
            if ($lvl === 0) {
                continue;
            }
            if ($score >= $def->minimum_score) {
                $level = $lvl;
            }
        }
        return $level;
    }

    private static function upsertUserSkill(
        int $userId,
        int $skillId,
        int $level,
        int $progress,
        int $evidenceCount,
        ?string $lastActivity
    ): void {
        Database::execute(
            'INSERT INTO user_skills
             (user_id, skill_id, current_level, progress_score, evidence_count, last_activity_at)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
               current_level = VALUES(current_level),
               progress_score = VALUES(progress_score),
               evidence_count = VALUES(evidence_count),
               last_activity_at = VALUES(last_activity_at)',
            [$userId, $skillId, $level, $progress, $evidenceCount, $lastActivity]
        );
    }

    public static function recalculateUser(int $userId): int
    {
        $skillIds = Database::fetchAll(
            'SELECT DISTINCT skill_id FROM skill_evidence WHERE user_id = ?
             UNION
             SELECT skill_id FROM user_skills WHERE user_id = ?',
            [$userId, $userId]
        );
        $n = 0;
        foreach ($skillIds as $row) {
            self::recalculateUserSkill($userId, (int) $row['skill_id']);
            $n++;
        }
        return $n;
    }

    public static function recalculateAll(): int
    {
        $pairs = Database::fetchAll(
            'SELECT DISTINCT user_id, skill_id FROM skill_evidence
             UNION
             SELECT user_id, skill_id FROM user_skills'
        );
        foreach ($pairs as $row) {
            self::recalculateUserSkill((int) $row['user_id'], (int) $row['skill_id']);
        }
        return count($pairs);
    }

    /**
     * @return array{level:int,name:string,progress:int,evidence_count:int}|null
     */
    public static function getUserSkill(int $userId, int $skillId): ?array
    {
        $row = Database::fetch(
            'SELECT us.*, sl.name AS level_name
             FROM user_skills us
             LEFT JOIN skill_levels sl ON sl.level = us.current_level
             WHERE us.user_id = ? AND us.skill_id = ?
             LIMIT 1',
            [$userId, $skillId]
        );
        if ($row === null) {
            return [
                'level' => 0,
                'name' => SkillLevel::nameFor(0),
                'progress' => 0,
                'evidence_count' => 0,
            ];
        }
        return [
            'level' => (int) $row['current_level'],
            'name' => (string) ($row['level_name'] ?? SkillLevel::nameFor((int) $row['current_level'])),
            'progress' => (int) $row['progress_score'],
            'evidence_count' => (int) $row['evidence_count'],
        ];
    }
}
