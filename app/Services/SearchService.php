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
     *   projects: list<array<string, mixed>>,
     *   writeups: list<array<string, mixed>>,
     *   knowledge: list<array<string, mixed>>,
     *   labs: list<array<string, mixed>>
     * }
     */
    public static function search(string $query, int $limit = 20): array
    {
        $q = trim($query);
        if ($q === '' || mb_strlen($q) < 2) {
            return ['threads' => [], 'users' => [], 'tags' => [], 'projects' => [], 'writeups' => [], 'knowledge' => [], 'labs' => []];
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

        $writeups = Database::fetchAll(
            "SELECT w.id, w.title, w.slug, w.short_description, u.username
             FROM writeups w
             INNER JOIN users u ON u.id = w.user_id AND u.status = 'active'
             WHERE w.deleted_at IS NULL AND w.status = 'published' AND w.visibility = 'public'
               AND (w.title LIKE ? OR w.short_description LIKE ? OR w.content LIKE ?)
             ORDER BY w.published_at DESC
             LIMIT {$limit}",
            [$like, $like, $like]
        );

        $knowledge = Database::fetchAll(
            "SELECT a.id, a.title, a.slug, a.summary, u.username
             FROM knowledge_articles a
             INNER JOIN users u ON u.id = a.author_id AND u.status = 'active'
             WHERE a.status = 'published' AND a.visibility = 'public'
               AND (a.title LIKE ? OR a.summary LIKE ? OR a.content LIKE ?)
             ORDER BY a.published_at DESC
             LIMIT {$limit}",
            [$like, $like, $like]
        );

        $labs = Database::fetchAll(
            "SELECT l.id, l.title, l.slug, l.short_description, l.difficulty, c.name AS category_name
             FROM labs l
             LEFT JOIN lab_categories c ON c.id = l.category_id
             WHERE l.status = 'published' AND l.visibility = 'public'
               AND (l.title LIKE ? OR l.short_description LIKE ?)
             ORDER BY l.published_at DESC
             LIMIT {$limit}",
            [$like, $like]
        );

        return [
            'threads' => $threads,
            'users' => $users,
            'tags' => $tags,
            'projects' => $projects,
            'writeups' => $writeups,
            'knowledge' => $knowledge,
            'labs' => $labs,
        ];
    }

    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
