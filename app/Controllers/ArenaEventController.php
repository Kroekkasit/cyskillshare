<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\ArenaEvent;
use App\Services\ArenaEventService;
use App\Services\ArenaLeaderboardService;
use App\Services\ChallengeService;

final class ArenaEventController extends Controller
{
    public function index(Request $request): void
    {
        $this->view('arena/events/index', [
            'title' => 'Events — Cyber Arena',
            'isArena' => true,
            'events' => ArenaEvent::listPublic(),
        ]);
    }

    public function show(Request $request): void
    {
        $id = (int) $request->param('id');
        $canManage = ChallengeService::canManageArena();
        $event = $canManage ? ArenaEvent::find($id) : ArenaEvent::findVisible($id);

        if ($event === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Event Not Found',
                'message' => 'This event does not exist or is not available.',
            ]);
            return;
        }

        $userId = Auth::id();

        $this->view('arena/events/show', [
            'title' => $event->name . ' — Cyber Arena',
            'isArena' => true,
            'event' => $event,
            'challenges' => ArenaEventService::eventChallenges($event->id, $userId),
            'leaderboard' => ArenaLeaderboardService::forEvent($event->id),
        ]);
    }
}
