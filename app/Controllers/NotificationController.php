<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Channel;
use App\Models\Notification;
use App\Services\NotificationService;

final class NotificationController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $notifications = Notification::forUser($userId, 50);

        $this->view('pages/notifications/index', [
            'title' => 'Notifications — CySkillShare',
            'notifications' => $notifications,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }

    public function markRead(Request $request): void
    {
        Auth::requireLogin();
        $id = $request->input('notification_id');
        NotificationService::markRead(
            (int) Auth::id(),
            $id !== null && $id !== '' ? (int) $id : null
        );
        $this->withSuccess('Notifications updated.');
        $this->redirect('/notifications');
    }
}
