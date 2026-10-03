<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ChallengeSolve extends Model
{
    public int $id;
    public int $challenge_id;
    public int $user_id;
    public int $points_awarded;
    public int $hints_used;
    public string $solved_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->challenge_id = (int) $row['challenge_id'];
        $this->user_id = (int) $row['user_id'];
        $this->points_awarded = (int) $row['points_awarded'];
        $this->hints_used = (int) ($row['hints_used'] ?? 0);
        $this->solved_at = (string) $row['solved_at'];
    }

    public static function findForUser(int $challengeId, int $userId): ?self
    {
        $row = self::fetch(
            'SELECT * FROM challenge_solves WHERE challenge_id = ? AND user_id = ? LIMIT 1',
            [$challengeId, $userId]
        );
        return $row ? new self($row) : null;
    }

    public static function countForChallenge(int $challengeId): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM challenge_solves WHERE challenge_id = ?',
            [$challengeId]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    /**
     * @param list<int> $challengeIds
     * @return array<int, true>
     */
    public static function solvedSetForUser(int $userId, array $challengeIds): array
    {
        if ($challengeIds === []) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($challengeIds), '?'));
        $params = array_merge([$userId], $challengeIds);
        $rows = self::fetchAll(
            "SELECT challenge_id FROM challenge_solves WHERE user_id = ? AND challenge_id IN ({$ph})",
            $params
        );
        $set = [];
        foreach ($rows as $row) {
            $set[(int) $row['challenge_id']] = true;
        }
        return $set;
    }

    /**
     * @param list<int> $challengeIds
     * @return array<int, int>
     */
    public static function solveCounts(array $challengeIds): array
    {
        if ($challengeIds === []) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($challengeIds), '?'));
        $rows = self::fetchAll(
            "SELECT challenge_id, COUNT(*) AS cnt FROM challenge_solves
             WHERE challenge_id IN ({$ph}) GROUP BY challenge_id",
            $challengeIds
        );
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['challenge_id']] = (int) $row['cnt'];
        }
        return $map;
    }
}
