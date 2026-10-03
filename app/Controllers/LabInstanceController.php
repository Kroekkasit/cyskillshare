<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\LabAccessService;
use App\Services\LabHintService;
use App\Services\LabInstanceService;
use App\Services\LabProgressService;
use App\Services\LabService;
use InvalidArgumentException;
use RuntimeException;

final class LabInstanceController extends Controller
{
    public function show(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');

        $lab = LabService::findBySlug($slug);
        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
        } catch (InvalidArgumentException $e) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Instance Not Found',
                'message' => $e->getMessage(),
            ]);
            return;
        } catch (RuntimeException $e) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        if ((int) $instance['lab_id'] !== (int) $lab['id']) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Instance Not Found',
                'message' => 'This lab instance does not match the lab.',
            ]);
            return;
        }

        $instance = LabInstanceService::refreshProvisioning($instance);
        $tasks = LabProgressService::forInstance($instanceId);

        $hintsByTask = [];
        foreach ($tasks as $task) {
            $hintsByTask[(int) $task['task_id']] = LabHintService::availableForTask(
                (int) $task['task_id'],
                $instanceId
            );
        }

        $revealedHints = Database::fetchAll(
            'SELECT h.id, h.task_id, h.title, h.content, u.penalty_applied
             FROM lab_hint_usage u
             INNER JOIN lab_task_hints h ON h.id = u.hint_id
             WHERE u.instance_id = ?',
            [$instanceId]
        );
        $revealedByTask = [];
        foreach ($revealedHints as $rh) {
            $tid = (int) $rh['task_id'];
            $revealedByTask[$tid][] = $rh;
        }

        $publicEnv = LabAccessService::publicEnvironment($instance, $lab);
        $stats = LabProgressService::completionStats($instanceId);

        $this->view('labs/instance', [
            'title' => (string) $lab['title'] . ' — Lab Session',
            'lab' => $lab,
            'instance' => $this->sanitizeInstance($instance),
            'tasks' => $tasks,
            'hintsByTask' => $hintsByTask,
            'revealedByTask' => $revealedByTask,
            'publicEnv' => $publicEnv,
            'stats' => $stats,
            'pollSeconds' => (int) config('labs.provision_poll_seconds', 2),
        ]);
    }

    public function status(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $instanceId = (int) $request->param('id');

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
        } catch (\Throwable $e) {
            http_response_code((int) ($e->getCode() ?: 403) >= 400 ? (int) $e->getCode() : 403);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        $instance = LabInstanceService::refreshProvisioning($instance);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => (string) ($instance['status'] ?? ''),
            'provision_state' => (string) ($instance['provision_state'] ?? ''),
            'seconds_remaining' => LabProgressService::secondsRemaining((int) $instance['id']),
            'expires_at' => (string) ($instance['expires_at'] ?? ''),
        ]);
    }

    public function stop(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
            LabInstanceService::stop($instance, $userId);
            $this->withSuccess('Lab session stopped.');
        } catch (\Throwable $e) {
            $this->withError($e->getMessage());
        }
        $this->redirect('/labs/' . rawurlencode($slug));
    }

    public function reset(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');

        if ((string) $request->input('confirm', '') !== 'yes') {
            $this->withError('Please confirm the reset.');
            $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . $instanceId);
            return;
        }

        $lab = LabService::findBySlug($slug);
        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
            $fresh = LabInstanceService::reset($instance, $userId, $lab);
            $this->withSuccess('Lab environment reset. Provisioning again…');
            $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . (int) $fresh['id']);
        } catch (RuntimeException $e) {
            if ((int) $e->getCode() === 429) {
                http_response_code(429);
            }
            $this->withError($e->getMessage());
            $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . $instanceId);
        }
    }

    public function complete(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');

        $lab = LabService::findBySlug($slug);
        if ($lab === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Lab Not Found',
                'message' => 'This lab does not exist.',
            ]);
            return;
        }

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
        } catch (\Throwable $e) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        $completion = Database::fetch(
            'SELECT * FROM lab_completions WHERE lab_id = ? AND user_id = ? LIMIT 1',
            [(int) $lab['id'], $userId]
        );
        $stats = LabProgressService::completionStats($instanceId);
        $tasks = LabProgressService::forInstance($instanceId);

        $this->view('labs/complete', [
            'title' => 'Lab Complete — ' . (string) $lab['title'],
            'lab' => $lab,
            'instance' => $this->sanitizeInstance($instance),
            'completion' => $completion,
            'stats' => $stats,
            'tasks' => $tasks,
        ]);
    }

    public function submitTask(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');
        $taskId = (int) $request->param('taskId');
        $answer = trim((string) $request->input('answer', ''));

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
            $result = LabProgressService::submit($instance, $taskId, $answer, $userId);

            if ($result['result'] === 'correct' || $result['result'] === 'partial') {
                $this->withSuccess($result['message']);
            } else {
                $this->withError($result['message']);
            }

            if ($result['completed_lab']) {
                $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . $instanceId . '/complete');
                return;
            }
        } catch (RuntimeException $e) {
            $code = (int) $e->getCode();
            if ($code >= 400) {
                http_response_code($code);
            }
            $this->withError($e->getMessage());
        } catch (InvalidArgumentException $e) {
            http_response_code(404);
            $this->withError($e->getMessage());
        }

        $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . $instanceId);
    }

    public function revealHint(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $slug = (string) $request->param('slug');
        $instanceId = (int) $request->param('id');
        $hintId = (int) $request->param('hintId');

        try {
            $instance = LabInstanceService::requireOwned($instanceId, $userId);
            $hint = LabHintService::reveal($instance, $hintId, $userId);
            if ($hint['already']) {
                $this->withSuccess('Hint: ' . $hint['title']);
            } else {
                $this->withSuccess('Hint revealed (−' . (int) $hint['penalty'] . ' pts): ' . $hint['title']);
            }
        } catch (\Throwable $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/labs/' . rawurlencode($slug) . '/instance/' . $instanceId);
    }

    /**
     * Strip secrets from instance before passing to views.
     *
     * @param array<string, mixed> $instance
     * @return array<string, mixed>
     */
    private function sanitizeInstance(array $instance): array
    {
        unset($instance['runtime_secrets'], $instance['access_token']);
        return $instance;
    }
}
