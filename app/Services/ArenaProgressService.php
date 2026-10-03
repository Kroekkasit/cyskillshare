<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class ArenaProgressService
{
    /**
     * @return array<string, mixed>
     */
    public static function summary(int $userId): array
    {
        $solved = Database::fetch(
            'SELECT COUNT(*) AS cnt, COALESCE(SUM(points_awarded), 0) AS pts
             FROM challenge_solves WHERE user_id = ?',
            [$userId]
        );
        $attempted = Database::fetch(
            'SELECT COUNT(DISTINCT challenge_id) AS cnt FROM challenge_submissions WHERE user_id = ?',
            [$userId]
        );
        $hints = Database::fetch(
            'SELECT COUNT(*) AS cnt FROM challenge_hint_usage WHERE user_id = ?',
            [$userId]
        );
        $cats = Database::fetch(
            "SELECT COUNT(DISTINCT c.category_id) AS cnt
             FROM challenge_solves s
             INNER JOIN challenges c ON c.id = s.challenge_id
             WHERE s.user_id = ?",
            [$userId]
        );

        return [
            'points' => (int) ($solved['pts'] ?? 0),
            'solved' => (int) ($solved['cnt'] ?? 0),
            'attempted' => (int) ($attempted['cnt'] ?? 0),
            'hints_used' => (int) ($hints['cnt'] ?? 0),
            'categories_practiced' => (int) ($cats['cnt'] ?? 0),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function byCategory(int $userId): array
    {
        return Database::fetchAll(
            "SELECT cat.name, cat.slug,
                    COUNT(c.id) AS total,
                    SUM(CASE WHEN s.id IS NOT NULL THEN 1 ELSE 0 END) AS solved
             FROM challenge_categories cat
             LEFT JOIN challenges c
               ON c.category_id = cat.id AND c.status = 'published' AND c.is_active = 1
             LEFT JOIN challenge_solves s
               ON s.challenge_id = c.id AND s.user_id = ?
             WHERE cat.is_active = 1
             GROUP BY cat.id, cat.name, cat.slug, cat.sort_order
             HAVING total > 0
             ORDER BY cat.sort_order ASC",
            [$userId]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function recentActivity(int $userId, int $limit = 20): array
    {
        $limit = max(1, min(50, $limit));
        // Union-like via PHP merge for simplicity
        $solves = Database::fetchAll(
            "SELECT 'solve' AS kind, c.title, s.points_awarded AS points, s.solved_at AS at, c.id AS challenge_id
             FROM challenge_solves s
             INNER JOIN challenges c ON c.id = s.challenge_id
             WHERE s.user_id = ?
             ORDER BY s.solved_at DESC LIMIT {$limit}",
            [$userId]
        );
        $fails = Database::fetchAll(
            "SELECT 'fail' AS kind, c.title, 0 AS points, sub.submitted_at AS at, c.id AS challenge_id
             FROM challenge_submissions sub
             INNER JOIN challenges c ON c.id = sub.challenge_id
             WHERE sub.user_id = ? AND sub.is_correct = 0
             ORDER BY sub.submitted_at DESC LIMIT {$limit}",
            [$userId]
        );
        $hints = Database::fetchAll(
            "SELECT 'hint' AS kind, c.title, u.penalty_applied AS points, u.revealed_at AS at, c.id AS challenge_id
             FROM challenge_hint_usage u
             INNER JOIN challenges c ON c.id = u.challenge_id
             WHERE u.user_id = ?
             ORDER BY u.revealed_at DESC LIMIT {$limit}",
            [$userId]
        );

        $all = array_merge($solves, $fails, $hints);
        usort($all, static fn(array $a, array $b): int => strcmp((string) $b['at'], (string) $a['at']));
        return array_slice($all, 0, $limit);
    }

    /**
     * @return array<string, mixed>
     */
    public static function categoryPage(string $slug, ?int $userId): array
    {
        $cat = \App\Models\ChallengeCategory::findBySlug($slug);
        if ($cat === null) {
            throw new \InvalidArgumentException('Category not found.', 404);
        }

        $diff = Database::fetchAll(
            "SELECT difficulty, COUNT(*) AS cnt
             FROM challenges
             WHERE category_id = ? AND status = 'published' AND is_active = 1
             GROUP BY difficulty",
            [$cat->id]
        );
        $diffMap = ['easy' => 0, 'medium' => 0, 'hard' => 0, 'expert' => 0];
        foreach ($diff as $row) {
            $diffMap[(string) $row['difficulty']] = (int) $row['cnt'];
        }

        $list = ChallengeService::listPublished([
            'category' => $slug,
            'sort' => 'difficulty',
        ], 1, 48);

        $solved = 0;
        if ($userId !== null) {
            foreach ($list['items'] as $item) {
                if (!empty($item['solved'])) {
                    $solved++;
                }
            }
        }

        return [
            'category' => $cat,
            'total' => $list['total'],
            'solved' => $solved,
            'difficulty' => $diffMap,
            'challenges' => $list['items'],
        ];
    }
}
