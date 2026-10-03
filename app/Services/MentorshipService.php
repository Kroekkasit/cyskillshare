<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use InvalidArgumentException;
use RuntimeException;

final class MentorshipService
{
    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM mentorships WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @param list<int> $skillIds
     */
    public static function request(int $menteeId, int $mentorProfileId, array $data, array $skillIds = []): array
    {
        $mentor = MentorService::find($mentorProfileId);
        if ($mentor === null || !(int) $mentor['accepting_requests'] || ($mentor['verification_status'] ?? '') === 'suspended') {
            throw new InvalidArgumentException('This mentor is not accepting requests.');
        }
        if ((int) $mentor['user_id'] === $menteeId) {
            throw new InvalidArgumentException('You cannot mentor yourself.');
        }
        if (UserBlockService::isBlockedEither((int) $mentor['user_id'], $menteeId)) {
            throw new RuntimeException('Unable to request mentorship.', 403);
        }

        $max = (int) config('collaboration.mentorship_request_max_per_day', 5);
        if (!RateLimiter::attempt($menteeId, 'mentorship_req_rl', $max, 86400)) {
            throw new RuntimeException('Too many mentorship requests today.', 429);
        }
        RateLimiter::hit($menteeId, 'mentorship_req_rl');

        $dup = Database::fetch(
            "SELECT id FROM mentorships
             WHERE mentor_id = ? AND mentee_id = ?
               AND status IN ('pending','accepted','active') LIMIT 1",
            [$mentorProfileId, $menteeId]
        );
        if ($dup !== null) {
            throw new InvalidArgumentException('You already have an open mentorship with this mentor.');
        }

        $active = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM mentorships
             WHERE mentor_id = ? AND status IN ('active','accepted','pending')",
            [$mentorProfileId]
        )['c'] ?? 0);
        if ($active >= (int) $mentor['max_mentees']) {
            throw new RuntimeException('This mentor has no available slots.', 409);
        }

        Database::execute(
            'INSERT INTO mentorships
             (mentor_id, mentee_id, status, learning_goal, message, preferred_frequency, preferred_session_length)
             VALUES (?, ?, \'pending\', ?, ?, ?, ?)',
            [
                $mentorProfileId,
                $menteeId,
                mb_substr(trim((string) ($data['learning_goal'] ?? '')), 0, 2000) ?: null,
                mb_substr(trim((string) ($data['message'] ?? '')), 0, 2000) ?: null,
                mb_substr((string) ($data['preferred_frequency'] ?? ''), 0, 80) ?: null,
                mb_substr((string) ($data['preferred_session_length'] ?? ''), 0, 40) ?: null,
            ]
        );
        $id = (int) Database::lastInsertId();
        foreach ($skillIds as $skillId) {
            $skillId = (int) $skillId;
            if ($skillId > 0) {
                Database::execute(
                    'INSERT IGNORE INTO mentorship_skills (mentorship_id, skill_id) VALUES (?, ?)',
                    [$id, $skillId]
                );
            }
        }

        ActivityLogService::log($menteeId, 'mentorship_requested', 'mentorship', $id);
        NotificationService::create(
            (int) $mentor['user_id'],
            'mentorship_request_received',
            'Mentorship request',
            'A student requested your mentorship.',
            'mentorship',
            $id
        );

        $row = self::find($id);
        if ($row === null) {
            throw new RuntimeException('Mentorship missing after create.');
        }
        return $row;
    }

    public static function respond(int $mentorshipId, int $actorUserId, string $decision): void
    {
        $m = self::find($mentorshipId);
        if ($m === null || ($m['status'] ?? '') !== 'pending') {
            throw new InvalidArgumentException('Request not found.');
        }
        $mentor = MentorService::find((int) $m['mentor_id']);
        if ($mentor === null) {
            throw new InvalidArgumentException('Mentor not found.');
        }

        if ($decision === 'cancelled') {
            if ((int) $m['mentee_id'] !== $actorUserId) {
                throw new RuntimeException('Forbidden', 403);
            }
            Database::execute("UPDATE mentorships SET status = 'cancelled' WHERE id = ?", [$mentorshipId]);
            ActivityLogService::log($actorUserId, 'mentorship_cancelled', 'mentorship', $mentorshipId);
            return;
        }

        if ((int) $mentor['user_id'] !== $actorUserId) {
            throw new RuntimeException('Forbidden', 403);
        }
        if (!in_array($decision, ['accepted', 'declined'], true)) {
            throw new InvalidArgumentException('Invalid decision.');
        }

        if ($decision === 'accepted') {
            $active = (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM mentorships
                 WHERE mentor_id = ? AND status IN ('active','accepted')",
                [(int) $m['mentor_id']]
            )['c'] ?? 0);
            if ($active >= (int) $mentor['max_mentees']) {
                throw new RuntimeException('Mentor capacity exceeded.', 409);
            }
            Database::execute(
                "UPDATE mentorships SET status = 'active', started_at = NOW() WHERE id = ?",
                [$mentorshipId]
            );
            ActivityLogService::log($actorUserId, 'mentorship_accepted', 'mentorship', $mentorshipId);
            NotificationService::create(
                (int) $m['mentee_id'],
                'mentorship_request_accepted',
                'Mentorship accepted',
                'Your mentorship request was accepted.',
                'mentorship',
                $mentorshipId
            );
        } else {
            Database::execute("UPDATE mentorships SET status = 'declined' WHERE id = ?", [$mentorshipId]);
            NotificationService::create(
                (int) $m['mentee_id'],
                'mentorship_request_declined',
                'Mentorship declined',
                'Your mentorship request was declined.',
                'mentorship',
                $mentorshipId
            );
        }
    }

    public static function requireParticipant(int $mentorshipId, int $userId): array
    {
        $m = self::find($mentorshipId);
        if ($m === null) {
            throw new InvalidArgumentException('Mentorship not found.', 404);
        }
        $mentor = MentorService::find((int) $m['mentor_id']);
        $mentorUserId = (int) ($mentor['user_id'] ?? 0);
        if ($userId !== (int) $m['mentee_id'] && $userId !== $mentorUserId && !GroupService::canManagePlatform()) {
            throw new RuntimeException('Forbidden', 403);
        }
        return $m;
    }

    /**
     * @return array<string, mixed>
     */
    public static function dashboard(int $mentorshipId, int $viewerId): array
    {
        $m = self::requireParticipant($mentorshipId, $viewerId);
        $mentor = MentorService::find((int) $m['mentor_id']);
        $mentorUser = Database::fetch(
            'SELECT id, username, full_name FROM users WHERE id = ?',
            [(int) ($mentor['user_id'] ?? 0)]
        );
        $mentee = Database::fetch(
            'SELECT id, username, full_name FROM users WHERE id = ?',
            [(int) $m['mentee_id']]
        );
        $goals = Database::fetchAll(
            'SELECT g.*, s.name AS skill_name, s.slug AS skill_slug,
                    COALESCE(us.current_level, 0) AS current_level
             FROM mentorship_goals g
             LEFT JOIN skills s ON s.id = g.skill_id
             LEFT JOIN user_skills us ON us.skill_id = g.skill_id AND us.user_id = ?
             WHERE g.mentorship_id = ?
             ORDER BY FIELD(g.status,\'active\',\'completed\',\'cancelled\'), g.id',
            [(int) $m['mentee_id'], $mentorshipId]
        );
        $sessions = Database::fetchAll(
            'SELECT * FROM mentorship_sessions WHERE mentorship_id = ? ORDER BY scheduled_at DESC LIMIT 20',
            [$mentorshipId]
        );
        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug FROM mentorship_skills ms
             INNER JOIN skills s ON s.id = ms.skill_id WHERE ms.mentorship_id = ?',
            [$mentorshipId]
        );

        return [
            'mentorship' => $m,
            'mentor' => $mentor,
            'mentor_user' => $mentorUser,
            'mentee' => $mentee,
            'goals' => $goals,
            'sessions' => $sessions,
            'skills' => $skills,
        ];
    }

    public static function addGoal(int $mentorshipId, int $actorId, array $data): void
    {
        self::requireParticipant($mentorshipId, $actorId);
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            throw new InvalidArgumentException('Goal title is required.');
        }
        Database::execute(
            'INSERT INTO mentorship_goals (mentorship_id, skill_id, title, description, target_level, status, progress)
             VALUES (?, ?, ?, ?, ?, \'active\', ?)',
            [
                $mentorshipId,
                !empty($data['skill_id']) ? (int) $data['skill_id'] : null,
                mb_substr($title, 0, 200),
                mb_substr(trim((string) ($data['description'] ?? '')), 0, 2000) ?: null,
                isset($data['target_level']) ? max(1, min(5, (int) $data['target_level'])) : null,
                max(0, min(100, (int) ($data['progress'] ?? 0))),
            ]
        );
    }

    public static function scheduleSession(int $mentorshipId, int $actorId, array $data): void
    {
        self::requireParticipant($mentorshipId, $actorId);
        $title = trim((string) ($data['title'] ?? ''));
        $when = trim((string) ($data['scheduled_at'] ?? ''));
        if ($title === '' || $when === '') {
            throw new InvalidArgumentException('Title and schedule time are required.');
        }
        Database::execute(
            'INSERT INTO mentorship_sessions
             (mentorship_id, title, notes, scheduled_at, duration_minutes, meeting_link, status)
             VALUES (?, ?, ?, ?, ?, ?, \'scheduled\')',
            [
                $mentorshipId,
                mb_substr($title, 0, 200),
                mb_substr(trim((string) ($data['notes'] ?? '')), 0, 5000) ?: null,
                $when,
                max(15, min(180, (int) ($data['duration_minutes'] ?? 45))),
                self::safeMeetingLink((string) ($data['meeting_link'] ?? '')),
            ]
        );
        $m = self::find($mentorshipId);
        $mentor = MentorService::find((int) ($m['mentor_id'] ?? 0));
        $notifyId = $actorId === (int) ($m['mentee_id'] ?? 0)
            ? (int) ($mentor['user_id'] ?? 0)
            : (int) ($m['mentee_id'] ?? 0);
        if ($notifyId > 0) {
            NotificationService::create(
                $notifyId,
                'mentorship_session_upcoming',
                'Mentorship session scheduled',
                mb_strimwidth($title, 0, 80, '…'),
                'mentorship',
                $mentorshipId
            );
        }
    }

    private static function safeMeetingLink(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }
        if (!preg_match('#^https://#i', $url)) {
            throw new InvalidArgumentException('Meeting links must use HTTPS.');
        }
        return mb_substr($url, 0, 500);
    }

    /** @return list<array<string,mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT ms.*, mu.username AS mentor_username, me.username AS mentee_username,
                    m.verification_status
             FROM mentorships ms
             INNER JOIN mentors m ON m.id = ms.mentor_id
             INNER JOIN users mu ON mu.id = m.user_id
             INNER JOIN users me ON me.id = ms.mentee_id
             WHERE ms.mentee_id = ? OR m.user_id = ?
             ORDER BY FIELD(ms.status,'active','pending','accepted','completed','declined','cancelled'), ms.updated_at DESC",
            [$userId, $userId]
        );
    }
}
