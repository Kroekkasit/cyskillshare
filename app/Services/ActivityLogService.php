<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;

/**
 * Security-relevant activity logging.
 * Never log passwords, session tokens, CSRF tokens, or other secrets.
 */
final class ActivityLogService
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_hash',
        'password_confirmation',
        'token',
        'csrf',
        '_csrf',
        'session',
        'secret',
        'api_key',
    ];

    /**
     * @param array<string, mixed>|null $metadata
     */
    public static function log(
        ?int $userId,
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?array $metadata = null
    ): void {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua = isset($_SERVER['HTTP_USER_AGENT'])
                ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 512)
                : null;

            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'metadata' => $metadata !== null ? self::sanitize($metadata) : null,
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the request.
            \App\Core\ErrorHandler::log('ActivityLogService failed: ' . $e->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private static function sanitize(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $lower = strtolower((string) $key);
            foreach (self::SENSITIVE_KEYS as $sensitive) {
                if (str_contains($lower, $sensitive)) {
                    $clean[$key] = '[redacted]';
                    continue 2;
                }
            }
            if (is_array($value)) {
                $clean[$key] = self::sanitize($value);
            } else {
                $clean[$key] = $value;
            }
        }
        return $clean;
    }
}
