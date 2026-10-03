<?php

declare(strict_types=1);

namespace App\Services\Lab;

use App\Core\Database;

/**
 * Development orchestrator — no Docker, no host escape surface.
 * Simulates async provisioning using lab_instances timestamps.
 */
final class SimulatedLabOrchestrator implements LabOrchestratorInterface
{
    public function provision(array $request): array
    {
        $ref = 'sim-' . $request['instance_identifier'];
        // Persist ready-at target in orchestrator_ref metadata via DB update by caller.
        return [
            'orchestrator_ref' => $ref,
            'provision_state' => 'provisioning',
        ];
    }

    public function status(string $orchestratorRef): array
    {
        $row = Database::fetch(
            'SELECT id, provision_state, status, created_at, ready_at
             FROM lab_instances WHERE orchestrator_ref = ? LIMIT 1',
            [$orchestratorRef]
        );
        if ($row === null) {
            return ['provision_state' => 'failed', 'status' => 'failed', 'detail' => 'missing'];
        }

        if (($row['provision_state'] ?? '') === 'ready') {
            return ['provision_state' => 'ready', 'status' => (string) $row['status'], 'detail' => null];
        }
        if (($row['provision_state'] ?? '') === 'failed') {
            return ['provision_state' => 'failed', 'status' => 'failed', 'detail' => null];
        }

        $delay = max(1, (int) config('labs.simulated_ready_after_seconds', 3));
        $created = strtotime((string) $row['created_at']) ?: time();
        if (time() >= $created + $delay) {
            return ['provision_state' => 'ready', 'status' => 'running', 'detail' => null];
        }

        return ['provision_state' => 'provisioning', 'status' => 'provisioning', 'detail' => null];
    }

    public function stop(string $orchestratorRef): void
    {
        // Idempotent no-op for simulated environments.
    }

    public function destroy(string $orchestratorRef): void
    {
        // Idempotent no-op for simulated environments.
    }

    public function reset(string $orchestratorRef): void
    {
        // Caller recreates secrets; simulated env has no containers to recycle.
    }
}
