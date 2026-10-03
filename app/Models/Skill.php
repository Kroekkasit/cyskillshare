<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class Skill extends Model
{
    public int $id;
    public int $category_id;
    public ?int $parent_skill_id;
    public string $name;
    public string $slug;
    public string $description;
    public ?string $icon;
    public int $display_order;
    public bool $is_active;
    public bool $is_gated;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->category_id = (int) $row['category_id'];
        $this->parent_skill_id = isset($row['parent_skill_id']) && $row['parent_skill_id'] !== null
            ? (int) $row['parent_skill_id']
            : null;
        $this->name = (string) $row['name'];
        $this->slug = (string) $row['slug'];
        $this->description = (string) $row['description'];
        $this->icon = isset($row['icon']) ? (string) $row['icon'] : null;
        $this->display_order = (int) ($row['display_order'] ?? 0);
        $this->is_active = (bool) ($row['is_active'] ?? 1);
        $this->is_gated = (bool) ($row['is_gated'] ?? 0);
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM skills WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch(
            'SELECT * FROM skills WHERE slug = ? AND is_active = 1 LIMIT 1',
            [$slug]
        );
        return $row ? new self($row) : null;
    }

    public static function findActive(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM skills WHERE id = ? AND is_active = 1 LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function childrenOf(?int $parentId): array
    {
        if ($parentId === null) {
            $rows = self::fetchAll(
                'SELECT * FROM skills WHERE parent_skill_id IS NULL AND is_active = 1
                 ORDER BY display_order ASC, name ASC'
            );
        } else {
            $rows = self::fetchAll(
                'SELECT * FROM skills WHERE parent_skill_id = ? AND is_active = 1
                 ORDER BY display_order ASC, name ASC',
                [$parentId]
            );
        }
        return array_map(static fn(array $r): self => new self($r), $rows);
    }

    /**
     * @return list<self>
     */
    public static function byCategory(int $categoryId): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM skills WHERE category_id = ? AND is_active = 1
             ORDER BY display_order ASC, name ASC',
            [$categoryId]
        );
        return array_map(static fn(array $r): self => new self($r), $rows);
    }

    public static function slugify(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: 'skill';
    }

    /**
     * @return list<array{id:int,name:string,slug:string,minimum_level:int}>
     */
    public function prerequisites(): array
    {
        return self::fetchAll(
            'SELECT s.id, s.name, s.slug, sp.minimum_level
             FROM skill_prerequisites sp
             INNER JOIN skills s ON s.id = sp.prerequisite_skill_id
             WHERE sp.skill_id = ?
             ORDER BY s.name',
            [$this->id]
        );
    }
}
