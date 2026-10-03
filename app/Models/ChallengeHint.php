<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ChallengeHint extends Model
{
    public int $id;
    public int $challenge_id;
    public int $hint_order;
    public string $content;
    public int $point_penalty;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->challenge_id = (int) $row['challenge_id'];
        $this->hint_order = (int) $row['hint_order'];
        $this->content = (string) $row['content'];
        $this->point_penalty = (int) $row['point_penalty'];
        $this->created_at = (string) $row['created_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM challenge_hints WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function forChallenge(int $challengeId): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM challenge_hints WHERE challenge_id = ? ORDER BY hint_order ASC, id ASC',
            [$challengeId]
        );
        return array_map(static fn(array $r): self => new self($r), $rows);
    }

    public static function create(int $challengeId, int $order, string $content, int $penalty): self
    {
        self::execute(
            'INSERT INTO challenge_hints (challenge_id, hint_order, content, point_penalty) VALUES (?, ?, ?, ?)',
            [$challengeId, $order, $content, max(0, $penalty)]
        );
        $hint = self::find((int) self::lastInsertId());
        if ($hint === null) {
            throw new \RuntimeException('Failed to create hint.');
        }
        return $hint;
    }

    public static function updateHint(int $id, string $content, int $penalty, int $order): void
    {
        self::execute(
            'UPDATE challenge_hints SET content = ?, point_penalty = ?, hint_order = ? WHERE id = ?',
            [$content, max(0, $penalty), $order, $id]
        );
    }

    public static function delete(int $id): void
    {
        self::execute('DELETE FROM challenge_hints WHERE id = ?', [$id]);
    }
}
