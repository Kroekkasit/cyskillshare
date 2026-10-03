<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Role extends Model
{
    public int $id;
    public string $name;
    public ?string $description;
    public string $created_at;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->name = (string) $row['name'];
        $this->description = $row['description'] !== null ? (string) $row['description'] : null;
        $this->created_at = (string) $row['created_at'];
    }

    public static function findByName(string $name): ?self
    {
        $row = self::fetch('SELECT * FROM roles WHERE name = ? LIMIT 1', [$name]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function all(): array
    {
        $rows = self::fetchAll('SELECT * FROM roles ORDER BY id');
        return array_map(static fn(array $row): self => new self($row), $rows);
    }
}
