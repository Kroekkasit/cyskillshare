<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\ArenaProgressService;

final class ArenaProgressController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        $userId = (int) Auth::id();

        $this->view('arena/progress', [
            'title' => 'My Progress — Cyber Arena',
            'isArena' => true,
            'summary' => ArenaProgressService::summary($userId),
            'byCategory' => ArenaProgressService::byCategory($userId),
            'recentActivity' => ArenaProgressService::recentActivity($userId),
        ]);
    }
}
