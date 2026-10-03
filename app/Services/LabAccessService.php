<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use RuntimeException;

/**
 * Lab gateway access — authenticated owner only, never exposes orchestrator secrets.
 */
final class LabAccessService
{
    /**
     * @return array{instance:array<string,mixed>,lab:array<string,mixed>,public_env:array<string,string>}
     */
    public static function authorizeGateway(int $instanceId, int $userId, ?string $token = null): array
    {
        $instance = LabInstanceService::requireOwned($instanceId, $userId);
        LabCleanupService::expireIfNeeded($instance);
        $instance = LabInstanceService::find($instanceId) ?? $instance;

        if (($instance['status'] ?? '') !== 'running' || ($instance['provision_state'] ?? '') !== 'ready') {
            throw new RuntimeException('Lab environment is not available.', 409);
        }
        if (LabProgressService::isExpired($instance)) {
            LabCleanupService::destroyInstance($instanceId, 'expired');
            throw new RuntimeException('This lab session has expired.', 410);
        }
        if ($token !== null && $token !== '' && !hash_equals((string) $instance['access_token'], $token)) {
            throw new RuntimeException('Forbidden', 403);
        }

        $lab = LabService::find((int) $instance['lab_id']);
        if ($lab === null) {
            throw new RuntimeException('Lab not found.', 404);
        }

        LabInstanceService::touch($instanceId);

        return [
            'instance' => $instance,
            'lab' => $lab,
            'public_env' => self::publicEnvironment($instance, $lab),
        ];
    }

    /**
     * Student-visible environment facts — never includes unused secret keys beyond needed UI.
     *
     * @param array<string, mixed> $instance
     * @param array<string, mixed> $lab
     * @return array<string, string>
     */
    public static function publicEnvironment(array $instance, array $lab): array
    {
        $secrets = json_decode((string) ($instance['runtime_secrets'] ?? '{}'), true);
        if (!is_array($secrets)) {
            $secrets = [];
        }

        $public = [
            'instance' => (string) $instance['instance_identifier'],
            'environment_type' => (string) ($lab['environment_type'] ?? 'browser'),
        ];

        // Reveal non-flag operational values needed to work the lab
        foreach (['target_ip', 'attacker_ip', 'suspect_host', 'ioc_domain', 'malware_name', 'first_seen_hour',
            'webshell_path', 'compromised_user', 'access_hour', 'bad_process', 'ssh_target_user',
            'world_writable'] as $key) {
            if (isset($secrets[$key])) {
                $public[$key] = (string) $secrets[$key];
            }
        }

        // Flag is only shown in the simulated target after "success" panel — not listed here as flag key
        // Gateway page may show it in a success panel for teaching; still not in lab metadata APIs.

        return $public;
    }

    /**
     * Simulated target payload for gateway rendering.
     *
     * @param array<string, mixed> $instance
     * @param array<string, mixed> $lab
     * @return array<string, mixed>
     */
    public static function simulatedTarget(array $instance, array $lab): array
    {
        $secrets = json_decode((string) ($instance['runtime_secrets'] ?? '{}'), true);
        if (!is_array($secrets)) {
            $secrets = [];
        }
        $slug = (string) $lab['slug'];

        return match ($slug) {
            'vulnerable-web-application' => [
                'kind' => 'web',
                'title' => 'Club Portal (Simulated)',
                'login_path' => '/login',
                'form_fields' => ['username', 'password'],
                'notes' => 'This is a simulated target. Use prepared statements as remediation.',
                'success_flag' => (string) ($secrets['flag'] ?? ''),
                'log_lines' => [
                    ($secrets['attacker_ip'] ?? '10.10.2.1') . ' - - [01/Oct/2026:03:11:02] "POST /login HTTP/1.1" 401',
                    ($secrets['attacker_ip'] ?? '10.10.2.1') . ' - - [01/Oct/2026:03:11:05] "POST /login HTTP/1.1" 200',
                    ($secrets['target_ip'] ?? '10.10.1.1') . ' host target',
                ],
            ],
            'suspicious-network-traffic' => [
                'kind' => 'pcap',
                'title' => 'PCAP Summary (Simulated)',
                'flows' => [
                    ['src' => $secrets['suspect_host'] ?? '', 'dst' => '8.8.8.8', 'proto' => 'DNS', 'note' => 'suspicious'],
                    ['src' => '10.10.3.5', 'dst' => '10.10.3.1', 'proto' => 'HTTPS', 'note' => 'normal'],
                ],
                'ioc' => (string) ($secrets['ioc_domain'] ?? ''),
                'success_flag' => (string) ($secrets['flag'] ?? ''),
            ],
            'compromised-workstation' => [
                'kind' => 'forensics',
                'title' => 'Artifact Browser (Read-only)',
                'artifacts' => [
                    'persistence' => 'run-key',
                    'file' => (string) ($secrets['malware_name'] ?? ''),
                    'first_seen_hour_utc' => (string) ($secrets['first_seen_hour'] ?? ''),
                ],
                'success_flag' => (string) ($secrets['flag'] ?? ''),
            ],
            'web-server-compromise' => [
                'kind' => 'ir',
                'title' => 'IR Console (Simulated)',
                'findings' => [
                    'vector' => 'sql injection',
                    'webshell' => (string) ($secrets['webshell_path'] ?? ''),
                    'user' => (string) ($secrets['compromised_user'] ?? ''),
                    'hour' => (string) ($secrets['access_hour'] ?? ''),
                ],
                'success_flag' => (string) ($secrets['flag'] ?? ''),
            ],
            'linux-security-investigation' => [
                'kind' => 'linux',
                'title' => 'Host Console (Simulated)',
                'ps' => [(string) ($secrets['bad_process'] ?? ''), 'sshd', 'systemd'],
                'auth' => 'Failed password for ' . ($secrets['ssh_target_user'] ?? 'admin'),
                'perms' => (string) ($secrets['world_writable'] ?? ''),
                'success_flag' => (string) ($secrets['flag'] ?? ''),
            ],
            default => [
                'kind' => 'generic',
                'title' => 'Lab Environment',
                'success_flag' => (string) ($secrets['flag'] ?? ''),
                'target_ip' => (string) ($secrets['target_ip'] ?? ''),
            ],
        };
    }
}
