<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ChallengeFile extends Model
{
    public int $id;
    public int $challenge_id;
    public string $original_name;
    public string $stored_name;
    public string $storage_path;
    public int $file_size;
    public string $mime_type;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->challenge_id = (int) $row['challenge_id'];
        $this->original_name = (string) $row['original_name'];
        $this->stored_name = (string) $row['stored_name'];
        $this->storage_path = (string) $row['storage_path'];
        $this->file_size = (int) $row['file_size'];
        $this->mime_type = (string) $row['mime_type'];
        $this->created_at = (string) $row['created_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM challenge_files WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    public static function findForChallenge(int $fileId, int $challengeId): ?self
    {
        $row = self::fetch(
            'SELECT * FROM challenge_files WHERE id = ? AND challenge_id = ? LIMIT 1',
            [$fileId, $challengeId]
        );
        return $row ? new self($row) : null;
    }

    /**
     * @return list<self>
     */
    public static function forChallenge(int $challengeId): array
    {
        $rows = self::fetchAll(
            'SELECT * FROM challenge_files WHERE challenge_id = ? ORDER BY id ASC',
            [$challengeId]
        );
        return array_map(static fn(array $r): self => new self($r), $rows);
    }
}
