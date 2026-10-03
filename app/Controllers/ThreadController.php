<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Services\ActivityLogService;

final class ThreadController extends Controller
{
    public function show(Request $request): void
    {
        $id = (int) $request->param('id');
        $thread = Thread::find($id);

        if ($thread === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Thread Not Found',
                'message' => 'The requested thread does not exist.',
            ]);
            return;
        }

        $thread->incrementViews();
        $replies = Reply::byThread($thread->id);
        $author = \App\Models\User::find($thread->user_id);
        $channel = Channel::find($thread->channel_id);

        $this->view('pages/thread/show', [
            'title' => $thread->title . ' — CySkillShare',
            'thread' => $thread,
            'author' => $author,
            'channel' => $channel,
            'replies' => $replies,
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();

        $data = [
            'channel_id' => $request->input('channel_id'),
            'title' => trim((string) $request->input('title', '')),
            'content' => trim((string) $request->input('content', '')),
        ];

        $validator = Validator::make($data, [
            'channel_id' => 'required|integer',
            'title' => 'required|string|min_length:5|max_length:200',
            'content' => 'required|string|min_length:10|max_length:20000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), $data);
            $this->redirect('/community');
        }

        $channel = Channel::find((int) $data['channel_id']);
        if ($channel === null || !$channel->is_active) {
            $this->withError('Invalid channel.');
            $this->redirect('/community');
        }

        $thread = Thread::create([
            'channel_id' => $channel->id,
            'user_id' => (int) Auth::id(),
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        ActivityLogService::log(Auth::id(), 'thread_create', 'thread', $thread->id);

        $this->withSuccess('Thread created.');
        $this->redirect('/thread/' . $thread->id);
    }

    public function reply(Request $request): void
    {
        Auth::requireLogin();

        $threadId = (int) $request->param('id');
        $thread = Thread::find($threadId);

        if ($thread === null) {
            $this->withError('Thread not found.');
            $this->redirect('/community');
        }

        if ($thread->is_locked) {
            $this->withError('This thread is locked.');
            $this->redirect('/thread/' . $threadId);
        }

        $data = [
            'content' => trim((string) $request->input('content', '')),
        ];

        $validator = Validator::make($data, [
            'content' => 'required|string|min_length:2|max_length:20000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $this->redirect('/thread/' . $threadId);
        }

        $reply = Reply::create([
            'thread_id' => $threadId,
            'user_id' => (int) Auth::id(),
            'content' => $data['content'],
        ]);

        ActivityLogService::log(Auth::id(), 'reply_create', 'reply', $reply->id);

        $this->withSuccess('Reply posted.');
        $this->redirect('/thread/' . $threadId);
    }
}
