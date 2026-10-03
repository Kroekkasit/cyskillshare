<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Idempotent cleanup for expired / abandoned lab instances.
 */
final class LabCleanupService
{
    /**
     * @param array<string, mixed> $instance
     */
    public static function expireIfNeeded(array $instance): void
    {
        if (!in_array($instance['status'] ?? '', ['queued', 'provisioning', 'running', 'paused'], true)) {
            return;
        }
        if (empty($instance['expires_at'])) {
            return;
        }
        if (!LabProgressService::isExpired($instance)) {
            return;
        }
        self::destroyInstance((int) $instance['id'], 'expired');
    }

    public static function destroyInstance(int $instanceId, string $reason = 'destroyed'): void
    {
        $instance = LabInstanceService::find($instanceId);
        if ($instance === null) {
            return;
        }
        if (in_array($instance['status'] ?? '', ['destroyed', 'completed'], true)) {
            return;
        }

        $ref = (string) ($instance['orchestrator_ref'] ?? '');
        if ($ref !== '') {
            try {
                $orch = LabInstanceService::orchestrator();
                $orch->stop($ref);
                $orch->destroy($ref);
            } catch (\Throwable) {
                // continue
            }
        }

        $status = $reason === 'expired' ? 'expired' : 'destroyed';
        Database::execute(
            'UPDATE lab_instances SET status = ?, stopped_at = COALESCE(stopped_at, NOW()) WHERE id = ?',
            [$status, $instanceId]
        );
        ActivityLogService::log(
            (int) $instance['user_id'],
            $reason === 'expired' ? 'lab_expired' : 'lab_destroyed',
            'lab_instance',
            $instanceId
        );

        if ($reason === 'expired') {
            NotificationService::create(
                (int) $instance['user_id'],
                'lab_expired',
                'Lab expired',
                'Your lab session expired. Progress on tasks is preserved for review where applicable.',
                'lab_instance',
                $instanceId
            );
        }
    }

    /** Sweep expired instances (call from admin/cron-style endpoint). */
    public static function sweepExpired(int $limit = 50): int
    {
        $limit = max(1, min(200, $limit));
        $rows = Database::fetchAll(
            "SELECT id FROM lab_instances
             WHERE status IN ('queued','provisioning','running','paused')
               AND expires_at IS NOT NULL AND expires_at < NOW()
             ORDER BY expires_at ASC
             LIMIT {$limit}"
        );
        foreach ($rows as $row) {
            self::destroyInstance((int) $row['id'], 'expired');
        }
        return count($rows);
    }
}
