<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use InvalidArgumentException;
use RuntimeException;

final class LabProgressService
{
    public static function initializeForInstance(int $instanceId, int $labId): void
    {
        $tasks = Database::fetchAll(
            'SELECT id FROM lab_tasks WHERE lab_id = ? ORDER BY display_order, id',
            [$labId]
        );
        $deps = Database::fetchAll(
            'SELECT d.task_id, d.depends_on_task_id
             FROM lab_task_dependencies d
             INNER JOIN lab_tasks t ON t.id = d.task_id
             WHERE t.lab_id = ?',
            [$labId]
        );
        $blocked = [];
        foreach ($deps as $d) {
            $blocked[(int) $d['task_id']] = true;
        }

        foreach ($tasks as $task) {
            $tid = (int) $task['id'];
            $status = isset($blocked[$tid]) ? 'locked' : 'available';
            Database::execute(
                'INSERT IGNORE INTO lab_progress (instance_id, task_id, status) VALUES (?, ?, ?)',
                [$instanceId, $tid, $status]
            );
        }
    }

    /** @return list<array<string, mixed>> */
    public static function forInstance(int $instanceId): array
    {
        return Database::fetchAll(
            'SELECT p.*, t.title, t.slug, t.description, t.task_type, t.display_order,
                    t.required, t.points, t.options_json
             FROM lab_progress p
             INNER JOIN lab_tasks t ON t.id = p.task_id
             WHERE p.instance_id = ?
             ORDER BY t.display_order, t.id',
            [$instanceId]
        );
    }

    /**
     * @param array<string, mixed> $instance
     * @return array{result:string,score:int,message:string,completed_lab:bool}
     */
    public static function submit(array $instance, int $taskId, string $answer, int $userId): array
    {
        LabCleanupService::expireIfNeeded($instance);
        $instance = LabInstanceService::find((int) $instance['id']) ?? $instance;
        if (($instance['status'] ?? '') !== 'running' || ($instance['provision_state'] ?? '') !== 'ready') {
            throw new RuntimeException('Lab environment is not ready.', 409);
        }
        if (self::isExpired($instance)) {
            throw new RuntimeException('This lab session has expired.', 410);
        }

        $max = (int) config('labs.submit_max_per_minute', 12);
        if (!RateLimiter::attempt($userId, 'lab_submit_rl', $max, 60)
            || !RateLimiter::attempt($userId, 'lab_submit_rl_' . (int) $instance['id'], $max, 60)
        ) {
            throw new RuntimeException('Too many submissions. Please wait and try again.', 429);
        }
        RateLimiter::hit($userId, 'lab_submit_rl');
        RateLimiter::hit($userId, 'lab_submit_rl_' . (int) $instance['id']);

        $progress = Database::fetch(
            'SELECT p.*, t.points, t.required, t.lab_id, t.slug AS task_slug
             FROM lab_progress p
             INNER JOIN lab_tasks t ON t.id = p.task_id
             WHERE p.instance_id = ? AND p.task_id = ? LIMIT 1',
            [(int) $instance['id'], $taskId]
        );
        if ($progress === null) {
            throw new InvalidArgumentException('Task not found for this lab.', 404);
        }
        if (($progress['status'] ?? '') === 'locked') {
            throw new RuntimeException('Complete prerequisite tasks first.', 403);
        }
        if (($progress['status'] ?? '') === 'completed') {
            return [
                'result' => 'correct',
                'score' => (int) $progress['best_score'],
                'message' => 'Task already completed.',
                'completed_lab' => false,
            ];
        }

        $result = LabValidationService::validate(
            $taskId,
            $answer,
            $instance,
            (int) $progress['points']
        );

        $answerHash = LabValidationService::hashAnswer($answer, true);
        Database::execute(
            'INSERT INTO lab_attempts (progress_id, answer_hash, result, score) VALUES (?, ?, ?, ?)',
            [(int) $progress['id'], $answerHash, $result['result'], $result['score']]
        );
        Database::execute(
            'UPDATE lab_progress SET attempts = attempts + 1, status = ?,
             best_score = GREATEST(best_score, ?),
             completed_at = CASE WHEN ? IN (\'correct\',\'partial\') THEN NOW() ELSE completed_at END,
             updated_at = NOW()
             WHERE id = ?',
            [
                in_array($result['result'], ['correct', 'partial'], true) ? 'completed'
                    : ($result['result'] === 'manual_review' ? 'in_progress' : 'in_progress'),
                $result['score'],
                $result['result'],
                (int) $progress['id'],
            ]
        );

        if (in_array($result['result'], ['correct', 'partial'], true)) {
            Database::execute(
                "UPDATE lab_progress SET status = 'completed' WHERE id = ?",
                [(int) $progress['id']]
            );
            self::unlockDependents((int) $instance['id'], $taskId);
            ActivityLogService::log($userId, 'task_completed', 'lab_task', $taskId, [
                'instance_id' => (int) $instance['id'],
            ]);
        } else {
            ActivityLogService::log($userId, 'attempt_submitted', 'lab_task', $taskId, [
                'instance_id' => (int) $instance['id'],
                'result' => $result['result'],
            ]);
        }

        LabInstanceService::touch((int) $instance['id']);
        $completedLab = self::maybeCompleteLab($instance, $userId);

        return [
            'result' => $result['result'],
            'score' => $result['score'],
            'message' => $result['message'],
            'completed_lab' => $completedLab,
        ];
    }

    public static function unlockDependents(int $instanceId, int $completedTaskId): void
    {
        $dependents = Database::fetchAll(
            'SELECT task_id FROM lab_task_dependencies WHERE depends_on_task_id = ?',
            [$completedTaskId]
        );
        foreach ($dependents as $dep) {
            $taskId = (int) $dep['task_id'];
            $allDeps = Database::fetchAll(
                'SELECT depends_on_task_id FROM lab_task_dependencies WHERE task_id = ?',
                [$taskId]
            );
            $ready = true;
            foreach ($allDeps as $d) {
                $row = Database::fetch(
                    "SELECT status FROM lab_progress WHERE instance_id = ? AND task_id = ? LIMIT 1",
                    [$instanceId, (int) $d['depends_on_task_id']]
                );
                if (($row['status'] ?? '') !== 'completed') {
                    $ready = false;
                    break;
                }
            }
            if ($ready) {
                Database::execute(
                    "UPDATE lab_progress SET status = 'available'
                     WHERE instance_id = ? AND task_id = ? AND status = 'locked'",
                    [$instanceId, $taskId]
                );
            }
        }
    }

    /**
     * @param array<string, mixed> $instance
     */
    public static function maybeCompleteLab(array $instance, int $userId): bool
    {
        $stats = self::completionStats((int) $instance['id']);
        if ($stats['required_total'] === 0 || $stats['required_completed'] < $stats['required_total']) {
            return false;
        }
        if (($instance['status'] ?? '') === 'completed') {
            return true;
        }

        $hintPenalty = (int) (Database::fetch(
            'SELECT COALESCE(SUM(penalty_applied),0) AS p FROM lab_hint_usage WHERE instance_id = ?',
            [(int) $instance['id']]
        )['p'] ?? 0);
        $rawScore = $stats['score_sum'];
        $score = max(0, $rawScore - $hintPenalty);

        Database::beginTransaction();
        try {
            Database::execute(
                "UPDATE lab_instances SET status = 'completed', completed_at = NOW(), score = ?,
                 hints_used = ? WHERE id = ?",
                [$score, $stats['hints_used'], (int) $instance['id']]
            );
            Database::execute(
                'INSERT INTO lab_completions
                 (lab_id, user_id, instance_id, score, required_completed, required_total,
                  optional_completed, hints_used, show_on_portfolio)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE
                   instance_id = VALUES(instance_id),
                   score = VALUES(score),
                   required_completed = VALUES(required_completed),
                   required_total = VALUES(required_total),
                   optional_completed = VALUES(optional_completed),
                   hints_used = VALUES(hints_used),
                   completed_at = CURRENT_TIMESTAMP',
                [
                    (int) $instance['lab_id'],
                    $userId,
                    (int) $instance['id'],
                    $score,
                    $stats['required_completed'],
                    $stats['required_total'],
                    $stats['optional_completed'],
                    $stats['hints_used'],
                ]
            );
            Database::execute(
                'UPDATE labs SET completion_count = (
                   SELECT COUNT(*) FROM lab_completions WHERE lab_id = ?
                 ) WHERE id = ?',
                [(int) $instance['lab_id'], (int) $instance['lab_id']]
            );
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        LabEvidenceService::recordFromCompletion($userId, (int) $instance['lab_id']);
        ActivityLogService::log($userId, 'lab_completed', 'lab', (int) $instance['lab_id'], [
            'instance_id' => (int) $instance['id'],
            'score' => $score,
        ]);
        NotificationService::create(
            $userId,
            'lab_completed',
            'Lab completed',
            'Great work — required tasks are complete. Consider writing a writeup.',
            'lab',
            (int) $instance['lab_id']
        );

        // Stop orchestrator resources but keep completion record
        $ref = (string) ($instance['orchestrator_ref'] ?? '');
        if ($ref !== '') {
            try {
                LabInstanceService::orchestrator()->stop($ref);
            } catch (\Throwable) {
            }
        }

        return true;
    }

    /**
     * @return array{required_total:int,required_completed:int,optional_completed:int,score_sum:int,hints_used:int}
     */
    public static function completionStats(int $instanceId): array
    {
        $rows = Database::fetchAll(
            'SELECT p.status, p.best_score, t.required
             FROM lab_progress p
             INNER JOIN lab_tasks t ON t.id = p.task_id
             WHERE p.instance_id = ?',
            [$instanceId]
        );
        $requiredTotal = 0;
        $requiredCompleted = 0;
        $optionalCompleted = 0;
        $score = 0;
        foreach ($rows as $r) {
            $isRequired = (int) $r['required'] === 1;
            if ($isRequired) {
                $requiredTotal++;
            }
            if (($r['status'] ?? '') === 'completed') {
                $score += (int) $r['best_score'];
                if ($isRequired) {
                    $requiredCompleted++;
                } else {
                    $optionalCompleted++;
                }
            }
        }
        $hints = (int) (Database::fetch(
            'SELECT COUNT(*) AS c FROM lab_hint_usage WHERE instance_id = ?',
            [$instanceId]
        )['c'] ?? 0);

        return [
            'required_total' => $requiredTotal,
            'required_completed' => $requiredCompleted,
            'optional_completed' => $optionalCompleted,
            'score_sum' => $score,
            'hints_used' => $hints,
        ];
    }

    /** @param array<string, mixed> $instance */
    public static function isExpired(array $instance): bool
    {
        if (empty($instance['expires_at'])) {
            return false;
        }
        // Compare in MySQL so PHP timezone ≠ DB timezone cannot expire labs early.
        $row = Database::fetch(
            'SELECT (expires_at IS NOT NULL AND expires_at < NOW()) AS expired
             FROM lab_instances WHERE id = ? LIMIT 1',
            [(int) $instance['id']]
        );
        return (int) ($row['expired'] ?? 0) === 1;
    }

    /** Seconds remaining until expiry (MySQL-authoritative). */
    public static function secondsRemaining(int $instanceId): int
    {
        $row = Database::fetch(
            'SELECT GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), expires_at)) AS secs
             FROM lab_instances WHERE id = ? LIMIT 1',
            [$instanceId]
        );
        return max(0, (int) ($row['secs'] ?? 0));
    }
}
