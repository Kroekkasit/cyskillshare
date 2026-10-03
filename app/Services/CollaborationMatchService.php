<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Deterministic, explainable collaboration recommendations (no black-box scores in UI).
 */
final class CollaborationMatchService
{
    /**
     * @return array{
     *   mentors:list<array<string,mixed>>,
     *   groups:list<array<string,mixed>>,
     *   teams:list<array<string,mixed>>,
     *   projects:list<array<string,mixed>>,
     *   recruitment:list<array<string,mixed>>
     * }
     */
    public static function recommended(?int $userId): array
    {
        if ($userId === null) {
            return [
                'mentors' => MentorService::list([], 1, 3)['items'],
                'groups' => GroupService::list(['type' => 'study'], 1, 3)['items'],
                'teams' => GroupService::list(['type' => 'ctf'], 1, 3)['items'],
                'projects' => GroupService::list(['type' => 'project'], 1, 3)['items'],
                'recruitment' => RecruitmentService::listOpen(3),
            ];
        }

        $skillSlugs = Database::fetchAll(
            'SELECT s.slug, s.name, us.current_level
             FROM user_skills us INNER JOIN skills s ON s.id = us.skill_id
             WHERE us.user_id = ? AND us.current_level > 0
             ORDER BY us.current_level DESC LIMIT 8',
            [$userId]
        );
        $slugs = array_column($skillSlugs, 'slug');
        $names = array_column($skillSlugs, 'name');

        $mentors = [];
        $groups = [];
        $teams = [];
        $projects = [];

        if ($slugs !== []) {
            $placeholders = implode(',', array_fill(0, count($slugs), '?'));
            $mentors = Database::fetchAll(
                "SELECT m.id, m.verification_status, u.username, u.full_name, m.bio,
                        GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS skill_names
                 FROM mentors m
                 INNER JOIN users u ON u.id = m.user_id AND u.status = 'active' AND u.show_in_discovery = 1
                 INNER JOIN mentor_skills ms ON ms.mentor_id = m.id
                 INNER JOIN skills s ON s.id = ms.skill_id AND s.slug IN ({$placeholders})
                 WHERE m.accepting_requests = 1 AND m.verification_status <> 'suspended' AND u.id <> ?
                 GROUP BY m.id
                 ORDER BY FIELD(m.verification_status,'verified','unverified')
                 LIMIT 4",
                array_merge($slugs, [$userId])
            );
            foreach ($mentors as &$m) {
                $m['reasons'] = [
                    'You are learning skills they mentor: ' . (string) $m['skill_names'] . '.',
                ];
            }
            unset($m);

            $groups = self::matchGroups($userId, $slugs, 'study', $names);
            $teams = self::matchGroups($userId, $slugs, 'ctf', $names);
            $projects = self::matchGroups($userId, $slugs, 'project', $names);
        }

        return [
            'mentors' => $mentors,
            'groups' => $groups,
            'teams' => $teams,
            'projects' => $projects,
            'recruitment' => RecruitmentService::listOpen(4, $slugs),
        ];
    }

    /**
     * @param list<string> $slugs
     * @param list<string> $names
     * @return list<array<string,mixed>>
     */
    private static function matchGroups(int $userId, array $slugs, string $type, array $names): array
    {
        $placeholders = implode(',', array_fill(0, count($slugs), '?'));
        $rows = Database::fetchAll(
            "SELECT g.id, g.name, g.slug, g.group_type, g.description,
                    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS skill_names,
                    (SELECT COUNT(*) FROM collab_group_members gm
                     WHERE gm.group_id = g.id AND gm.status = 'active') AS member_count
             FROM collab_groups g
             INNER JOIN collab_group_skills gs ON gs.group_id = g.id
             INNER JOIN skills s ON s.id = gs.skill_id AND s.slug IN ({$placeholders})
             WHERE g.status = 'active' AND g.group_type = ?
               AND g.visibility IN ('public','community')
               AND NOT EXISTS (
                 SELECT 1 FROM collab_group_members m
                 WHERE m.group_id = g.id AND m.user_id = ? AND m.status = 'active'
               )
             GROUP BY g.id
             ORDER BY COUNT(DISTINCT s.id) DESC, g.updated_at DESC
             LIMIT 4",
            array_merge($slugs, [$type, $userId])
        );
        foreach ($rows as &$r) {
            $focus = (string) ($r['skill_names'] ?? '');
            $r['reasons'] = [
                'This ' . $type . ' group focuses on ' . ($focus !== '' ? $focus : 'related skills')
                . (isset($names[0]) ? ', which overlaps with your learning.' : '.'),
            ];
        }
        unset($r);
        return $rows;
    }
}
