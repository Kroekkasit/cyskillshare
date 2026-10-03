<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use InvalidArgumentException;
use RuntimeException;

final class MentorService
{
    public static function canVerify(): bool
    {
        $roles = config('collaboration.mentor_verifier_roles', ['instructor', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function findByUserId(int $userId): ?array
    {
        return Database::fetch('SELECT * FROM mentors WHERE user_id = ? LIMIT 1', [$userId]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM mentors WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @param list<int> $skillIds
     */
    public static function enable(int $userId, array $data, array $skillIds = []): array
    {
        $minLevel = (int) config('collaboration.mentor_min_skill_level', 2);
        $hasSkill = Database::fetch(
            'SELECT 1 AS x FROM user_skills WHERE user_id = ? AND current_level >= ? LIMIT 1',
            [$userId, $minLevel]
        );
        // Allow enable without skill gate for staff; soft gate for students
        if ($hasSkill === null && !Auth::hasAnyRole(['instructor', 'mentor', 'admin'])) {
            throw new RuntimeException(
                'Demonstrate at least one skill at the required level before enabling mentorship.',
                403
            );
        }

        $existing = self::findByUserId($userId);
        $max = max(1, min(10, (int) ($data['max_mentees'] ?? config('collaboration.default_max_mentees', 3))));

        if ($existing !== null) {
            Database::execute(
                'UPDATE mentors SET bio = ?, accepting_requests = ?, max_mentees = ?,
                 preferred_frequency = ?, preferred_session_length = ?, languages = ?,
                 communication_style = ?, updated_at = NOW()
                 WHERE id = ?',
                [
                    mb_substr(trim((string) ($data['bio'] ?? '')), 0, 5000) ?: null,
                    !empty($data['accepting_requests']) ? 1 : 0,
                    $max,
                    mb_substr((string) ($data['preferred_frequency'] ?? ''), 0, 80) ?: null,
                    mb_substr((string) ($data['preferred_session_length'] ?? ''), 0, 40) ?: null,
                    mb_substr((string) ($data['languages'] ?? ''), 0, 120) ?: null,
                    mb_substr((string) ($data['communication_style'] ?? ''), 0, 120) ?: null,
                    (int) $existing['id'],
                ]
            );
            $mentorId = (int) $existing['id'];
        } else {
            Database::execute(
                'INSERT INTO mentors
                 (user_id, bio, accepting_requests, max_mentees, preferred_frequency,
                  preferred_session_length, languages, communication_style)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $userId,
                    mb_substr(trim((string) ($data['bio'] ?? '')), 0, 5000) ?: null,
                    !isset($data['accepting_requests']) || !empty($data['accepting_requests']) ? 1 : 0,
                    $max,
                    mb_substr((string) ($data['preferred_frequency'] ?? 'weekly'), 0, 80),
                    mb_substr((string) ($data['preferred_session_length'] ?? '45 minutes'), 0, 40),
                    mb_substr((string) ($data['languages'] ?? ''), 0, 120) ?: null,
                    mb_substr((string) ($data['communication_style'] ?? ''), 0, 120) ?: null,
                ]
            );
            $mentorId = (int) Database::lastInsertId();
            ActivityLogService::log($userId, 'mentor_enabled', 'mentor', $mentorId);
        }

        self::syncSkills($mentorId, $skillIds);
        $row = self::find($mentorId);
        if ($row === null) {
            throw new RuntimeException('Mentor profile missing.');
        }
        return $row;
    }

    /** @param list<int> $skillIds */
    public static function syncSkills(int $mentorId, array $skillIds): void
    {
        Database::execute('DELETE FROM mentor_skills WHERE mentor_id = ?', [$mentorId]);
        foreach ($skillIds as $skillId) {
            $skillId = (int) $skillId;
            if ($skillId > 0) {
                Database::execute(
                    'INSERT IGNORE INTO mentor_skills (mentor_id, skill_id) VALUES (?, ?)',
                    [$mentorId, $skillId]
                );
            }
        }
    }

    public static function verify(int $mentorId, int $verifierId, string $note = ''): void
    {
        if (!self::canVerify()) {
            throw new RuntimeException('Forbidden', 403);
        }
        $mentor = self::find($mentorId);
        if ($mentor === null) {
            throw new InvalidArgumentException('Mentor not found.');
        }
        if ((int) $mentor['user_id'] === $verifierId) {
            throw new RuntimeException('You cannot verify yourself.', 403);
        }
        Database::execute(
            "UPDATE mentors SET verification_status = 'verified', verified_by = ?, verified_at = NOW(),
             verification_note = ? WHERE id = ?",
            [$verifierId, mb_substr($note, 0, 500) ?: null, $mentorId]
        );
        ActivityLogService::log($verifierId, 'mentor_verified', 'mentor', $mentorId);
        NotificationService::create(
            (int) $mentor['user_id'],
            'mentor_verified',
            'Mentor verified',
            'Your mentor profile was verified by staff.',
            'mentor',
            $mentorId
        );
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items:list<array<string,mixed>>,total:int,page:int}
     */
    public static function list(array $filters, int $page = 1, int $perPage = 12, ?int $viewerId = null): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(40, $perPage));
        $where = ["m.verification_status <> 'suspended'", 'm.accepting_requests = 1', 'u.status = \'active\'', 'u.show_in_discovery = 1'];
        $params = [];

        if (!empty($filters['skill'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM mentor_skills ms INNER JOIN skills s ON s.id = ms.skill_id
                WHERE ms.mentor_id = m.id AND s.slug = ?
            )';
            $params[] = (string) $filters['skill'];
        }
        if (!empty($filters['verified'])) {
            $where[] = "m.verification_status = 'verified'";
        }
        if ($viewerId !== null) {
            $blocked = UserBlockService::blockedIdsFor($viewerId);
            if ($blocked !== []) {
                $in = implode(',', array_fill(0, count($blocked), '?'));
                $where[] = "u.id NOT IN ({$in})";
                foreach ($blocked as $b) {
                    $params[] = $b;
                }
            }
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM mentors m INNER JOIN users u ON u.id = m.user_id WHERE {$whereSql}",
            $params
        )['c'] ?? 0);
        $offset = ($page - 1) * $perPage;
        $items = Database::fetchAll(
            "SELECT m.*, u.username, u.full_name, u.bio AS user_bio,
                    (SELECT COUNT(*) FROM mentorships ms
                     WHERE ms.mentor_id = m.id AND ms.status IN ('active','accepted')) AS active_mentees
             FROM mentors m
             INNER JOIN users u ON u.id = m.user_id
             WHERE {$whereSql}
             ORDER BY FIELD(m.verification_status,'verified','unverified'), m.updated_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        foreach ($items as &$item) {
            $item['skills'] = Database::fetchAll(
                'SELECT s.name, s.slug, COALESCE(us.current_level, 0) AS level
                 FROM mentor_skills ms
                 INNER JOIN skills s ON s.id = ms.skill_id
                 LEFT JOIN user_skills us ON us.skill_id = s.id AND us.user_id = ?
                 WHERE ms.mentor_id = ?
                 ORDER BY s.name LIMIT 8',
                [(int) $item['user_id'], (int) $item['id']]
            );
        }
        unset($item);

        return ['items' => $items, 'total' => $total, 'page' => $page];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detailByUsername(string $username, ?int $viewerId): array
    {
        $user = Database::fetch(
            "SELECT id, username, full_name, bio, show_in_discovery, skills_visibility, status
             FROM users WHERE username = ? LIMIT 1",
            [$username]
        );
        if ($user === null || ($user['status'] ?? '') !== 'active') {
            throw new InvalidArgumentException('Mentor not found.', 404);
        }
        if ($viewerId !== null && UserBlockService::isBlockedEither($viewerId, (int) $user['id'])) {
            throw new InvalidArgumentException('Mentor not found.', 404);
        }
        $mentor = self::findByUserId((int) $user['id']);
        if ($mentor === null || ($mentor['verification_status'] ?? '') === 'suspended') {
            throw new InvalidArgumentException('Mentor not found.', 404);
        }

        $skills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug, COALESCE(us.current_level, 0) AS level
             FROM mentor_skills ms
             INNER JOIN skills s ON s.id = ms.skill_id
             LEFT JOIN user_skills us ON us.skill_id = s.id AND us.user_id = ?
             WHERE ms.mentor_id = ?
             ORDER BY us.current_level DESC, s.name',
            [(int) $user['id'], (int) $mentor['id']]
        );

        $active = (int) (Database::fetch(
            "SELECT COUNT(*) AS c FROM mentorships
             WHERE mentor_id = ? AND status IN ('active','accepted','pending')",
            [(int) $mentor['id']]
        )['c'] ?? 0);

        return [
            'user' => $user,
            'mentor' => $mentor,
            'skills' => $skills,
            'active_mentees' => $active,
            'slots_left' => max(0, (int) $mentor['max_mentees'] - $active),
        ];
    }
}
