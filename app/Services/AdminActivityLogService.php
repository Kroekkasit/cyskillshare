<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AdminActivityLogService
{
    /**
     * @return array{rows: list<array<string, mixed>>, total: int, page: int, per_page: int, pages: int}
     */
    public static function paginate(
        int $page = 1,
        int $perPage = 40,
        ?string $action = null,
        ?string $q = null,
        ?int $userId = null
    ): array {
        $page = max(1, $page);
        $perPage = max(10, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if ($action !== null && $action !== '') {
            $where[] = 'a.action = ?';
            $params[] = $action;
        }

        if ($userId !== null && $userId > 0) {
            $where[] = 'a.user_id = ?';
            $params[] = $userId;
        }

        if ($q !== null && trim($q) !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], trim($q)) . '%';
            $where[] = '(u.username LIKE ? OR a.action LIKE ? OR a.target_type LIKE ? OR a.ip_address LIKE ?)';
            array_push($params, $like, $like, $like, $like);
        }

        $sqlWhere = implode(' AND ', $where);

        $countRow = Database::fetch(
            "SELECT COUNT(*) AS cnt
             FROM activity_logs a
             LEFT JOIN users u ON u.id = a.user_id
             WHERE {$sqlWhere}",
            $params
        );
        $total = (int) ($countRow['cnt'] ?? 0);

        $rows = Database::fetchAll(
            "SELECT a.id, a.user_id, a.action, a.target_type, a.target_id,
                    a.ip_address, a.user_agent, a.metadata, a.created_at,
                    u.username
             FROM activity_logs a
             LEFT JOIN users u ON u.id = a.user_id
             WHERE {$sqlWhere}
             ORDER BY a.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        foreach ($rows as &$row) {
            if (isset($row['metadata']) && is_string($row['metadata']) && $row['metadata'] !== '') {
                $decoded = json_decode($row['metadata'], true);
                $row['metadata_decoded'] = is_array($decoded) ? $decoded : null;
            } else {
                $row['metadata_decoded'] = null;
            }
        }
        unset($row);

        return [
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    /**
     * @return list<string>
     */
    public static function distinctActions(int $limit = 80): array
    {
        $rows = Database::fetchAll(
            'SELECT DISTINCT action FROM activity_logs ORDER BY action ASC LIMIT ' . max(1, min(200, $limit))
        );
        return array_map(static fn(array $r): string => (string) $r['action'], $rows);
    }
}
