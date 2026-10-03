<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Report extends Model
{
    public int $id;
    public int $reporter_id;
    public string $target_type;
    public int $target_id;
    public string $reason;
    public ?string $description;
    public string $status;
    public ?int $reviewed_by;
    public ?string $reviewed_at;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->reporter_id = (int) $row['reporter_id'];
        $this->target_type = (string) $row['target_type'];
        $this->target_id = (int) $row['target_id'];
        $this->reason = (string) $row['reason'];
        $this->description = $row['description'] !== null ? (string) $row['description'] : null;
        $this->status = (string) $row['status'];
        $this->reviewed_by = $row['reviewed_by'] !== null ? (int) $row['reviewed_by'] : null;
        $this->reviewed_at = $row['reviewed_at'] !== null ? (string) $row['reviewed_at'] : null;
        $this->created_at = (string) $row['created_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM reports WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function existsPending(int $reporterId, string $targetType, int $targetId): bool
    {
        $row = self::fetch(
            "SELECT id FROM reports
             WHERE reporter_id = ? AND target_type = ? AND target_id = ? AND status = 'pending'
             LIMIT 1",
            [$reporterId, $targetType, $targetId]
        );
        return $row !== null;
    }

    /**
     * @param array{
     *   reporter_id: int,
     *   target_type: string,
     *   target_id: int,
     *   reason: string,
     *   description?: ?string
     * } $data
     */
    public static function create(array $data): int
    {
        self::execute(
            'INSERT INTO reports (reporter_id, target_type, target_id, reason, description, status)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['reporter_id'],
                $data['target_type'],
                $data['target_id'],
                $data['reason'],
                $data['description'] ?? null,
                'pending',
            ]
        );
        return (int) self::lastInsertId();
    }

    public static function updateStatus(int $id, string $status, int $reviewedBy): void
    {
        self::execute(
            'UPDATE reports SET status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?',
            [$status, $reviewedBy, $id]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function pendingList(int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        return self::fetchAll(
            "SELECT r.*, u.username AS reporter_username
             FROM reports r
             INNER JOIN users u ON u.id = r.reporter_id
             WHERE r.status = 'pending'
             ORDER BY r.created_at ASC
             LIMIT {$limit}"
        );
    }
}
