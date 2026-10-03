<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Leaderboard ranking (deterministic tie-break):
 * 1. points DESC
 * 2. solved_count DESC
 * 3. earliest last_solve / achievement timestamp ASC
 */
final class ArenaLeaderboardService
{
    /**
     * @return list<array{rank:int,username:string,solved:int,points:int}>
     */
    public static function global(string $period = 'all', int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        [$from, $to] = self::periodBounds($period);

        $timeSql = '';
        $params = [];
        if ($from !== null) {
            $timeSql .= ' AND s.solved_at >= ?';
            $params[] = $from;
        }
        if ($to !== null) {
            $timeSql .= ' AND s.solved_at <= ?';
            $params[] = $to;
        }

        $rows = Database::fetchAll(
            "SELECT u.username,
                    COUNT(*) AS solved,
                    COALESCE(SUM(s.points_awarded), 0) AS points,
                    MAX(s.solved_at) AS last_solve,
                    MIN(s.solved_at) AS first_solve
             FROM challenge_solves s
             INNER JOIN users u ON u.id = s.user_id AND u.status = 'active'
             WHERE 1=1 {$timeSql}
             GROUP BY u.id, u.username
             ORDER BY points DESC, solved DESC, first_solve ASC, u.username ASC
             LIMIT {$limit}",
            $params
        );

        return self::rankRows($rows);
    }

    /**
     * Event leaderboard — only solves for challenges assigned to the event.
     *
     * @return list<array{rank:int,username:string,solved:int,points:int}>
     */
    public static function forEvent(int $eventId, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $rows = Database::fetchAll(
            "SELECT u.username,
                    COUNT(*) AS solved,
                    COALESCE(SUM(s.points_awarded), 0) AS points,
                    MIN(s.solved_at) AS first_solve
             FROM challenge_solves s
             INNER JOIN arena_event_challenges ec ON ec.challenge_id = s.challenge_id AND ec.event_id = ?
             INNER JOIN users u ON u.id = s.user_id AND u.status = 'active'
             GROUP BY u.id, u.username
             ORDER BY points DESC, solved DESC, first_solve ASC, u.username ASC
             LIMIT {$limit}",
            [$eventId]
        );
        return self::rankRows($rows);
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private static function periodBounds(string $period): array
    {
        return match ($period) {
            'month' => [
                date('Y-m-01 00:00:00'),
                date('Y-m-t 23:59:59'),
            ],
            'semester' => [
                (string) config('arena.semester.start') . ' 00:00:00',
                (string) config('arena.semester.end') . ' 23:59:59',
            ],
            default => [null, null],
        };
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{rank:int,username:string,solved:int,points:int}>
     */
    private static function rankRows(array $rows): array
    {
        $out = [];
        $rank = 1;
        foreach ($rows as $row) {
            $out[] = [
                'rank' => $rank,
                'username' => (string) $row['username'],
                'solved' => (int) $row['solved'],
                'points' => (int) $row['points'],
            ];
            $rank++;
        }
        return $out;
    }
}
