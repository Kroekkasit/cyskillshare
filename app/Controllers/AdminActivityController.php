<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\AdminActivityLogService;
use App\Services\AdminDashboardService;

final class AdminActivityController extends Controller
{
    public function index(Request $request): void
    {
        AdminDashboardService::requireAdmin();

        $action = trim((string) $request->input('action', ''));
        $q = trim((string) $request->input('q', ''));
        $userIdRaw = trim((string) $request->input('user_id', ''));
        $userId = $userIdRaw !== '' && ctype_digit($userIdRaw) ? (int) $userIdRaw : null;
        $page = max(1, (int) $request->input('page', 1));

        $result = AdminActivityLogService::paginate(
            $page,
            40,
            $action !== '' ? $action : null,
            $q !== '' ? $q : null,
            $userId
        );

        $this->view('admin/activity', [
            'title' => 'Activity Logs — CySkillShare',
            'result' => $result,
            'actions' => AdminActivityLogService::distinctActions(),
            'filters' => [
                'action' => $action,
                'q' => $q,
                'user_id' => $userIdRaw,
            ],
        ]);
    }
}
