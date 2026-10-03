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

    /**
     * @param array<string, mixed> $row
     */
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
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM threads WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function byChannel(int $channelId, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));

        return self::fetchAll(
            "SELECT t.*, u.username
             FROM threads t
             INNER JOIN users u ON u.id = t.user_id
             WHERE t.channel_id = ?
             ORDER BY t.is_pinned DESC, t.created_at DESC
             LIMIT {$limit}",
            [$channelId]
        );
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

    public function incrementViews(): void
    {
        self::execute('UPDATE threads SET views = views + 1 WHERE id = ?', [$this->id]);
        $this->views++;
    }
}
