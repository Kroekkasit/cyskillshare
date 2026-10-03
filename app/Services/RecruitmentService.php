<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\RateLimiter;
use InvalidArgumentException;
use RuntimeException;

final class RecruitmentService
{
    /**
     * @param list<string> $preferSkillSlugs
     * @return list<array<string,mixed>>
     */
    public static function listOpen(int $limit = 10, array $preferSkillSlugs = []): array
    {
        $limit = max(1, min(30, $limit));
        if ($preferSkillSlugs === []) {
            return Database::fetchAll(
                "SELECT r.*, u.username, g.name AS group_name, g.slug AS group_slug
                 FROM recruitment_posts r
                 INNER JOIN users u ON u.id = r.creator_id
                 LEFT JOIN collab_groups g ON g.id = r.group_id
                 WHERE r.status = 'open' AND (r.expires_at IS NULL OR r.expires_at > NOW())
                 ORDER BY r.created_at DESC
                 LIMIT {$limit}"
            );
        }
        $placeholders = implode(',', array_fill(0, count($preferSkillSlugs), '?'));
        return Database::fetchAll(
            "SELECT r.*, u.username, g.name AS group_name, g.slug AS group_slug,
                    COUNT(DISTINCT rs.skill_id) AS skill_hits
             FROM recruitment_posts r
             INNER JOIN users u ON u.id = r.creator_id
             LEFT JOIN collab_groups g ON g.id = r.group_id
             LEFT JOIN recruitment_skills rs ON rs.recruitment_id = r.id
             LEFT JOIN skills s ON s.id = rs.skill_id AND s.slug IN ({$placeholders})
             WHERE r.status = 'open' AND (r.expires_at IS NULL OR r.expires_at > NOW())
             GROUP BY r.id
             ORDER BY skill_hits DESC, r.created_at DESC
             LIMIT {$limit}",
            $preferSkillSlugs
        );
    }

    /**
     * @param list<int> $skillIds
     */
    public static function create(int $creatorId, array $data, array $skillIds = []): array
    {
        $max = (int) config('collaboration.recruitment_max_per_day', 5);
        if (!RateLimiter::attempt($creatorId, 'recruitment_rl', $max, 86400)) {
            throw new RuntimeException('Too many recruitment posts today.', 429);
        }
        RateLimiter::hit($creatorId, 'recruitment_rl');

        $title = trim((string) ($data['title'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        if ($title === '' || $description === '') {
            throw new InvalidArgumentException('Title and description are required.');
        }
        $looking = in_array($data['looking_for'] ?? '', ['study','ctf','project','mentor','general'], true)
            ? (string) $data['looking_for'] : 'general';

        Database::execute(
            'INSERT INTO recruitment_posts
             (creator_id, group_id, title, description, looking_for, status, expires_at)
             VALUES (?, ?, ?, ?, ?, \'open\', DATE_ADD(NOW(), INTERVAL 30 DAY))',
            [
                $creatorId,
                !empty($data['group_id']) ? (int) $data['group_id'] : null,
                mb_substr($title, 0, 200),
                $description,
                $looking,
            ]
        );
        $id = (int) Database::lastInsertId();
        foreach ($skillIds as $skillId) {
            $skillId = (int) $skillId;
            if ($skillId > 0) {
                Database::execute(
                    'INSERT IGNORE INTO recruitment_skills (recruitment_id, skill_id, required) VALUES (?, ?, 1)',
                    [$id, $skillId]
                );
            }
        }
        ActivityLogService::log($creatorId, 'recruitment_created', 'recruitment', $id);
        $row = Database::fetch('SELECT * FROM recruitment_posts WHERE id = ?', [$id]);
        if ($row === null) {
            throw new RuntimeException('Recruitment missing after create.');
        }
        return $row;
    }

    public static function apply(int $recruitmentId, int $userId, string $message = ''): void
    {
        $post = Database::fetch('SELECT * FROM recruitment_posts WHERE id = ? LIMIT 1', [$recruitmentId]);
        if ($post === null || ($post['status'] ?? '') !== 'open') {
            throw new InvalidArgumentException('Recruitment post not available.');
        }
        if ((int) $post['creator_id'] === $userId) {
            throw new InvalidArgumentException('You cannot apply to your own post.');
        }
        Database::execute(
            "INSERT INTO recruitment_applications (recruitment_id, user_id, message, status)
             VALUES (?, ?, ?, 'pending')
             ON DUPLICATE KEY UPDATE message = VALUES(message), status = 'pending'",
            [$recruitmentId, $userId, mb_substr(trim($message), 0, 2000) ?: null]
        );
        ActivityLogService::log($userId, 'recruitment_application_submitted', 'recruitment', $recruitmentId);
        NotificationService::create(
            (int) $post['creator_id'],
            'team_application_received',
            'Recruitment application',
            'Someone applied to your recruitment post.',
            'recruitment',
            $recruitmentId
        );
    }
}
