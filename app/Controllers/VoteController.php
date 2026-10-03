<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\VoteService;

final class VoteController extends Controller
{
    public function voteThread(Request $request): void
    {
        $this->vote($request, 'thread', (int) $request->param('id'));
    }

    public function voteReply(Request $request): void
    {
        $this->vote($request, 'reply', (int) $request->param('id'));
    }

    private function vote(Request $request, string $targetType, int $targetId): void
    {
        Auth::requireLogin();
        $voteType = (string) $request->input('vote_type', '');

        try {
            VoteService::vote((int) Auth::id(), $targetType, $targetId, $voteType);
            $this->withSuccess('Your vote was recorded.');
        } catch (\RuntimeException $e) {
            http_response_code($e->getCode() >= 400 ? (int) $e->getCode() : 400);
            $this->withError($e->getMessage());
        } catch (\InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $redirect = (string) $request->input('redirect', '');
        if ($redirect !== '' && str_starts_with($redirect, '/')) {
            $this->redirect($redirect);
        }

        if ($targetType === 'thread') {
            $this->redirect('/thread/' . $targetId);
        }

        $reply = \App\Models\Reply::find($targetId);
        $this->redirect('/thread/' . ($reply?->thread_id ?? ''));
    }
}
