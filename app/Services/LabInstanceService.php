<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use App\Services\Lab\LabOrchestratorInterface;
use App\Services\Lab\SimulatedLabOrchestrator;
use InvalidArgumentException;
use RuntimeException;

final class LabInstanceService
{
    public static function orchestrator(): LabOrchestratorInterface
    {
        // Future: switch on config('labs.orchestrator') === 'http'
        return new SimulatedLabOrchestrator();
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM lab_instances WHERE id = ? LIMIT 1', [$id]);
    }

    public static function requireOwned(int $instanceId, int $userId): array
    {
        $instance = self::find($instanceId);
        if ($instance === null) {
            throw new InvalidArgumentException('Lab instance not found.', 404);
        }
        if ((int) $instance['user_id'] !== $userId && !LabService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    public static function start(int $userId, array $lab): array
    {
        if (($lab['status'] ?? '') !== 'published') {
            throw new InvalidArgumentException('Lab is not available.');
        }
        if (!ContentVisibilityService::canView((string) $lab['visibility'], (int) $lab['author_id'], $userId, 'published')) {
            throw new RuntimeException('Forbidden', 403);
        }

        self::enforcePrerequisites($lab, $userId);

        $maxUser = (int) config('labs.max_active_instances_per_user', 2);
        $activeUser = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM lab_instances
             WHERE user_id = ? AND status IN ('queued','provisioning','running','paused')",
            [$userId]
        )['c'] ?? 0);
        if ($activeUser >= $maxUser) {
            throw new RuntimeException('You already have the maximum number of active labs. Stop one before starting another.', 429);
        }

        $maxTotal = (int) config('labs.max_total_active_instances', 100);
        $activeTotal = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM lab_instances
             WHERE status IN ('queued','provisioning','running','paused')"
        )['c'] ?? 0);
        if ($activeTotal >= $maxTotal) {
            throw new RuntimeException('Lab capacity is currently full. Please try again later.', 503);
        }

        $startMax = (int) config('labs.start_max_per_hour', 10);
        if (!RateLimiter::attempt($userId, 'lab_start_rl', $startMax, 3600)) {
            throw new RuntimeException('Too many lab starts. Please wait and try again.', 429);
        }
        RateLimiter::hit($userId, 'lab_start_rl');

        // Resume existing active instance for same lab
        $existing = Database::fetch(
            "SELECT * FROM lab_instances
             WHERE lab_id = ? AND user_id = ?
               AND status IN ('queued','provisioning','running','paused')
             ORDER BY id DESC LIMIT 1",
            [(int) $lab['id'], $userId]
        );
        if ($existing !== null) {
            return self::refreshProvisioning($existing);
        }

        $identifier = bin2hex(random_bytes(16));
        $accessToken = bin2hex(random_bytes(24));
        $secrets = self::generateSecrets($lab);
        $lifetime = max(15, min(
            (int) config('labs.max_lifetime_minutes', 180),
            (int) ($lab['lifetime_minutes'] ?? config('labs.default_lifetime_minutes', 60))
        ));

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO lab_instances
                 (lab_id, user_id, status, provision_state, instance_identifier, runtime_secrets,
                  access_token, started_at, last_activity_at, expires_at)
                 VALUES (?, ?, \'queued\', \'queued\', ?, ?, ?, NOW(), NOW(), DATE_ADD(NOW(), INTERVAL ? MINUTE))',
                [
                    (int) $lab['id'],
                    $userId,
                    $identifier,
                    json_encode($secrets, JSON_UNESCAPED_SLASHES),
                    $accessToken,
                    $lifetime,
                ]
            );
            $id = (int) Database::lastInsertId();
            LabProgressService::initializeForInstance($id, (int) $lab['id']);
            Database::execute('UPDATE labs SET start_count = start_count + 1 WHERE id = ?', [(int) $lab['id']]);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        $instance = self::find($id);
        if ($instance === null) {
            throw new RuntimeException('Instance missing after create.');
        }

        ActivityLogService::log($userId, 'lab_started', 'lab_instance', $id, [
            'lab_id' => (int) $lab['id'],
        ]);
        NotificationService::create(
            $userId,
            'lab_started',
            'Lab starting',
            '“' . mb_strimwidth((string) $lab['title'], 0, 80, '…') . '” is being prepared.',
            'lab_instance',
            $id
        );

        return self::provision($instance, $lab);
    }

    /**
     * @param array<string, mixed> $instance
     * @param array<string, mixed>|null $lab
     * @return array<string, mixed>
     */
    public static function provision(array $instance, ?array $lab = null): array
    {
        $lab ??= LabService::find((int) $instance['lab_id']);
        if ($lab === null) {
            throw new InvalidArgumentException('Lab not found.');
        }

        $template = Database::fetch(
            'SELECT slug FROM lab_templates WHERE id = ? LIMIT 1',
            [(int) ($lab['template_id'] ?? 0)]
        );
        $templateSlug = (string) ($template['slug'] ?? 'single_web_app');
        $secrets = json_decode((string) ($instance['runtime_secrets'] ?? '{}'), true);
        if (!is_array($secrets)) {
            $secrets = [];
        }

        Database::execute(
            'UPDATE lab_instances SET status = \'provisioning\', provision_state = \'provisioning\' WHERE id = ?',
            [(int) $instance['id']]
        );

        try {
            $result = self::orchestrator()->provision([
                'instance_id' => (int) $instance['id'],
                'instance_identifier' => (string) $instance['instance_identifier'],
                'lab_id' => (int) $lab['id'],
                'template_slug' => $templateSlug,
                'resources' => [
                    'cpu' => (float) $lab['cpu_limit'],
                    'memory_mb' => (int) $lab['memory_mb'],
                    'disk_mb' => (int) $lab['disk_mb'],
                ],
                'allow_internet' => (bool) $lab['allow_internet'],
                'secrets' => array_map('strval', $secrets),
            ]);
            Database::execute(
                'UPDATE lab_instances SET orchestrator_ref = ?, provision_state = ? WHERE id = ?',
                [
                    (string) $result['orchestrator_ref'],
                    (string) $result['provision_state'],
                    (int) $instance['id'],
                ]
            );
        } catch (\Throwable $e) {
            Database::execute(
                'UPDATE lab_instances SET status = \'failed\', provision_state = \'failed\',
                 failure_category = ?, stopped_at = NOW() WHERE id = ?',
                ['provision_error', (int) $instance['id']]
            );
            ActivityLogService::log((int) $instance['user_id'], 'lab_provision_failed', 'lab_instance', (int) $instance['id'], [
                'category' => 'provision_error',
            ]);
            throw new RuntimeException('Lab could not be started. Please try again.', 503);
        }

        $fresh = self::find((int) $instance['id']);
        return self::refreshProvisioning($fresh ?? $instance);
    }

    /**
     * @param array<string, mixed> $instance
     * @return array<string, mixed>
     */
    public static function refreshProvisioning(array $instance): array
    {
        LabCleanupService::expireIfNeeded($instance);
        $instance = self::find((int) $instance['id']) ?? $instance;

        if (!in_array($instance['provision_state'] ?? '', ['queued', 'provisioning'], true)) {
            return $instance;
        }
        $ref = (string) ($instance['orchestrator_ref'] ?? '');
        if ($ref === '') {
            return $instance;
        }

        $status = self::orchestrator()->status($ref);
        if (($status['provision_state'] ?? '') === 'ready') {
            Database::execute(
                "UPDATE lab_instances SET provision_state = 'ready', status = 'running',
                 ready_at = COALESCE(ready_at, NOW()), last_activity_at = NOW()
                 WHERE id = ?",
                [(int) $instance['id']]
            );
            ActivityLogService::log((int) $instance['user_id'], 'lab_ready', 'lab_instance', (int) $instance['id']);
            NotificationService::create(
                (int) $instance['user_id'],
                'lab_ready',
                'Lab ready',
                'Your lab environment is ready.',
                'lab_instance',
                (int) $instance['id']
            );
        } elseif (($status['provision_state'] ?? '') === 'failed') {
            Database::execute(
                "UPDATE lab_instances SET provision_state = 'failed', status = 'failed',
                 failure_category = 'orchestrator_failed', stopped_at = NOW() WHERE id = ?",
                [(int) $instance['id']]
            );
        }

        return self::find((int) $instance['id']) ?? $instance;
    }

    public static function stop(array $instance, int $actorId): void
    {
        if ((int) $instance['user_id'] !== $actorId && !LabService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $ref = (string) ($instance['orchestrator_ref'] ?? '');
        if ($ref !== '') {
            try {
                self::orchestrator()->stop($ref);
                self::orchestrator()->destroy($ref);
            } catch (\Throwable) {
                // continue cleanup
            }
        }
        Database::execute(
            "UPDATE lab_instances SET status = 'stopped', stopped_at = NOW()
             WHERE id = ? AND status NOT IN ('destroyed','completed')",
            [(int) $instance['id']]
        );
        ActivityLogService::log($actorId, 'lab_stopped', 'lab_instance', (int) $instance['id']);
    }

    /**
     * @return array<string, mixed>
     */
    public static function reset(array $instance, int $actorId, array $lab): array
    {
        if ((int) $instance['user_id'] !== $actorId && !LabService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $max = (int) config('labs.reset_max_per_hour', 6);
        if (!RateLimiter::attempt($actorId, 'lab_reset_rl', $max, 3600)) {
            throw new RuntimeException('Too many resets. Please wait and try again.', 429);
        }
        RateLimiter::hit($actorId, 'lab_reset_rl');

        $ref = (string) ($instance['orchestrator_ref'] ?? '');
        if ($ref !== '') {
            try {
                self::orchestrator()->reset($ref);
                self::orchestrator()->destroy($ref);
            } catch (\Throwable) {
            }
        }

        $secrets = self::generateSecrets($lab);
        $lifetime = max(15, min(
            (int) config('labs.max_lifetime_minutes', 180),
            (int) ($lab['lifetime_minutes'] ?? 60)
        ));
        $identifier = bin2hex(random_bytes(16));
        $accessToken = bin2hex(random_bytes(24));

        Database::execute(
            "UPDATE lab_instances SET
               status = 'queued', provision_state = 'queued', instance_identifier = ?,
               runtime_secrets = ?, access_token = ?, orchestrator_ref = NULL,
               failure_category = NULL, started_at = NOW(), ready_at = NULL,
               last_activity_at = NOW(), expires_at = DATE_ADD(NOW(), INTERVAL ? MINUTE),
               stopped_at = NULL, completed_at = NULL, score = NULL, hints_used = 0
             WHERE id = ?",
            [
                $identifier,
                json_encode($secrets, JSON_UNESCAPED_SLASHES),
                $accessToken,
                $lifetime,
                (int) $instance['id'],
            ]
        );

        if ((int) ($lab['reset_task_progress'] ?? 1) === 1) {
            Database::execute('DELETE FROM lab_hint_usage WHERE instance_id = ?', [(int) $instance['id']]);
            Database::execute(
                'DELETE a FROM lab_attempts a
                 INNER JOIN lab_progress p ON p.id = a.progress_id
                 WHERE p.instance_id = ?',
                [(int) $instance['id']]
            );
            Database::execute('DELETE FROM lab_progress WHERE instance_id = ?', [(int) $instance['id']]);
            LabProgressService::initializeForInstance((int) $instance['id'], (int) $lab['id']);
        }

        ActivityLogService::log($actorId, 'lab_reset', 'lab_instance', (int) $instance['id']);
        $fresh = self::find((int) $instance['id']);
        if ($fresh === null) {
            throw new RuntimeException('Instance missing after reset.');
        }
        return self::provision($fresh, $lab);
    }

    public static function touch(int $instanceId): void
    {
        Database::execute(
            'UPDATE lab_instances SET last_activity_at = NOW() WHERE id = ? AND status = \'running\'',
            [$instanceId]
        );
    }

    /**
     * @param array<string, mixed> $lab
     * @return array<string, string>
     */
    public static function generateSecrets(array $lab): array
    {
        $octet = random_int(20, 50);
        $slug = (string) ($lab['slug'] ?? 'lab');
        $flag = 'CSK{' . $slug . '_' . bin2hex(random_bytes(4)) . '}';

        return match ($slug) {
            'vulnerable-web-application' => [
                'target_ip' => '10.10.1.' . $octet,
                'attacker_ip' => '10.10.2.' . random_int(10, 40),
                'flag' => $flag,
            ],
            'suspicious-network-traffic' => [
                'suspect_host' => '10.10.3.' . $octet,
                'ioc_domain' => 'update-' . bin2hex(random_bytes(2)) . '.lab-c2.invalid',
                'flag' => $flag,
            ],
            'compromised-workstation' => [
                'malware_name' => 'svchost-' . bin2hex(random_bytes(2)) . '.exe',
                'first_seen_hour' => str_pad((string) random_int(0, 23), 2, '0', STR_PAD_LEFT),
                'flag' => $flag,
            ],
            'web-server-compromise' => [
                'webshell_path' => '/var/www/html/uploads/' . bin2hex(random_bytes(3)) . '.php',
                'compromised_user' => 'www-data',
                'access_hour' => str_pad((string) random_int(0, 23), 2, '0', STR_PAD_LEFT),
                'flag' => $flag,
            ],
            'linux-security-investigation' => [
                'bad_process' => 'kworker-' . bin2hex(random_bytes(2)),
                'ssh_target_user' => 'admin',
                'world_writable' => '/opt/lab/shared',
                'flag' => $flag,
            ],
            default => [
                'target_ip' => '10.10.9.' . $octet,
                'flag' => $flag,
            ],
        };
    }

    /**
     * @param array<string, mixed> $lab
     */
    private static function enforcePrerequisites(array $lab, int $userId): void
    {
        if (($lab['prerequisite_mode'] ?? 'recommended') !== 'required') {
            return;
        }
        $prereqs = Database::fetchAll(
            'SELECT skill_id, minimum_level FROM lab_prerequisites WHERE lab_id = ?',
            [(int) $lab['id']]
        );
        foreach ($prereqs as $p) {
            $row = Database::fetch(
                'SELECT current_level FROM user_skills WHERE user_id = ? AND skill_id = ? LIMIT 1',
                [$userId, (int) $p['skill_id']]
            );
            $level = (int) ($row['current_level'] ?? 0);
            if ($level < (int) $p['minimum_level']) {
                throw new RuntimeException('This lab requires higher skill progress before starting.', 403);
            }
        }
    }
}
