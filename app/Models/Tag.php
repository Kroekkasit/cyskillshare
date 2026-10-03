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

    /** @param array<string, mixed> $row */
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

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM tags WHERE id = ? LIMIT 1', [$id]);
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

    public static function normalize(string $raw): ?string
    {
        $slug = strtolower(trim($raw));
        $slug = preg_replace('/[\s_]+/', '-', $slug) ?? '';
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug) ?? '';
        $slug = trim($slug, '-');

        if ($slug === '' || mb_strlen($slug) < 2 || mb_strlen($slug) > 30) {
            return null;
        }

        return $slug;
    }

    public static function findOrCreate(string $raw): ?self
    {
        $slug = self::normalize($raw);
        if ($slug === null) {
            return null;
        }

        $existing = self::findBySlug($slug);
        if ($existing !== null) {
            return $existing;
        }

        self::execute('INSERT INTO tags (name, slug) VALUES (?, ?)', [$slug, $slug]);
        return self::find((int) self::lastInsertId());
    }

    /**
     * @param list<string> $rawTags
     */
    public static function syncForThread(int $threadId, array $rawTags): void
    {
        self::execute('DELETE FROM thread_tags WHERE thread_id = ?', [$threadId]);

        $seen = [];
        $count = 0;
        foreach ($rawTags as $raw) {
            if ($count >= 8) {
                break;
            }
            $tag = self::findOrCreate((string) $raw);
            if ($tag === null || isset($seen[$tag->id])) {
                continue;
            }
            self::execute(
                'INSERT INTO thread_tags (thread_id, tag_id) VALUES (?, ?)',
                [$threadId, $tag->id]
            );
            $seen[$tag->id] = true;
            $count++;
        }
    }

    /**
     * @param list<int> $threadIds
     * @return array<int, list<array{id:int,name:string,slug:string}>>
     */
    public static function forThreads(array $threadIds): array
    {
        if ($threadIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($threadIds), '?'));
        $rows = self::fetchAll(
            "SELECT tt.thread_id, t.id, t.name, t.slug
             FROM thread_tags tt
             INNER JOIN tags t ON t.id = tt.tag_id
             WHERE tt.thread_id IN ({$placeholders})
             ORDER BY t.name ASC",
            $threadIds
        );

        $map = [];
        foreach ($rows as $row) {
            $tid = (int) $row['thread_id'];
            $map[$tid][] = [
                'id' => (int) $row['id'],
                'name' => (string) $row['name'],
                'slug' => (string) $row['slug'],
            ];
        }
        return $map;
    }

    public function threadCount(): int
    {
        $row = self::fetch(
            'SELECT COUNT(*) AS cnt
             FROM thread_tags tt
             INNER JOIN threads th ON th.id = tt.thread_id AND th.deleted_at IS NULL
             WHERE tt.tag_id = ?',
            [$this->id]
        );
        return (int) ($row['cnt'] ?? 0);
    }
}
