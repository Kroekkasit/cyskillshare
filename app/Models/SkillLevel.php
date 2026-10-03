<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class SkillLevel extends Model
{
    public int $id;
    public int $level;
    public string $name;
    public ?string $description;
    public int $minimum_score;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->level = (int) $row['level'];
        $this->name = (string) $row['name'];
        $this->description = isset($row['description']) ? (string) $row['description'] : null;
        $this->minimum_score = (int) ($row['minimum_score'] ?? 0);
    }

    /**
     * @return array<int, self> keyed by level number
     */
    public static function mapByLevel(): array
    {
        $rows = self::fetchAll('SELECT * FROM skill_levels ORDER BY level ASC');
        $map = [];
        foreach ($rows as $row) {
            $lvl = new self($row);
            $map[$lvl->level] = $lvl;
        }
        return $map;
    }

    public static function nameFor(int $level): string
    {
        $row = self::fetch('SELECT name FROM skill_levels WHERE level = ? LIMIT 1', [$level]);
        return $row ? (string) $row['name'] : 'Unknown';
    }
}
