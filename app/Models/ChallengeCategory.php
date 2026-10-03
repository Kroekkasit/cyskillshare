<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ChallengeCategory extends Model
{
    public int $id;
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $icon;
    public int $sort_order;
    public bool $is_active;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->description = isset($row['description']) ? (string) $row['description'] : null;
        $this->icon = isset($row['icon']) ? (string) $row['icon'] : null;
        $this->sort_order = (int) ($row['sort_order'] ?? 0);
        $this->is_active = (bool) ($row['is_active'] ?? 1);
        $this->created_at = (string) $row['created_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM challenge_categories WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch(
            'SELECT * FROM challenge_categories WHERE slug = ? AND is_active = 1 LIMIT 1',
            [$slug]
        );
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function allActive(): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM challenge_categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC'
        );
        return array_map(static fn(array $r): self => new self($r), $rows);
    }

    /**
     * @return list<self>
     */
    public static function all(): array
    {
        $rows = self::fetchAll('SELECT * FROM challenge_categories ORDER BY sort_order ASC, name ASC');
        return array_map(static fn(array $r): self => new self($r), $rows);
    }
}
