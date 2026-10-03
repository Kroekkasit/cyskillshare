<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use App\Services\LabRecommendationService;
use App\Services\WriteupService;

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

        $stats = [
            'discussions' => Thread::countAll(),
            'solved_week' => Thread::countSolvedSince(date('Y-m-d H:i:s', strtotime('-7 days') ?: 'now')),
            'members' => User::countActive(),
        ];

        $latestWriteups = WriteupService::list(['sort' => 'recent'], 1, 4)['items'];
        $recommendedLabs = LabRecommendationService::forUser(Auth::id(), 3);

        $this->view('pages/home', [
            'title' => 'CySkillShare',
            'checks' => $checks,
            'user' => Auth::user(),
            'phpVersion' => PHP_VERSION,
            'stats' => $stats,
            'recentDiscussions' => Thread::recent(6),
            'latestWriteups' => $latestWriteups,
            'recommendedLabs' => $recommendedLabs,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }
}
