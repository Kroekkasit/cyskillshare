<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use InvalidArgumentException;
use RuntimeException;

final class GroupMemberService
{
    public static function join(array $group, int $userId, string $message = ''): string
    {
        if (UserBlockService::isBlockedEither((int) $group['owner_id'], $userId)) {
            throw new RuntimeException('Unable to join this group.', 403);
        }
        if (GroupService::isMember((int) $group['id'], $userId)) {
            return 'already_member';
        }
        if (($group['status'] ?? '') !== 'active') {
            throw new RuntimeException('This group is not accepting members.', 403);
        }

        $count = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM collab_group_members WHERE group_id = ? AND status = 'active'",
            [(int) $group['id']]
        )['c'] ?? 0);
        if ($count >= (int) $group['max_members']) {
            throw new RuntimeException('This group is full.', 409);
        }

        $policy = (string) ($group['join_policy'] ?? 'approval');
        if ($policy === 'invite_only') {
            throw new RuntimeException('This group is invite-only.', 403);
        }
        if ($policy === 'open') {
            Database::execute(
                "INSERT INTO collab_group_members (group_id, user_id, role, status)
                 VALUES (?, ?, 'member', 'active')
                 ON DUPLICATE KEY UPDATE role = 'member', status = 'active', joined_at = NOW()",
                [(int) $group['id'], $userId]
            );
            ActivityLogService::log($userId, 'group_joined', 'collab_group', (int) $group['id']);
            NotificationService::create(
                (int) $group['owner_id'],
                'group_member_joined',
                'New group member',
                'Someone joined “' . mb_strimwidth((string) $group['name'], 0, 60, '…') . '”.',
                'collab_group',
                (int) $group['id']
            );
            return 'joined';
        }

        // approval
        $max = (int) config('collaboration.join_request_max_per_hour', 10);
        if (!RateLimiter::attempt($userId, 'group_join_rl', $max, 3600)) {
            throw new RuntimeException('Too many join requests. Please wait.', 429);
        }
        RateLimiter::hit($userId, 'group_join_rl');

        $existing = Database::fetch(
            "SELECT id FROM collab_group_join_requests
             WHERE group_id = ? AND user_id = ? AND status = 'pending' LIMIT 1",
            [(int) $group['id'], $userId]
        );
        if ($existing !== null) {
            return 'pending';
        }

        Database::execute(
            'INSERT INTO collab_group_join_requests (group_id, user_id, message, status) VALUES (?, ?, ?, \'pending\')',
            [(int) $group['id'], $userId, mb_substr(trim($message), 0, 2000) ?: null]
        );
        NotificationService::create(
            (int) $group['owner_id'],
            'group_join_request_received',
            'Group join request',
            'A student requested to join “' . mb_strimwidth((string) $group['name'], 0, 60, '…') . '”.',
            'collab_group',
            (int) $group['id']
        );
        return 'requested';
    }

    public static function leave(array $group, int $userId): void
    {
        $role = GroupService::memberRole((int) $group['id'], $userId);
        if ($role === null) {
            throw new InvalidArgumentException('You are not a member of this group.');
        }
        if (in_array($role, ['owner', 'captain'], true) && (int) $group['owner_id'] === $userId) {
            throw new RuntimeException('Transfer ownership before leaving as owner/captain.', 403);
        }
        Database::execute(
            "UPDATE collab_group_members SET status = 'left' WHERE group_id = ? AND user_id = ?",
            [(int) $group['id'], $userId]
        );
        ActivityLogService::log($userId, 'group_left', 'collab_group', (int) $group['id']);
    }

    public static function reviewJoinRequest(int $requestId, int $reviewerId, string $decision): void
    {
        $req = Database::fetch('SELECT * FROM collab_group_join_requests WHERE id = ? LIMIT 1', [$requestId]);
        if ($req === null || ($req['status'] ?? '') !== 'pending') {
            throw new InvalidArgumentException('Join request not found.');
        }
        if ((int) $req['user_id'] === $reviewerId) {
            throw new RuntimeException('You cannot review your own request.', 403);
        }
        $group = GroupService::find((int) $req['group_id']);
        if ($group === null || !GroupService::canManageGroup($group, $reviewerId)) {
            throw new RuntimeException('Forbidden', 403);
        }
        if (!in_array($decision, ['approved', 'rejected'], true)) {
            throw new InvalidArgumentException('Invalid decision.');
        }

        Database::execute(
            'UPDATE collab_group_join_requests SET status = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?',
            [$decision, $reviewerId, $requestId]
        );

        if ($decision === 'approved') {
            Database::execute(
                "INSERT INTO collab_group_members (group_id, user_id, role, status)
                 VALUES (?, ?, 'member', 'active')
                 ON DUPLICATE KEY UPDATE status = 'active', role = IF(role='owner', role, 'member'), joined_at = NOW()",
                [(int) $group['id'], (int) $req['user_id']]
            );
            ActivityLogService::log($reviewerId, 'group_join_approved', 'collab_group', (int) $group['id'], [
                'user_id' => (int) $req['user_id'],
            ]);
            NotificationService::create(
                (int) $req['user_id'],
                'group_join_request_approved',
                'Join request approved',
                'You joined “' . mb_strimwidth((string) $group['name'], 0, 60, '…') . '”.',
                'collab_group',
                (int) $group['id']
            );
        } else {
            NotificationService::create(
                (int) $req['user_id'],
                'group_join_request_rejected',
                'Join request declined',
                'Your request to join “' . mb_strimwidth((string) $group['name'], 0, 60, '…') . '” was declined.',
                'collab_group',
                (int) $group['id']
            );
        }
    }

    public static function invite(array $group, int $inviterId, int $inviteeId): void
    {
        if (!GroupService::canManageGroup($group, $inviterId)) {
            throw new RuntimeException('Forbidden', 403);
        }
        if ($inviteeId === $inviterId) {
            throw new InvalidArgumentException('You cannot invite yourself.');
        }
        if (UserBlockService::isBlockedEither($inviterId, $inviteeId)) {
            throw new RuntimeException('Unable to invite this user.', 403);
        }
        if (GroupService::isMember((int) $group['id'], $inviteeId)) {
            throw new InvalidArgumentException('User is already a member.');
        }

        $max = (int) config('collaboration.group_invite_max_per_hour', 20);
        if (!RateLimiter::attempt($inviterId, 'group_invite_rl', $max, 3600)) {
            throw new RuntimeException('Too many invitations. Please wait.', 429);
        }
        RateLimiter::hit($inviterId, 'group_invite_rl');

        $hours = (int) config('collaboration.invitation_ttl_hours', 72);
        $token = bin2hex(random_bytes(32));
        Database::execute(
            'INSERT INTO collab_group_invitations
             (group_id, inviter_id, invitee_id, token, status, expires_at)
             VALUES (?, ?, ?, ?, \'pending\', DATE_ADD(NOW(), INTERVAL ? HOUR))',
            [(int) $group['id'], $inviterId, $inviteeId, $token, $hours]
        );
        ActivityLogService::log($inviterId, 'group_invited', 'collab_group', (int) $group['id'], [
            'invitee_id' => $inviteeId,
        ]);
        NotificationService::create(
            $inviteeId,
            'group_invitation_received',
            'Group invitation',
            'You were invited to “' . mb_strimwidth((string) $group['name'], 0, 60, '…') . '”.',
            'collab_group',
            (int) $group['id']
        );
    }

    public static function respondInvitation(int $invitationId, int $userId, string $decision): void
    {
        $inv = Database::fetch('SELECT * FROM collab_group_invitations WHERE id = ? LIMIT 1', [$invitationId]);
        if ($inv === null || (int) $inv['invitee_id'] !== $userId) {
            throw new RuntimeException('Forbidden', 403);
        }
        if (($inv['status'] ?? '') !== 'pending') {
            throw new InvalidArgumentException('Invitation is no longer pending.');
        }
        $expired = Database::fetch(
            'SELECT (expires_at < NOW()) AS e FROM collab_group_invitations WHERE id = ?',
            [$invitationId]
        );
        if ((int) ($expired['e'] ?? 0) === 1) {
            Database::execute("UPDATE collab_group_invitations SET status = 'expired' WHERE id = ?", [$invitationId]);
            throw new InvalidArgumentException('Invitation expired.');
        }
        if (!in_array($decision, ['accepted', 'declined'], true)) {
            throw new InvalidArgumentException('Invalid decision.');
        }
        Database::execute(
            'UPDATE collab_group_invitations SET status = ? WHERE id = ?',
            [$decision, $invitationId]
        );
        if ($decision === 'accepted') {
            $group = GroupService::find((int) $inv['group_id']);
            if ($group === null) {
                throw new InvalidArgumentException('Group not found.');
            }
            Database::execute(
                "INSERT INTO collab_group_members (group_id, user_id, role, status)
                 VALUES (?, ?, 'member', 'active')
                 ON DUPLICATE KEY UPDATE status = 'active', joined_at = NOW()",
                [(int) $group['id'], $userId]
            );
            ActivityLogService::log($userId, 'group_joined', 'collab_group', (int) $group['id']);
        }
    }

    public static function removeMember(array $group, int $actorId, int $targetUserId): void
    {
        if (!GroupService::canManageGroup($group, $actorId)) {
            throw new RuntimeException('Forbidden', 403);
        }
        if ($targetUserId === (int) $group['owner_id']) {
            throw new RuntimeException('Cannot remove the group owner.', 403);
        }
        Database::execute(
            "UPDATE collab_group_members SET status = 'removed' WHERE group_id = ? AND user_id = ?",
            [(int) $group['id'], $targetUserId]
        );
        ActivityLogService::log($actorId, 'group_member_removed', 'collab_group', (int) $group['id'], [
            'user_id' => $targetUserId,
        ]);
    }
}
