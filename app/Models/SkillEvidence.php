<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class SkillEvidence extends Model
{
    public int $id;
    public int $user_id;
    public int $skill_id;
    public string $evidence_type;
    public string $source_type;
    public ?int $source_id;
    public string $title;
    public ?string $description;
    public int $strength;
    public string $status;
    public ?int $verified_by;
    public ?string $verified_at;
    public ?string $verification_note;
    public string $created_at;

    /** @param array<string, mixed> $row */
    public function __construct(array $row)
    {
        $this->id = (int) $row['id'];
        $this->user_id = (int) $row['user_id'];
        $this->skill_id = (int) $row['skill_id'];
        $this->evidence_type = (string) $row['evidence_type'];
        $this->source_type = (string) $row['source_type'];
        $this->source_id = isset($row['source_id']) && $row['source_id'] !== null
            ? (int) $row['source_id']
            : null;
        $this->title = (string) $row['title'];
        $this->description = isset($row['description']) ? (string) $row['description'] : null;
        $this->strength = (int) $row['strength'];
        $this->status = (string) $row['status'];
        $this->verified_by = isset($row['verified_by']) && $row['verified_by'] !== null
            ? (int) $row['verified_by']
            : null;
        $this->verified_at = isset($row['verified_at']) ? (string) $row['verified_at'] : null;
        $this->verification_note = isset($row['verification_note']) ? (string) $row['verification_note'] : null;
        $this->created_at = (string) $row['created_at'];
    }

    public static function find(int $id): ?self
    {
        $row = self::fetch('SELECT * FROM skill_evidence WHERE id = ? LIMIT 1', [$id]);
        return $row ? new self($row) : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function forUserSkill(int $userId, int $skillId, string $status = 'accepted'): array
    {
        return self::fetchAll(
            'SELECT * FROM skill_evidence
             WHERE user_id = ? AND skill_id = ? AND status = ?
             ORDER BY created_at DESC',
            [$userId, $skillId, $status]
        );
    }
}
