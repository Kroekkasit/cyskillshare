<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Challenge metadata model.
 * Never expose flag_hash through public view data.
 */
final class Challenge extends Model
{
    public int $id;
    public string $title;
    public string $slug;
    public string $description;
    public int $category_id;
    public string $difficulty;
    public int $points;
    public int $author_id;
    public string $status;
    public string $flag_type;
    public bool $case_sensitive;
    public bool $is_active;
    public bool $is_featured;
    public ?string $first_solved_at;
    public string $created_at;
    public string $updated_at;
    public ?string $published_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->title = (string) $row['title'];
        $this->slug = (string) $row['slug'];
        $this->description = (string) $row['description'];
        $this->category_id = (int) $row['category_id'];
        $this->difficulty = (string) $row['difficulty'];
        $this->points = (int) $row['points'];
        $this->author_id = (int) $row['author_id'];
        $this->status = (string) $row['status'];
        $this->flag_type = (string) ($row['flag_type'] ?? 'static');
        $this->case_sensitive = (bool) ($row['case_sensitive'] ?? 1);
        $this->is_active = (bool) ($row['is_active'] ?? 1);
        $this->is_featured = (bool) ($row['is_featured'] ?? 0);
        $this->first_solved_at = isset($row['first_solved_at']) && $row['first_solved_at'] !== null
            ? (string) $row['first_solved_at']
            : null;
        $this->created_at = (string) $row['created_at'];
        $this->updated_at = (string) $row['updated_at'];
        $this->published_at = isset($row['published_at']) && $row['published_at'] !== null
            ? (string) $row['published_at']
            : null;
    }

    private const PUBLIC_COLS = 'id, title, slug, description, category_id, difficulty, points,
        author_id, status, flag_type, case_sensitive, is_active, is_featured,
        first_solved_at, created_at, updated_at, published_at';

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT ' . self::PUBLIC_COLS . ' FROM challenges WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findPublished(int $id): ?self
    {
        $row = self::fetch(
            'SELECT ' . self::PUBLIC_COLS . ' FROM challenges
             WHERE id = ? AND status = ? AND is_active = 1 LIMIT 1',
            [$id, 'published']
        );
        return $row ? new self($row) : null;
    }

    public static function findBySlug(string $slug): ?self
    {
        $row = self::fetch(
            'SELECT ' . self::PUBLIC_COLS . ' FROM challenges WHERE slug = ? LIMIT 1',
            [$slug]
        );
        return $row ? new self($row) : null;
    }

    /**
     * Staff-only: load flag hash for verification/edit checks.
     */
    public static function fetchFlagHash(int $id): ?array
    {
        return self::fetch(
            'SELECT id, flag_hash, case_sensitive, flag_type, status, is_active, points
             FROM challenges WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public function isSolvable(): bool
    {
        return $this->status === 'published' && $this->is_active;
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    public function tags(): array
    {
        return Tag::forChallenges([$this->id])[$this->id] ?? [];
    }

    public static function slugify(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'challenge';
        }
        return mb_substr($slug, 0, 200);
    }

    public static function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug = self::slugify($base);
        $candidate = $slug;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM challenges WHERE slug = ?';
            $params = [$candidate];
            if ($excludeId !== null) {
                $sql .= ' AND id <> ?';
                $params[] = $excludeId;
            }
            $sql .= ' LIMIT 1';
            if (self::fetch($sql, $params) === null) {
                return $candidate;
            }
            $candidate = $slug . '-' . $i;
            $i++;
        }
    }
}
