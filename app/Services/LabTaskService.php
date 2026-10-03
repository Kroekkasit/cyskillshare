<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class LabTaskService
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $validation
     * @param list<int> $dependsOn
     */
    public static function create(int $labId, array $data, ?array $validation = null, array $dependsOn = []): array
    {
        if (!LabService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $title = trim((string) ($data['title'] ?? ''));
        $description = (string) ($data['description'] ?? '');
        if ($title === '' || $description === '') {
            throw new InvalidArgumentException('Task title and description are required.');
        }
        $slugBase = trim((string) ($data['slug'] ?? ''));
        if ($slugBase === '') {
            $slugBase = $title;
        }
        $slug = LabService::slugify($slugBase);
        $types = [
            'question','flag','command_output','multiple_choice','file_analysis',
            'log_analysis','configuration','investigation','report','manual_verification',
        ];
        $type = in_array($data['task_type'] ?? '', $types, true) ? (string) $data['task_type'] : 'question';

        Database::execute(
            'INSERT INTO lab_tasks
             (lab_id, title, slug, description, task_type, display_order, required, points, options_json)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $labId,
                $title,
                $slug,
                $description,
                $type,
                (int) ($data['display_order'] ?? 0),
                !empty($data['required']) || !isset($data['required']) ? 1 : 0,
                max(0, min(100, (int) ($data['points'] ?? 10))),
                !empty($data['options_json']) ? json_encode($data['options_json']) : null,
            ]
        );
        $id = (int) Database::lastInsertId();

        if ($validation !== null) {
            self::setValidation($id, $validation);
        }
        self::setDependencies($id, $labId, $dependsOn);

        $row = Database::fetch('SELECT * FROM lab_tasks WHERE id = ?', [$id]);
        if ($row === null) {
            throw new RuntimeException('Task missing after create.');
        }
        return $row;
    }

    /**
     * @param array{validation_type:string,validation_config:array<string,mixed>} $validation
     */
    public static function setValidation(int $taskId, array $validation): void
    {
        if (!LabService::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $type = (string) ($validation['validation_type'] ?? '');
        $allowed = ['exact','regex','flag','instance_secret','multiple_choice','manual'];
        if (!in_array($type, $allowed, true)) {
            throw new InvalidArgumentException('Invalid validation type.');
        }
        $config = $validation['validation_config'] ?? [];
        if (!is_array($config)) {
            throw new InvalidArgumentException('Invalid validation config.');
        }
        // Hash plaintext answers when provided as value
        if (isset($config['value']) && is_string($config['value']) && $config['value'] !== '') {
            $case = (bool) ($config['case_sensitive'] ?? true);
            $config['value_hash'] = LabValidationService::hashAnswer($config['value'], $case);
            unset($config['value']);
        }
        Database::execute(
            'INSERT INTO lab_task_validations (task_id, validation_type, validation_config)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE validation_type = VALUES(validation_type),
               validation_config = VALUES(validation_config)',
            [$taskId, $type, json_encode($config, JSON_UNESCAPED_SLASHES)]
        );
    }

    /** @param list<int> $dependsOnTaskIds */
    public static function setDependencies(int $taskId, int $labId, array $dependsOnTaskIds): void
    {
        Database::execute('DELETE FROM lab_task_dependencies WHERE task_id = ?', [$taskId]);
        foreach ($dependsOnTaskIds as $depId) {
            $depId = (int) $depId;
            if ($depId <= 0 || $depId === $taskId) {
                continue;
            }
            $dep = Database::fetch(
                'SELECT id FROM lab_tasks WHERE id = ? AND lab_id = ? LIMIT 1',
                [$depId, $labId]
            );
            if ($dep === null) {
                continue;
            }
            if (self::wouldCreateCycle($taskId, $depId)) {
                throw new InvalidArgumentException('Task dependency would create a cycle.');
            }
            Database::execute(
                'INSERT IGNORE INTO lab_task_dependencies (task_id, depends_on_task_id) VALUES (?, ?)',
                [$taskId, $depId]
            );
        }
    }

    private static function wouldCreateCycle(int $taskId, int $dependsOn): bool
    {
        // If dependsOn eventually depends on taskId, cycle.
        $queue = [$dependsOn];
        $seen = [];
        while ($queue !== []) {
            $current = array_shift($queue);
            if ($current === $taskId) {
                return true;
            }
            if (isset($seen[$current])) {
                continue;
            }
            $seen[$current] = true;
            $rows = Database::fetchAll(
                'SELECT depends_on_task_id FROM lab_task_dependencies WHERE task_id = ?',
                [$current]
            );
            foreach ($rows as $r) {
                $queue[] = (int) $r['depends_on_task_id'];
            }
        }
        return false;
    }

    /** @return list<array<string, mixed>> */
    public static function forLabAdmin(int $labId): array
    {
        return Database::fetchAll(
            'SELECT t.*, v.validation_type
             FROM lab_tasks t
             LEFT JOIN lab_task_validations v ON v.task_id = t.id
             WHERE t.lab_id = ?
             ORDER BY t.display_order, t.id',
            [$labId]
        );
    }
}
