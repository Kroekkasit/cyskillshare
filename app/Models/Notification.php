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

    /**
     * @param array<string, mixed> $row
     */
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
     * @return list<self>
     */
    public static function forUser(int $userId, int $limit = 20): array
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
}
