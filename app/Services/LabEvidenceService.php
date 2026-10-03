<?php

declare(strict_types=1);

namespace App\Services;

final class LabEvidenceService
{
    public static function recordFromCompletion(int $userId, int $labId): void
    {
        SkillEvidenceService::recordLabEvidence($userId, $labId);
    }
}
