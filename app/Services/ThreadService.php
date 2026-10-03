<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\RateLimiter;
use App\Models\Channel;
use App\Models\Tag;
use App\Models\Thread;
use InvalidArgumentException;
use RuntimeException;

final class ThreadService
{
    /**
     * @param list<string> $tagNames
     */
    public static function create(int $userId, int $channelId, string $title, string $content, array $tagNames = []): Thread
    {
        if (!RateLimiter::attempt($userId, 'thread_created', 5, 300)) {
            throw new RuntimeException('Too many discussions created. Please wait a few minutes.', 429);
        }

        $channel = Channel::find($channelId);
        if ($channel === null || !$channel->is_active) {
            throw new InvalidArgumentException('Invalid channel.');
        }

        Database::beginTransaction();
        try {
            $thread = Thread::create([
                'channel_id' => $channel->id,
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
            ]);

            Tag::syncForThread($thread->id, $tagNames);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($userId, 'thread_created', 'thread', $thread->id);
        return $thread;
    }

    /**
     * @param list<string> $tagNames
     */
    public static function update(Thread $thread, int $actorId, string $title, string $content, ?int $channelId = null, array $tagNames = []): Thread
    {
        if (!Auth::canManage($thread->user_id)) {
            throw new RuntimeException('Forbidden', 403);
        }

        $channelId = $channelId ?? $thread->channel_id;
        $channel = Channel::find($channelId);
        if ($channel === null || !$channel->is_active) {
            throw new InvalidArgumentException('Invalid channel.');
        }

        Database::beginTransaction();
        try {
            Thread::updateFields($thread->id, [
                'channel_id' => $channel->id,
                'title' => $title,
                'content' => $content,
            ]);
            Tag::syncForThread($thread->id, $tagNames);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'thread_updated', 'thread', $thread->id);
        $updated = Thread::find($thread->id);
        if ($updated === null) {
            throw new RuntimeException('Thread missing after update.');
        }
        return $updated;
    }

    public static function softDelete(Thread $thread, int $actorId): void
    {
        if (!Auth::canManage($thread->user_id, ['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        Thread::softDelete($thread->id);
        ActivityLogService::log($actorId, 'thread_deleted', 'thread', $thread->id);
    }

    public static function setPinned(Thread $thread, int $actorId, bool $pinned): void
    {
        if (!Auth::hasAnyRole(['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        Thread::updateFields($thread->id, ['is_pinned' => $pinned ? 1 : 0]);
        ActivityLogService::log(
            $actorId,
            $pinned ? 'thread_pinned' : 'thread_unpinned',
            'thread',
            $thread->id
        );
    }

    public static function setLocked(Thread $thread, int $actorId, bool $locked): void
    {
        if (!Auth::hasAnyRole(['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        Thread::updateFields($thread->id, ['is_locked' => $locked ? 1 : 0]);
        ActivityLogService::log(
            $actorId,
            $locked ? 'thread_locked' : 'thread_unlocked',
            'thread',
            $thread->id
        );
    }
}
