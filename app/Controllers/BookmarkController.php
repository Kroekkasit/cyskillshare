<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\BookmarkService;

final class BookmarkController extends Controller
{
    public function toggle(Request $request): void
    {
        Auth::requireLogin();
        $threadId = (int) $request->param('id');

        try {
            $on = BookmarkService::toggle((int) Auth::id(), $threadId);
            $this->withSuccess($on ? 'Thread bookmarked.' : 'Bookmark removed.');
        } catch (\InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/thread/' . $threadId);
    }
}
