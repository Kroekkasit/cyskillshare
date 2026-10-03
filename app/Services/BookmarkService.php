<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bookmark;
use App\Models\Thread;
use InvalidArgumentException;

final class BookmarkService
{
    public static function toggle(int $userId, int $threadId): bool
    {
        $thread = Thread::findVisible($threadId);
        if ($thread === null) {
            throw new InvalidArgumentException('Thread not found.');
        }

        if (Bookmark::exists($userId, 'thread', $threadId)) {
            Bookmark::remove($userId, 'thread', $threadId);
            ActivityLogService::log($userId, 'bookmark_removed', 'thread', $threadId);
            return false;
        }

        Bookmark::add($userId, 'thread', $threadId);
        ActivityLogService::log($userId, 'bookmark_created', 'thread', $threadId);
        return true;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function forUser(int $userId, int $page = 1, int $perPage = 15): array
    {
        return Bookmark::threadsForUser($userId, $page, $perPage);
    }
}
