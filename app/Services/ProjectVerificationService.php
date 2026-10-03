<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class ProjectVerificationService
{
    public static function canVerify(): bool
    {
        return Auth::hasAnyRole(['instructor', 'mentor', 'admin']);
    }

    public static function request(int $projectId, int $userId): void
    {
        $project = ProjectService::find($projectId);
        if ($project === null) {
            throw new InvalidArgumentException('Project not found.');
        }
        ProjectService::requireOwner($project, $userId);
        if (($project['publish_status'] ?? '') !== 'published') {
            throw new InvalidArgumentException('Publish the project before requesting verification.');
        }

        $pending = Database::fetch(
            "SELECT id FROM project_verifications
             WHERE project_id = ? AND status = 'pending' LIMIT 1",
            [$projectId]
        );
        if ($pending !== null) {
            throw new InvalidArgumentException('A verification request is already pending.');
        }

        Database::execute(
            'INSERT INTO project_verifications (project_id, requested_by, status)
             VALUES (?, ?, \'pending\')',
            [$projectId, $userId]
        );

        // Ensure skill evidence exists as pending for project skills
        ProjectService::syncEvidence($projectId);
    }

    public static function approve(int $verificationId, int $verifierId, ?string $note = null): void
    {
        if (!self::canVerify()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $row = Database::fetch('SELECT * FROM project_verifications WHERE id = ? LIMIT 1', [$verificationId]);
        if ($row === null) {
            throw new InvalidArgumentException('Request not found.');
        }
        $project = ProjectService::find((int) $row['project_id']);
        if ($project === null) {
            throw new InvalidArgumentException('Project not found.');
        }
        if ((int) $project['user_id'] === $verifierId) {
            throw new RuntimeException('You cannot verify your own project.', 403);
        }

        Database::execute(
            "UPDATE project_verifications
             SET status = 'verified', verified_by = ?, verified_at = NOW(), verification_note = ?
             WHERE id = ?",
            [$verifierId, $note, $verificationId]
        );

        // Accept related project skill evidence
        Database::execute(
            "UPDATE skill_evidence
             SET status = 'accepted', verified_by = ?, verified_at = NOW(), strength = GREATEST(strength, 5)
             WHERE source_type = 'project' AND source_id = ? AND user_id = ?",
            [$verifierId, (int) $project['id'], (int) $project['user_id']]
        );

        $skills = Database::fetchAll(
            'SELECT skill_id FROM project_skills WHERE project_id = ?',
            [(int) $project['id']]
        );
        foreach ($skills as $s) {
            SkillProgressService::recalculateUserSkill((int) $project['user_id'], (int) $s['skill_id']);
        }

        ActivityLogService::log($verifierId, 'project_verified', 'project', (int) $project['id']);
        NotificationService::create(
            (int) $project['user_id'],
            'project_verified',
            'Project verified',
            '“' . mb_strimwidth((string) $project['title'], 0, 80, '…') . '” was verified.',
            'project',
            (int) $project['id']
        );
    }

    public static function reject(int $verificationId, int $verifierId, string $reason): void
    {
        if (!self::canVerify()) {
            throw new RuntimeException('Forbidden', 403);
        }

        $row = Database::fetch('SELECT * FROM project_verifications WHERE id = ? LIMIT 1', [$verificationId]);
        if ($row === null) {
            throw new InvalidArgumentException('Request not found.');
        }
        $project = ProjectService::find((int) $row['project_id']);
        if ($project === null) {
            throw new InvalidArgumentException('Project not found.');
        }
        if ((int) $project['user_id'] === $verifierId) {
            throw new RuntimeException('You cannot reject your own project.', 403);
        }

        Database::execute(
            "UPDATE project_verifications
             SET status = 'rejected', verified_by = ?, verified_at = NOW(), verification_note = ?
             WHERE id = ?",
            [$verifierId, mb_substr($reason, 0, 500), $verificationId]
        );

        Database::execute(
            "UPDATE skill_evidence
             SET status = 'rejected', verified_by = ?, verified_at = NOW(), verification_note = ?
             WHERE source_type = 'project' AND source_id = ? AND user_id = ? AND status = 'pending'",
            [$verifierId, mb_substr($reason, 0, 500), (int) $project['id'], (int) $project['user_id']]
        );

        ActivityLogService::log($verifierId, 'project_verified', 'project', (int) $project['id'], [
            'result' => 'rejected',
        ]);
        NotificationService::create(
            (int) $project['user_id'],
            'project_rejected',
            'Project verification declined',
            '“' . mb_strimwidth((string) $project['title'], 0, 80, '…') . '” was not verified.',
            'project',
            (int) $project['id']
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function pendingList(): array
    {
        return Database::fetchAll(
            "SELECT pv.*, p.title, p.slug, u.username AS owner_username, ru.username AS requester_username
             FROM project_verifications pv
             INNER JOIN projects p ON p.id = pv.project_id
             INNER JOIN users u ON u.id = p.user_id
             INNER JOIN users ru ON ru.id = pv.requested_by
             WHERE pv.status = 'pending'
             ORDER BY pv.created_at ASC"
        );
    }
}
