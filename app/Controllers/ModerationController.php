<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Channel;
use App\Models\Report;
use App\Services\ModerationService;

final class ModerationController extends Controller
{
    public function reports(Request $request): void
    {
        Auth::requireAnyRole(['moderator', 'admin']);

        $this->view('pages/moderation/reports', [
            'title' => 'Moderation — Reports',
            'reports' => Report::pendingList(100),
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }

    public function resolve(Request $request): void
    {
        Auth::requireAnyRole(['moderator', 'admin']);
        $id = (int) $request->param('id');

        try {
            ModerationService::resolve($id, (int) Auth::id(), 'resolved');
            $this->withSuccess('Report resolved.');
        } catch (\RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/moderation/reports');
    }

    public function dismiss(Request $request): void
    {
        Auth::requireAnyRole(['moderator', 'admin']);
        $id = (int) $request->param('id');

        try {
            ModerationService::resolve($id, (int) Auth::id(), 'dismissed');
            $this->withSuccess('Report dismissed.');
        } catch (\RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/moderation/reports');
    }
}
