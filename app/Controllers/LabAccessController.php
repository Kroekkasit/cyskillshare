<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\LabAccessService;
use RuntimeException;

final class LabAccessController extends Controller
{
    public function gateway(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $instanceId = (int) $request->param('id');
        $token = $request->input('token') !== null ? (string) $request->input('token') : null;

        try {
            $auth = LabAccessService::authorizeGateway($instanceId, $userId, $token);
        } catch (RuntimeException $e) {
            $code = (int) $e->getCode();
            http_response_code($code >= 400 ? $code : 403);
            $this->view('pages/errors/403', [
                'title' => 'Lab Gateway Unavailable',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        $instance = $auth['instance'];
        unset($instance['runtime_secrets'], $instance['access_token']);

        $target = LabAccessService::simulatedTarget($auth['instance'], $auth['lab']);

        $this->view('labs/gateway', [
            'title' => 'Lab Environment — ' . (string) $auth['lab']['title'],
            'lab' => $auth['lab'],
            'instance' => $instance,
            'publicEnv' => $auth['public_env'],
            'target' => $target,
        ], null);
    }
}
