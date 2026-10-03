<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\RateLimiter;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use RuntimeException;

final class ReplyService
{
    public static function create(Thread $thread, int $userId, string $content, ?int $parentReplyId = null): Reply
    {
        if ($thread->is_locked) {
            throw new RuntimeException('This discussion is locked.', 403);
        }

        if ($thread->deleted_at !== null) {
            throw new RuntimeException('This discussion is no longer available.', 404);
        }

        if (!RateLimiter::attempt($userId, 'reply_created', 20, 300)) {
            throw new RuntimeException('Too many replies. Please slow down.', 429);
        }

        if ($parentReplyId !== null) {
            $parent = Reply::find($parentReplyId);
            if ($parent === null || $parent->thread_id !== $thread->id || $parent->deleted_at !== null) {
                throw new RuntimeException('Invalid parent reply.', 400);
            }
        }

        $reply = Reply::create([
            'thread_id' => $thread->id,
            'user_id' => $userId,
            'content' => $content,
            'parent_reply_id' => $parentReplyId,
        ]);

        ActivityLogService::log($userId, 'reply_created', 'reply', $reply->id);

        // Notify thread owner (not self)
        if ($thread->user_id !== $userId) {
            $actor = User::find($userId);
            NotificationService::create(
                $thread->user_id,
                'thread_reply',
                ($actor?->username ?? 'Someone') . ' replied to your discussion.',
                '“' . mb_strimwidth($thread->title, 0, 80, '…') . '”',
                'thread',
                $thread->id
            );
        }

        // Mentions
        NotificationService::notifyMentions($content, $userId, 'reply', $reply->id, $thread->title);

        return $reply;
    }

    public static function update(Reply $reply, int $actorId, string $content): Reply
    {
        if (!Auth::canManage($reply->user_id)) {
            throw new RuntimeException('Forbidden', 403);
        }

        if ($reply->deleted_at !== null) {
            throw new RuntimeException('Reply not found.', 404);
        }

        Reply::updateContent($reply->id, $content);
        ActivityLogService::log($actorId, 'reply_updated', 'reply', $reply->id);

        $updated = Reply::find($reply->id);
        if ($updated === null) {
            throw new RuntimeException('Reply missing after update.');
        }
        return $updated;
    }

    public static function softDelete(Reply $reply, int $actorId): void
    {
        if (!Auth::canManage($reply->user_id, ['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        // If best answer removed via delete, reopen thread
        Database::beginTransaction();
        try {
            $wasBest = $reply->is_best_answer;
            $reply->softDelete();
            if ($wasBest) {
                Thread::clearBestAnswer($reply->thread_id);
                Thread::updateFields($reply->thread_id, ['status' => 'open']);
            }
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'reply_deleted', 'reply', $reply->id);
    }

    public static function markBestAnswer(Thread $thread, Reply $reply, int $actorId): void
    {
        if (!Auth::canManage($thread->user_id, ['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        if ($reply->thread_id !== $thread->id || $reply->deleted_at !== null) {
            throw new RuntimeException('Invalid reply for this thread.', 400);
        }

        Database::beginTransaction();
        try {
            Thread::clearBestAnswer($thread->id);
            Reply::setBestAnswer($reply->id, true);
            Thread::updateFields($thread->id, ['status' => 'solved']);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'best_answer_marked', 'reply', $reply->id);

        if ($reply->user_id !== $actorId) {
            NotificationService::create(
                $reply->user_id,
                'best_answer',
                'Your reply was marked as the best answer.',
                '“' . mb_strimwidth($thread->title, 0, 80, '…') . '”',
                'thread',
                $thread->id
            );
        }

        try {
            SkillEvidenceService::recordBestAnswerEvidence(
                $reply->user_id,
                $thread->id,
                $reply->id,
                $thread->title
            );
        } catch (\Throwable $e) {
            \App\Core\ErrorHandler::log('Skill evidence after best answer failed: ' . $e->getMessage());
        }
    }

    public static function unmarkBestAnswer(Thread $thread, Reply $reply, int $actorId): void
    {
        if (!Auth::canManage($thread->user_id, ['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        if ($reply->thread_id !== $thread->id || !$reply->is_best_answer) {
            throw new RuntimeException('Reply is not the best answer.', 400);
        }

        Database::beginTransaction();
        try {
            Reply::setBestAnswer($reply->id, false);
            Thread::updateFields($thread->id, ['status' => 'open']);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'best_answer_removed', 'reply', $reply->id);

        try {
            SkillEvidenceService::revokeBestAnswerEvidence($reply->id);
        } catch (\Throwable $e) {
            \App\Core\ErrorHandler::log('Skill evidence revoke after unmark failed: ' . $e->getMessage());
        }
    }
}
