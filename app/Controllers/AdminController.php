<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\AdminDashboardService;

final class AdminController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();
        if (!Auth::hasAnyRole(['admin', 'instructor', 'moderator', 'mentor'])) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access the admin area.',
            ]);
            return;
        }

        $stats = Auth::hasRole('admin') ? AdminDashboardService::stats() : [];

        $this->view('admin/dashboard', [
            'title' => 'Admin — CySkillShare',
            'stats' => $stats,
            'tools' => AdminDashboardService::toolsForCurrentUser(),
            'isAdmin' => Auth::hasRole('admin'),
        ]);
    }

    public function system(Request $request): void
    {
        AdminDashboardService::requireAdmin();

        $dbOk = Database::ping();

        $this->view('admin/system', [
            'title' => 'System Status — CySkillShare',
            'system' => [
                'app_name' => (string) config('app.name'),
                'app_env' => (string) config('app.env'),
                'app_debug' => (bool) config('app.debug'),
                'app_url' => (string) config('app.url'),
                'timezone' => (string) config('app.timezone'),
                'php_version' => PHP_VERSION,
                'db_host' => (string) config('database.host'),
                'db_name' => (string) config('database.database'),
                'db_user' => (string) config('database.username'),
                'db_ok' => $dbOk,
                'session_name' => (string) config('app.session.name'),
                'session_lifetime' => (int) config('app.session.lifetime'),
                'session_same_site' => (string) config('app.session.same_site'),
                'session_http_only' => (bool) config('app.session.http_only'),
                'session_secure' => (bool) config('app.session.secure'),
            ],
        ]);
    }
}
