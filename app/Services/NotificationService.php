<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

final class NotificationService
{
    public static function create(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    public static function notifyMentions(
        string $content,
        int $actorId,
        string $referenceType,
        int $referenceId,
        string $contextTitle
    ): void {
        if (!preg_match_all('/@([a-zA-Z0-9_]{3,50})/', $content, $matches)) {
            return;
        }

        $usernames = array_unique($matches[1]);
        $actor = User::find($actorId);

        foreach ($usernames as $username) {
            $user = User::findByUsername($username);
            if ($user === null || $user->id === $actorId) {
                continue;
            }

            self::create(
                $user->id,
                'mention',
                ($actor?->username ?? 'Someone') . ' mentioned you.',
                'In “' . mb_strimwidth($contextTitle, 0, 80, '…') . '”',
                $referenceType,
                $referenceId
            );
        }
    }

    public static function markRead(int $userId, ?int $notificationId = null): void
    {
        if ($notificationId !== null) {
            Notification::markReadForUser($userId, $notificationId);
            return;
        }
        Notification::markAllRead($userId);
    }
}
