<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ArenaEvent extends Model
{
    public int $id;
    public string $name;
    public string $slug;
    public string $description;
    public string $event_type;
    public string $status;
    public string $visibility;
    public string $start_at;
    public string $end_at;
    public int $created_by;
    public string $created_at;
    public string $updated_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->description = (string) $row['description'];
        $this->event_type = (string) $row['event_type'];
        $this->status = (string) $row['status'];
        $this->visibility = (string) ($row['visibility'] ?? 'public');
        $this->start_at = (string) $row['start_at'];
        $this->end_at = (string) $row['end_at'];
        $this->created_by = (int) $row['created_by'];
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM arena_events WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findVisible(int $id): ?self
    {
        $row = self::fetch(
            "SELECT * FROM arena_events
             WHERE id = ? AND status IN ('upcoming','active','ended') AND visibility = 'public'
             LIMIT 1",
            [$id]
        );
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function listPublic(): array
    {
        $rows = self::fetchAll(
            "SELECT * FROM arena_events
             WHERE visibility = 'public' AND status IN ('upcoming','active','ended')
             ORDER BY
               FIELD(status, 'active', 'upcoming', 'ended'),
               start_at DESC"
        );
        return array_map(static fn(array $r): self => new self($r), $rows);
    }

    /**
     * @return list<int>
     */
    public function challengeIds(): array
    {
        $rows = self::fetchAll(
            'SELECT challenge_id FROM arena_event_challenges WHERE event_id = ?',
            [$this->id]
        );
        return array_map(static fn(array $r): int => (int) $r['challenge_id'], $rows);
    }

    public function isParticipable(): bool
    {
        return $this->status === 'active' && $this->visibility === 'public';
    }
}
