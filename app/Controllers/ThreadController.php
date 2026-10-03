<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Bookmark;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use App\Models\Vote;
use App\Services\ReplyService;
use App\Services\SkillService;
use App\Services\ThreadService;

final class ThreadController extends Controller
{
    public function show(Request $request): void
    {
        $id = (int) $request->param('id');
        $thread = Thread::find($id);

        if ($thread === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Discussion Not Found',
                'message' => 'The requested discussion does not exist.',
            ]);
            return;
        }

        $isStaff = Auth::hasAnyRole(['moderator', 'admin']);
        if ($thread->deleted_at !== null && !$isStaff) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Discussion Unavailable',
                'message' => 'This discussion is no longer available.',
            ]);
            return;
        }

        if ($thread->deleted_at === null) {
            $thread->incrementViews();
        }

        $author = User::find($thread->user_id);
        $channel = Channel::find($thread->channel_id);
        $replies = Reply::byThread($thread->id, $isStaff);
        $tags = $thread->tags();
        $score = Vote::score('thread', $thread->id);
        $userVote = Auth::check() ? Vote::findUserVote((int) Auth::id(), 'thread', $thread->id) : null;
        $bookmarked = Auth::check() && Bookmark::exists((int) Auth::id(), 'thread', $thread->id);

        $replyVotes = [];
        if (Auth::check()) {
            foreach ($replies as $r) {
                $replyVotes[(int) $r['id']] = Vote::findUserVote((int) Auth::id(), 'reply', (int) $r['id']);
            }
        }

        $this->view('pages/thread/show', [
            'title' => $thread->title . ' — CySkillShare',
            'thread' => $thread,
            'author' => $author,
            'channel' => $channel,
            'replies' => $replies,
            'tags' => $tags,
            'score' => $score,
            'userVote' => $userVote['vote_type'] ?? null,
            'bookmarked' => $bookmarked,
            'replyVotes' => $replyVotes,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => $channel?->slug,
            'canModerate' => $isStaff,
            'canManageThread' => Auth::check() && Auth::canManage($thread->user_id),
            'discussedSkills' => SkillService::skillsForThread($thread->id),
        ]);
    }

    public function editForm(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            http_response_code(404);
            $this->view('pages/errors/404', ['title' => 'Not Found', 'message' => 'Discussion not found.']);
            return;
        }
        Auth::requireOwnership($thread->user_id);

        $tagList = array_map(static fn(array $t): string => $t['slug'], $thread->tags());

        $this->view('pages/thread/edit', [
            'title' => 'Edit Discussion — CySkillShare',
            'thread' => $thread,
            'channels' => Channel::allActive(),
            'channelsGrouped' => Channel::groupedForSidebar(),
            'tagString' => implode(', ', $tagList),
            'activeChannel' => Channel::find($thread->channel_id)?->slug,
        ]);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        $tagInput = trim((string) $request->input('tags', ''));
        $tagNames = $tagInput === ''
            ? []
            : array_values(array_filter(array_map('trim', preg_split('/[,]+/', $tagInput) ?: [])));

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
            $this->withErrors($validator->errors(), array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/thread/' . $thread->id . '/edit');
        }

        try {
            ThreadService::update(
                $thread,
                (int) Auth::id(),
                $data['title'],
                $data['content'],
                (int) $data['channel_id'],
                $tagNames
            );
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
            $this->redirect('/thread/' . $thread->id);
        }

        $this->withSuccess('Discussion updated successfully.');
        $this->redirect('/thread/' . $thread->id);
    }

    public function delete(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        try {
            ThreadService::softDelete($thread, (int) Auth::id());
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
            $this->redirect('/thread/' . $thread->id);
        }

        $this->withSuccess('Discussion removed.');
        $this->redirect('/community');
    }

    public function reply(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        $data = ['content' => trim((string) $request->input('content', ''))];
        $validator = Validator::make($data, [
            'content' => 'required|string|min_length:2|max_length:20000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $this->redirect('/thread/' . $thread->id);
        }

        try {
            ReplyService::create($thread, (int) Auth::id(), $data['content']);
        } catch (\RuntimeException $e) {
            http_response_code($e->getCode() >= 400 ? (int) $e->getCode() : 400);
            $this->withError($e->getMessage());
            $this->redirect('/thread/' . $thread->id);
        }

        $this->withSuccess('Reply posted successfully.');
        $this->redirect('/thread/' . $thread->id);
    }

    public function bestAnswer(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        $replyId = (int) $request->input('reply_id', 0);
        $action = (string) $request->input('action', 'mark');

        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        $reply = Reply::find($replyId);
        if ($reply === null) {
            $this->withError('Reply not found.');
            $this->redirect('/thread/' . $thread->id);
        }

        try {
            if ($action === 'unmark') {
                ReplyService::unmarkBestAnswer($thread, $reply, (int) Auth::id());
                $this->withSuccess('Best answer removed.');
            } else {
                ReplyService::markBestAnswer($thread, $reply, (int) Auth::id());
                $this->withSuccess('Best answer marked. Discussion marked as solved.');
            }
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError($e->getMessage());
        }

        $this->redirect('/thread/' . $thread->id);
    }

    public function pin(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        $pin = (string) $request->input('action', 'pin') === 'pin';
        try {
            ThreadService::setPinned($thread, (int) Auth::id(), $pin);
            $this->withSuccess($pin ? 'Discussion pinned.' : 'Discussion unpinned.');
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
        }
        $this->redirect('/thread/' . $thread->id);
    }

    public function lock(Request $request): void
    {
        Auth::requireLogin();
        $thread = Thread::findVisible((int) $request->param('id'));
        if ($thread === null) {
            $this->withError('Discussion not found.');
            $this->redirect('/community');
        }

        $lock = (string) $request->input('action', 'lock') === 'lock';
        try {
            ThreadService::setLocked($thread, (int) Auth::id(), $lock);
            $this->withSuccess($lock ? 'Discussion locked.' : 'Discussion unlocked.');
        } catch (\RuntimeException $e) {
            http_response_code(403);
            $this->withError('You do not have permission to perform this action.');
        }
        $this->redirect('/thread/' . $thread->id);
    }
}
