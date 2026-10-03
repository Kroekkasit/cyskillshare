<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class SearchService
{
    /**
     * @return array{
     *   threads: list<array<string, mixed>>,
     *   users: list<array<string, mixed>>,
     *   tags: list<array<string, mixed>>,
     *   projects: list<array<string, mixed>>
     * }
     */
    public static function search(string $query, int $limit = 20): array
    {
        $q = trim($query);
        if ($q === '' || mb_strlen($q) < 2) {
            return ['threads' => [], 'users' => [], 'tags' => [], 'projects' => []];
        }

        $limit = max(1, min(50, $limit));
        $like = '%' . self::escapeLike($q) . '%';

        $threads = Database::fetchAll(
            "SELECT t.id, t.title, t.created_at, t.status, u.username, c.slug AS channel_slug, c.name AS channel_name
             FROM threads t
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN channels c ON c.id = t.channel_id
             WHERE t.deleted_at IS NULL
               AND (t.title LIKE ? OR t.content LIKE ?)
             ORDER BY t.created_at DESC
             LIMIT {$limit}",
            [$like, $like]
        );

        $users = Database::fetchAll(
            "SELECT id, username, full_name, program, year_level
             FROM users
             WHERE status = 'active' AND (username LIKE ? OR full_name LIKE ?)
             ORDER BY username ASC
             LIMIT {$limit}",
            [$like, $like]
        );

        $tags = Database::fetchAll(
            "SELECT id, name, slug,
                    (SELECT COUNT(*) FROM thread_tags tt
                     INNER JOIN threads t ON t.id = tt.thread_id AND t.deleted_at IS NULL
                     WHERE tt.tag_id = tags.id) AS thread_count
             FROM tags
             WHERE name LIKE ? OR slug LIKE ?
             ORDER BY name ASC
             LIMIT {$limit}",
            [$like, $like]
        );

        $projects = Database::fetchAll(
            "SELECT p.id, p.title, p.slug, p.short_description, u.username
             FROM projects p
             INNER JOIN users u ON u.id = p.user_id AND u.status = 'active'
             WHERE p.publish_status = 'published' AND p.visibility = 'public'
               AND (p.title LIKE ? OR p.short_description LIKE ?)
             ORDER BY p.updated_at DESC
             LIMIT {$limit}",
            [$like, $like]
        );

        return [
            'threads' => $threads,
            'users' => $users,
            'tags' => $tags,
            'projects' => $projects,
        ];
    }

    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
