<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Category extends Model
{
    public int $id;
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $icon;
    public int $sort_order;
    public bool $is_active;
    public string $created_at;
    public string $updated_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->description = $row['description'] !== null ? (string) $row['description'] : null;
        $this->icon = $row['icon'] !== null ? (string) $row['icon'] : null;
        $this->sort_order = (int) $row['sort_order'];
        $this->is_active = (bool) $row['is_active'];
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
    }

    /**
     * @return list<self>
     */
    public static function allActive(): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, name ASC'
        );
        return array_map(static fn(array $row): self => new self($row), $rows);
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch('SELECT * FROM categories WHERE slug = ? LIMIT 1', [$slug]);
        return $row ? new self($row) : null;
    }
}
