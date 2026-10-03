<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use InvalidArgumentException;
use RuntimeException;

final class LabHintService
{
    /**
     * @return array{title:string,content:string,penalty:int,already:bool}
     */
    public static function reveal(array $instance, int $hintId, int $userId): array
    {
        if (($instance['status'] ?? '') !== 'running') {
            throw new RuntimeException('Lab is not running.', 409);
        }

        $max = (int) config('labs.hint_max_per_minute', 20);
        if (!RateLimiter::attempt($userId, 'lab_hint_rl', $max, 60)) {
            throw new RuntimeException('Too many hint requests. Please wait.', 429);
        }
        RateLimiter::hit($userId, 'lab_hint_rl');

        $hint = Database::fetch(
            'SELECT h.*, t.lab_id FROM lab_task_hints h
             INNER JOIN lab_tasks t ON t.id = h.task_id
             WHERE h.id = ? LIMIT 1',
            [$hintId]
        );
        if ($hint === null || (int) $hint['lab_id'] !== (int) $instance['lab_id']) {
            throw new InvalidArgumentException('Hint not found.', 404);
        }

        $progress = Database::fetch(
            'SELECT status FROM lab_progress WHERE instance_id = ? AND task_id = ? LIMIT 1',
            [(int) $instance['id'], (int) $hint['task_id']]
        );
        if ($progress === null || ($progress['status'] ?? '') === 'locked') {
            throw new RuntimeException('Hints unlock with the related task.', 403);
        }

        $existing = Database::fetch(
            'SELECT id, penalty_applied FROM lab_hint_usage WHERE instance_id = ? AND hint_id = ? LIMIT 1',
            [(int) $instance['id'], $hintId]
        );
        if ($existing !== null) {
            return [
                'title' => (string) $hint['title'],
                'content' => (string) $hint['content'],
                'penalty' => (int) $existing['penalty_applied'],
                'already' => true,
            ];
        }

        $penalty = (int) $hint['penalty'];
        Database::execute(
            'INSERT INTO lab_hint_usage (instance_id, hint_id, user_id, penalty_applied) VALUES (?, ?, ?, ?)',
            [(int) $instance['id'], $hintId, $userId, $penalty]
        );
        Database::execute(
            'UPDATE lab_instances SET hints_used = hints_used + 1, last_activity_at = NOW() WHERE id = ?',
            [(int) $instance['id']]
        );
        ActivityLogService::log($userId, 'hint_used', 'lab_hint', $hintId, [
            'instance_id' => (int) $instance['id'],
            'penalty' => $penalty,
        ]);

        return [
            'title' => (string) $hint['title'],
            'content' => (string) $hint['content'],
            'penalty' => $penalty,
            'already' => false,
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function availableForTask(int $taskId, int $instanceId): array
    {
        $hints = Database::fetchAll(
            'SELECT id, title, hint_level, penalty FROM lab_task_hints
             WHERE task_id = ? ORDER BY hint_level, id',
            [$taskId]
        );
        $used = Database::fetchAll(
            'SELECT hint_id FROM lab_hint_usage WHERE instance_id = ?',
            [$instanceId]
        );
        $usedMap = [];
        foreach ($used as $u) {
            $usedMap[(int) $u['hint_id']] = true;
        }
        foreach ($hints as &$h) {
            $h['revealed'] = isset($usedMap[(int) $h['id']]);
        }
        unset($h);
        return $hints;
    }
}
