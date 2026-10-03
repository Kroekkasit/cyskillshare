<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\ChallengeCategory;
use App\Services\ChallengeService;

final class ArenaController extends Controller
{
    public function index(Request $request): void
    {
        $userId = Auth::id();

        $this->view('arena/home', [
            'title' => 'Cyber Arena — CySkillShare',
            'isArena' => true,
            'stats' => ChallengeService::dashboardStats($userId),
            'continueLearning' => ChallengeService::continueLearning($userId),
            'recommended' => ChallengeService::recommended($userId),
            'featured' => ChallengeService::featured(),
            'categories' => ChallengeCategory::allActive(),
        ]);
    }
}
