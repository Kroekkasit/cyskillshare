<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class LabRecommendationService
{
    /** @return list<array<string, mixed>> */
    public static function forUser(?int $userId, int $limit = 4): array
    {
        $limit = max(1, min(12, $limit));
        if ($userId === null) {
            return Database::fetchAll(
                "SELECT l.id, l.title, l.slug, l.difficulty, l.estimated_minutes, l.short_description,
                        c.name AS category_name
                 FROM labs l
                 LEFT JOIN lab_categories c ON c.id = l.category_id
                 WHERE l.status = 'published' AND l.visibility = 'public'
                 ORDER BY l.featured DESC, l.published_at DESC
                 LIMIT {$limit}"
            );
        }

        // Prefer labs matching user's skill interests / progress, excluding completed
        return Database::fetchAll(
            "SELECT l.id, l.title, l.slug, l.difficulty, l.estimated_minutes, l.short_description,
                    c.name AS category_name,
                    COALESCE(SUM(us.current_level), 0) AS skill_affinity
             FROM labs l
             LEFT JOIN lab_categories c ON c.id = l.category_id
             LEFT JOIN lab_skills ls ON ls.lab_id = l.id
             LEFT JOIN user_skills us ON us.skill_id = ls.skill_id AND us.user_id = ?
             LEFT JOIN lab_completions lc ON lc.lab_id = l.id AND lc.user_id = ?
             WHERE l.status = 'published'
               AND l.visibility IN ('public','community')
               AND lc.id IS NULL
             GROUP BY l.id
             ORDER BY skill_affinity DESC, l.featured DESC, l.published_at DESC
             LIMIT {$limit}",
            [$userId, $userId]
        );
    }
}
