<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use RuntimeException;

final class PeopleDiscoveryService
{
    /**
     * @param array<string, mixed> $filters
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function search(array $filters, int $page = 1, int $perPage = 12, ?int $viewerId = null): array
    {
        if ($viewerId !== null) {
            $max = (int) config('collaboration.people_search_max_per_minute', 30);
            if (!RateLimiter::attempt($viewerId, 'people_search_rl', $max, 60)) {
                throw new RuntimeException('Too many searches. Please wait.', 429);
            }
            RateLimiter::hit($viewerId, 'people_search_rl');
        }

        $page = max(1, $page);
        $perPage = max(1, min(40, $perPage));
        $where = ["u.status = 'active'", 'u.show_in_discovery = 1'];
        $params = [];

        if ($viewerId !== null) {
            $where[] = 'u.id <> ?';
            $params[] = $viewerId;
            $blocked = UserBlockService::blockedIdsFor($viewerId);
            if ($blocked !== []) {
                $in = implode(',', array_fill(0, count($blocked), '?'));
                $where[] = "u.id NOT IN ({$in})";
                foreach ($blocked as $b) {
                    $params[] = $b;
                }
            }
        }

        if (!empty($filters['q'])) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], (string) $filters['q']) . '%';
            $where[] = '(u.username LIKE ? OR u.full_name LIKE ? OR u.bio LIKE ?)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $join = '';
        if (!empty($filters['skill'])) {
            $join .= ' INNER JOIN user_skills usf ON usf.user_id = u.id
                       INNER JOIN skills sf ON sf.id = usf.skill_id AND sf.slug = ?';
            $params[] = (string) $filters['skill'];
            if (!empty($filters['min_level'])) {
                $where[] = 'usf.current_level >= ?';
                $params[] = max(1, min(5, (int) $filters['min_level']));
            }
        }

        if (($filters['looking_for'] ?? '') === 'mentor') {
            $join .= ' INNER JOIN mentors ment ON ment.user_id = u.id AND ment.accepting_requests = 1
                       AND ment.verification_status <> \'suspended\'';
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) (Database::fetch(
            "SELECT COUNT(DISTINCT u.id) AS c FROM users u {$join} WHERE {$whereSql}",
            $params
        )['c'] ?? 0);

        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT DISTINCT u.id, u.username, u.full_name, u.bio,
                    EXISTS(SELECT 1 FROM mentors m2 WHERE m2.user_id = u.id AND m2.accepting_requests = 1) AS is_mentor
             FROM users u
             {$join}
             WHERE {$whereSql}
             ORDER BY u.username
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $viewerSkills = [];
        if ($viewerId !== null) {
            $viewerSkills = Database::fetchAll(
                'SELECT s.slug, s.name FROM user_skills us
                 INNER JOIN skills s ON s.id = us.skill_id
                 WHERE us.user_id = ? AND us.current_level > 0',
                [$viewerId]
            );
        }
        $viewerSlugs = array_column($viewerSkills, 'slug');

        foreach ($items as &$item) {
            $item['skills'] = Database::fetchAll(
                'SELECT s.name, s.slug, us.current_level
                 FROM user_skills us
                 INNER JOIN skills s ON s.id = us.skill_id
                 WHERE us.user_id = ? AND us.current_level > 0
                 ORDER BY us.current_level DESC, s.name
                 LIMIT 5',
                [(int) $item['id']]
            );
            $shared = [];
            foreach ($item['skills'] as $sk) {
                if (in_array($sk['slug'], $viewerSlugs, true)) {
                    $shared[] = (string) $sk['name'];
                }
            }
            $reasons = [];
            if ($shared !== []) {
                $reasons[] = 'You share interests in ' . implode(', ', array_slice($shared, 0, 3)) . '.';
            }
            if ((int) $item['is_mentor'] === 1) {
                $reasons[] = 'Available as a mentor.';
            }
            if ($reasons === [] && !empty($filters['skill'])) {
                $reasons[] = 'Matches your skill filter.';
            }
            $item['reasons'] = $reasons;
        }
        unset($item);

        return ['items' => $items, 'total' => $total, 'page' => $page];
    }

    public static function setDiscovery(int $userId, bool $enabled): void
    {
        Database::execute(
            'UPDATE users SET show_in_discovery = ? WHERE id = ?',
            [$enabled ? 1 : 0, $userId]
        );
    }
}
