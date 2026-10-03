<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\ArenaLeaderboardService;

final class ArenaLeaderboardController extends Controller
{
    public function index(Request $request): void
    {
        $period = (string) $request->input('period', 'all');
        if (!in_array($period, ['all', 'month', 'semester'], true)) {
            $period = 'all';
        }

        $this->view('arena/leaderboard', [
            'title' => 'Leaderboard — Cyber Arena',
            'isArena' => true,
            'period' => $period,
            'leaderboard' => ArenaLeaderboardService::global($period),
        ]);
    }
}
