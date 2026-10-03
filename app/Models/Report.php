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

    /**
     * @param array<string, mixed> $row
     */
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
}
