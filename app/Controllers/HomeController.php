<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

final class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $checks = [
            'PHP' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'MySQL' => extension_loaded('pdo_mysql'),
            'Database Connection' => false,
            'Session' => session_status() === PHP_SESSION_ACTIVE,
            'Routing' => true,
        ];

        try {
            $checks['Database Connection'] = Database::ping();
        } catch (\Throwable) {
            $checks['Database Connection'] = false;
        }

        $this->view('pages/home', [
            'title' => 'CySkillShare — System Status',
            'checks' => $checks,
            'user' => Auth::user(),
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
