<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Channel extends Model
{
    public int $id;
    public int $category_id;
    public string $name;
    public string $slug;
    public ?string $description;
    public string $channel_type;
    public bool $is_private;
    public bool $is_active;
    public int $sort_order;
    public string $created_at;
    public string $updated_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->category_id = (int) $row['category_id'];
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->description = $row['description'] !== null ? (string) $row['description'] : null;
        $this->channel_type = (string) $row['channel_type'];
        $this->is_private = (bool) $row['is_private'];
        $this->is_active = (bool) $row['is_active'];
        $this->sort_order = (int) $row['sort_order'];
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM channels WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch('SELECT * FROM channels WHERE slug = ? AND is_active = 1 LIMIT 1', [$slug]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function allActive(): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM channels WHERE is_active = 1 ORDER BY sort_order ASC, name ASC'
        );
        return array_map(static fn(array $row): self => new self($row), $rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function withCategories(): array
    {
        return self::fetchAll(
            'SELECT ch.*, c.name AS category_name, c.slug AS category_slug
             FROM channels ch
             INNER JOIN categories c ON c.id = ch.category_id
             WHERE ch.is_active = 1 AND c.is_active = 1
             ORDER BY c.sort_order ASC, ch.sort_order ASC'
        );
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public static function groupedForSidebar(): array
    {
        $grouped = [];
        foreach (self::withCategories() as $row) {
            $cat = (string) $row['category_name'];
            $grouped[$cat][] = $row;
        }
        return $grouped;
    }

    public function threadCount(): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt FROM threads WHERE channel_id = ? AND deleted_at IS NULL',
            [$this->id]
        );
        return (int) ($row['cnt'] ?? 0);
    }
}
