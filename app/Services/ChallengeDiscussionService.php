<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Challenge;
use App\Models\Channel;
use App\Models\Thread;
use RuntimeException;

final class ChallengeDiscussionService
{
    /**
     * Find or create the community discussion thread for a challenge.
     */
    public static function openOrCreate(int $userId, Challenge $challenge): Thread
    {
        $existing = Database::fetch(
            'SELECT id FROM threads
             WHERE challenge_id = ? AND deleted_at IS NULL
             ORDER BY id ASC LIMIT 1',
            [$challenge->id]
        );
        if ($existing !== null) {
            $thread = Thread::findVisible((int) $existing['id']);
            if ($thread !== null) {
                return $thread;
            }
        }

        $slug = (string) config('arena.discussion_channel_slug', 'ctf-general');
        $channel = Channel::findBySlug($slug);
        if ($channel === null || !$channel->is_active) {
            throw new RuntimeException('Discussion channel is unavailable.');
        }

        $title = 'Challenge Discussion: ' . $challenge->title;
        $content = "> ⚠ **Possible Spoiler**\n>\n> This discussion may contain hints or solutions for **"
            . $challenge->title . "**.\n>\n"
            . "Only continue if you are ready.\n\n"
            . "Challenge: [/arena/challenges/{$challenge->id}](/arena/challenges/{$challenge->id})\n\n"
            . "Have a question about approach, tooling, or learning resources? Ask here.";

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO threads (channel_id, user_id, challenge_id, title, content, status)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$channel->id, $userId, $challenge->id, $title, $content, 'open']
            );
            $id = (int) Database::lastInsertId();
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($userId, 'thread_created', 'thread', $id, [
            'challenge_id' => $challenge->id,
        ]);

        $thread = Thread::find($id);
        if ($thread === null) {
            throw new RuntimeException('Failed to open discussion.');
        }
        return $thread;
    }

    public static function threadIdForChallenge(int $challengeId): ?int
    {
        $row = Database::fetch(
            'SELECT id FROM threads WHERE challenge_id = ? AND deleted_at IS NULL ORDER BY id ASC LIMIT 1',
            [$challengeId]
        );
        return $row ? (int) $row['id'] : null;
    }
}
