<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Reply;
use App\Services\ReplyService;

final class ReplyController extends Controller
{
    public function update(Request $request): void
    {
        Auth::requireLogin();
        $reply = Reply::find((int) $request->param('id'));
        if ($reply === null || $reply->deleted_at !== null) {
            $this->withError('Reply not found.');
            $this->redirect('/community');
        }

        $data = ['content' => trim((string) $request->input('content', ''))];
        $validator = Validator::make($data, [
            'content' => 'required|string|min_length:2|max_length:20000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $this->redirect('/thread/' . $reply->thread_id);
        }

        try {
            ReplyService::update($reply, (int) Auth::id(), $data['content']);
            $this->withSuccess('Reply updated.');
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
        }

        $this->redirect('/thread/' . $reply->thread_id);
    }

    public function delete(Request $request): void
    {
        Auth::requireLogin();
        $reply = Reply::find((int) $request->param('id'));
        if ($reply === null || $reply->deleted_at !== null) {
            $this->withError('Reply not found.');
            $this->redirect('/community');
        }

        $threadId = $reply->thread_id;

        try {
            ReplyService::softDelete($reply, (int) Auth::id());
            $this->withSuccess('Reply removed.');
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
        }

        $this->redirect('/thread/' . $threadId);
    }
}
