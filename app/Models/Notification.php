<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Notification extends Model
{
    public int $id;
    public int $user_id;
    public string $type;
    public string $title;
    public ?string $message;
    public ?string $reference_type;
    public ?int $reference_id;
    public bool $is_read;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->user_id = (int) $row['user_id'];
        $this->type = (string) $row['type'];
        $this->title = (string) $row['title'];
        $this->message = $row['message'] !== null ? (string) $row['message'] : null;
        $this->reference_type = $row['reference_type'] !== null ? (string) $row['reference_type'] : null;
        $this->reference_id = $row['reference_id'] !== null ? (int) $row['reference_id'] : null;
        $this->is_read = (bool) $row['is_read'];
        $this->created_at = (string) $row['created_at'];
    }

    /**
     * @param array{
     *   user_id: int,
     *   type: string,
     *   title: string,
     *   message?: ?string,
     *   reference_type?: ?string,
     *   reference_id?: ?int
     * } $data
     */
    public static function create(array $data): void
    {
        self::execute(
            'INSERT INTO notifications (user_id, type, title, message, reference_type, reference_id)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
                $data['type'],
                $data['title'],
                $data['message'] ?? null,
                $data['reference_type'] ?? null,
                $data['reference_id'] ?? null,
            ]
        );
    }

    /**
     * @return list<self>
     */
    public static function forUser(int $userId, int $limit = 30): array
    {
        $limit = max(1, min(100, $limit));
        $rows = self::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}",
            [$userId]
        );
        return array_map(static fn(array $row): self => new self($row), $rows);
    }

    public static function unreadCount(int $userId): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0',
            [$userId]
        );
        return (int) ($row['cnt'] ?? 0);
    }

    public static function markReadForUser(int $userId, int $notificationId): void
    {
        self::execute(
            'UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?',
            [$notificationId, $userId]
        );
    }

    public static function markAllRead(int $userId): void
    {
        self::execute('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0', [$userId]);
    }
}
