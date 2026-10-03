<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Tag extends Model
{
    public int $id;
    public string $name;
    public string $slug;
    public string $created_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->created_at = (string) $row['created_at'];
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch('SELECT * FROM tags WHERE slug = ? LIMIT 1', [$slug]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function all(): array
    {
        $rows = self::fetchAll('SELECT * FROM tags ORDER BY name');
        return array_map(static fn(array $row): self => new self($row), $rows);
    }
}
