<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Reply extends Model
{
    public int $id;
    public int $thread_id;
    public int $user_id;
    public ?int $parent_reply_id;
    public string $content;
    public bool $is_best_answer;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->thread_id = (int) $row['thread_id'];
        $this->user_id = (int) $row['user_id'];
        $this->parent_reply_id = $row['parent_reply_id'] !== null ? (int) $row['parent_reply_id'] : null;
        $this->content = (string) $row['content'];
        $this->is_best_answer = (bool) $row['is_best_answer'];
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
        $this->deleted_at = $row['deleted_at'] !== null ? (string) $row['deleted_at'] : null;
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM replies WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function byThread(int $threadId): array
    {
        return self::fetchAll(
            'SELECT r.*, u.username
             FROM replies r
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.thread_id = ? AND r.deleted_at IS NULL
             ORDER BY r.created_at ASC',
            [$threadId]
        );
    }

    /**
     * @param array{thread_id: int, user_id: int, content: string, parent_reply_id?: ?int} $data
     */
    public static function create(array $data): self
    {
        self::execute(
            'INSERT INTO replies (thread_id, user_id, parent_reply_id, content)
             VALUES (?, ?, ?, ?)',
            [
                $data['thread_id'],
                $data['user_id'],
                $data['parent_reply_id'] ?? null,
                $data['content'],
            ]
        );

        $reply = self::find((int) self::lastInsertId());
        if ($reply === null) {
            throw new \RuntimeException('Failed to create reply.');
        }
        return $reply;
    }

    public function softDelete(): void
    {
        self::execute('UPDATE replies SET deleted_at = NOW() WHERE id = ?', [$this->id]);
        $this->deleted_at = date('Y-m-d H:i:s');
    }
}
