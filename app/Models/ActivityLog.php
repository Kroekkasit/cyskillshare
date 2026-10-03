<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class ActivityLog extends Model
{
    /**
     * @param array{
     *   user_id: ?int,
     *   action: string,
     *   target_type: ?string,
     *   target_id: ?int,
     *   ip_address: ?string,
     *   user_agent: ?string,
     *   metadata: ?array<string, mixed>
     * } $data
     */
    public static function create(array $data): void
    {
        $metadata = null;
        if ($data['metadata'] !== null) {
            $encoded = json_encode($data['metadata'], JSON_UNESCAPED_UNICODE);
            $metadata = $encoded === false ? null : $encoded;
        }

        self::execute(
            'INSERT INTO activity_logs (user_id, action, target_type, target_id, ip_address, user_agent, metadata)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $data['user_id'],
                $data['action'],
                $data['target_type'],
                $data['target_id'],
                $data['ip_address'],
                $data['user_agent'],
                $metadata,
            ]
        );
    }
}
