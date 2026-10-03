<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Thread extends Model
{
    public int $id;
    public int $channel_id;
    public int $user_id;
    public string $title;
    public string $content;
    public string $status;
    public bool $is_pinned;
    public bool $is_locked;
    public int $views;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->channel_id = (int) $row['channel_id'];
        $this->user_id = (int) $row['user_id'];
        $this->title = (string) $row['title'];
        $this->content = (string) $row['content'];
        $this->status = (string) $row['status'];
        $this->is_pinned = (bool) $row['is_pinned'];
        $this->is_locked = (bool) $row['is_locked'];
        $this->views = (int) $row['views'];
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
        $this->deleted_at = isset($row['deleted_at']) && $row['deleted_at'] !== null
            ? (string) $row['deleted_at']
            : null;
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM threads WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findVisible(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM threads WHERE id = ? AND deleted_at IS NULL LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @param array{channel_id: int, user_id: int, title: string, content: string} $data
     */
    public static function create(array $data): self
    {
        self::execute(
            'INSERT INTO threads (channel_id, user_id, title, content, status)
             VALUES (?, ?, ?, ?, ?)',
            [
                $data['channel_id'],
                $data['user_id'],
                $data['title'],
                $data['content'],
                'open',
            ]
        );

        $thread = self::find((int) self::lastInsertId());
        if ($thread === null) {
            throw new \RuntimeException('Failed to create thread.');
        }
        return $thread;
    }

    /**
     * @param array<string, int|string> $fields
     */
    public static function updateFields(int $id, array $fields): void
    {
        $allowed = ['channel_id', 'title', 'content', 'status', 'is_pinned', 'is_locked'];
        $sets = [];
        $params = [];
        foreach ($fields as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }
            $sets[] = "`{$key}` = ?";
            $params[] = $value;
        }
        if ($sets === []) {
            return;
        }
        $params[] = $id;
        self::execute('UPDATE threads SET ' . implode(', ', $sets) . ' WHERE id = ?', $params);
    }

    public static function softDelete(int $id): void
    {
        self::execute('UPDATE threads SET deleted_at = NOW() WHERE id = ?', [$id]);
    }

    public static function clearBestAnswer(int $threadId): void
    {
        self::execute(
            'UPDATE replies SET is_best_answer = 0 WHERE thread_id = ? AND is_best_answer = 1',
            [$threadId]
        );
    }

    public function incrementViews(): void
    {
        self::execute('UPDATE threads SET views = views + 1 WHERE id = ?', [$this->id]);
        $this->views++;
    }

    /**
     * Unanswered = zero non-deleted replies.
     *
     * @return array{items: list<array<string, mixed>>, total: int, page: int, per_page: int}
     */
    public static function paginate(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = ['t.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['channel_id'])) {
            $where[] = 't.channel_id = ?';
            $params[] = (int) $filters['channel_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = 't.user_id = ?';
            $params[] = (int) $filters['user_id'];
        }
        if (!empty($filters['tag_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM thread_tags tt WHERE tt.thread_id = t.id AND tt.tag_id = ?)';
            $params[] = (int) $filters['tag_id'];
        }
        if (!empty($filters['bookmarked_by'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM bookmarks b
                WHERE b.user_id = ? AND b.target_type = \'thread\' AND b.target_id = t.id
            )';
            $params[] = (int) $filters['bookmarked_by'];
        }

        $sort = (string) ($filters['sort'] ?? 'latest');
        $allowedSorts = ['latest', 'popular', 'unanswered', 'solved', 'mine'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'latest';
        }

        if ($sort === 'solved') {
            $where[] = "t.status = 'solved'";
        } elseif ($sort === 'unanswered') {
            $where[] = '(SELECT COUNT(*) FROM replies r WHERE r.thread_id = t.id AND r.deleted_at IS NULL) = 0';
        } elseif ($sort === 'mine' && !empty($filters['current_user_id'])) {
            $where[] = 't.user_id = ?';
            $params[] = (int) $filters['current_user_id'];
        }

        $whereSql = implode(' AND ', $where);

        $order = match ($sort) {
            'popular' => '(SELECT COALESCE(SUM(CASE WHEN v.vote_type = \'up\' THEN 1 WHEN v.vote_type = \'down\' THEN -1 ELSE 0 END), 0)
                           FROM votes v WHERE v.target_type = \'thread\' AND v.target_id = t.id) DESC, t.created_at DESC',
            'unanswered', 'solved', 'latest', 'mine' => 't.is_pinned DESC, t.created_at DESC',
            default => 't.is_pinned DESC, t.created_at DESC',
        };

        $countRow = self::fetch("SELECT COUNT(*) AS cnt FROM threads t WHERE {$whereSql}", $params);
        $total = (int) ($countRow['cnt'] ?? 0);

        $sql = "SELECT
                    t.*,
                    u.username,
                    u.full_name,
                    u.year_level,
                    u.program,
                    c.name AS channel_name,
                    c.slug AS channel_slug,
                    (SELECT COUNT(*) FROM replies r WHERE r.thread_id = t.id AND r.deleted_at IS NULL) AS reply_count,
                    (SELECT COALESCE(SUM(CASE WHEN v.vote_type = 'up' THEN 1 WHEN v.vote_type = 'down' THEN -1 ELSE 0 END), 0)
                     FROM votes v WHERE v.target_type = 'thread' AND v.target_id = t.id) AS score
                FROM threads t
                INNER JOIN users u ON u.id = t.user_id
                INNER JOIN channels c ON c.id = t.channel_id
                WHERE {$whereSql}
                ORDER BY {$order}
                LIMIT {$perPage} OFFSET {$offset}";

        $items = self::fetchAll($sql, $params);

        // Attach tags in one query
        $ids = array_map(static fn(array $row): int => (int) $row['id'], $items);
        $tagsByThread = Tag::forThreads($ids);
        foreach ($items as &$item) {
            $item['tags'] = $tagsByThread[(int) $item['id']] ?? [];
        }
        unset($item);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public static function countAll(): int
    {
        $row = self::fetch('SELECT COUNT(*) AS cnt FROM threads WHERE deleted_at IS NULL');
        return (int) ($row['cnt'] ?? 0);
    }

    public static function countSolvedSince(string $datetime): int
    {
        $row = self::fetch(
            "SELECT COUNT(*) AS cnt FROM threads
             WHERE deleted_at IS NULL AND status = 'solved' AND updated_at >= ?",
            [$datetime]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function recent(int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));
        return self::fetchAll(
            "SELECT t.id, t.title, t.status, t.created_at, c.name AS channel_name, c.slug AS channel_slug,
                    (SELECT COUNT(*) FROM replies r WHERE r.thread_id = t.id AND r.deleted_at IS NULL) AS reply_count
             FROM threads t
             INNER JOIN channels c ON c.id = t.channel_id
             WHERE t.deleted_at IS NULL
             ORDER BY t.created_at DESC
             LIMIT {$limit}"
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function tags(): array
    {
        return Tag::forThreads([$this->id])[$this->id] ?? [];
    }
}
