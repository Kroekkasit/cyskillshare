<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SkillLevel;

final class SkillTreeService
{
    /**
     * @return list<array{category: SkillCategory, skills: list<array<string, mixed>>}>
     */
    public static function tree(?int $userId = null, ?string $search = null, ?string $categorySlug = null): array
    {
        $categories = SkillCategory::allActive();
        $levels = SkillLevel::mapByLevel();
        $progress = [];
        if ($userId !== null) {
            $rows = Database::fetchAll(
                'SELECT skill_id, current_level, progress_score, evidence_count FROM user_skills WHERE user_id = ?',
                [$userId]
            );
            foreach ($rows as $row) {
                $progress[(int) $row['skill_id']] = $row;
            }
        }

        $out = [];
        foreach ($categories as $cat) {
            if ($categorySlug !== null && $categorySlug !== '' && $cat->slug !== $categorySlug) {
                continue;
            }
            $skills = Skill::byCategory($cat->id);
            $nodes = [];
            foreach ($skills as $skill) {
                if ($search !== null && $search !== '') {
                    $q = mb_strtolower($search);
                    if (!str_contains(mb_strtolower($skill->name), $q)
                        && !str_contains(mb_strtolower($skill->description), $q)
                        && !str_contains(mb_strtolower($skill->slug), $q)
                    ) {
                        continue;
                    }
                }
                $p = $progress[$skill->id] ?? null;
                $level = (int) ($p['current_level'] ?? 0);
                $nodes[] = [
                    'skill' => $skill,
                    'level' => $level,
                    'level_name' => $levels[$level]->name ?? SkillLevel::nameFor($level),
                    'progress' => (int) ($p['progress_score'] ?? 0),
                    'evidence_count' => (int) ($p['evidence_count'] ?? 0),
                    'symbol' => self::levelSymbol($level),
                ];
            }
            if ($nodes === [] && ($search !== null && $search !== '')) {
                continue;
            }
            $out[] = [
                'category' => $cat,
                'skills' => $nodes,
                'roots' => array_values(array_filter(
                    $nodes,
                    static fn(array $n): bool => $n['skill']->parent_skill_id === null
                )),
                'children_map' => self::childrenMap($nodes),
            ];
        }
        return $out;
    }

    /**
     * @param list<array<string, mixed>> $nodes
     * @return array<int, list<array<string, mixed>>>
     */
    private static function childrenMap(array $nodes): array
    {
        $map = [];
        foreach ($nodes as $node) {
            /** @var Skill $skill */
            $skill = $node['skill'];
            $pid = $skill->parent_skill_id;
            if ($pid === null) {
                continue;
            }
            $map[$pid][] = $node;
        }
        return $map;
    }

    public static function levelSymbol(int $level): string
    {
        return match ($level) {
            0 => '○',
            1 => '◐',
            2 => '◑',
            3 => '●',
            4 => '◆',
            5 => '✓',
            default => '○',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function skillDetail(string $slug, ?int $viewerId): array
    {
        $skill = Skill::findBySlug($slug);
        if ($skill === null) {
            throw new \InvalidArgumentException('Skill not found.', 404);
        }

        $category = SkillCategory::find($skill->category_id);
        $progress = $viewerId !== null
            ? SkillProgressService::getUserSkill($viewerId, $skill->id)
            : ['level' => 0, 'name' => SkillLevel::nameFor(0), 'progress' => 0, 'evidence_count' => 0];

        $evidence = [];
        $checklistByLevel = [];
        $why = [];
        if ($viewerId !== null) {
            $evidence = Database::fetchAll(
                "SELECT e.*, c.difficulty AS challenge_difficulty
                 FROM skill_evidence e
                 LEFT JOIN challenges c ON e.source_type = 'challenge' AND e.source_id = c.id
                 WHERE e.user_id = ? AND e.skill_id = ? AND e.status = 'accepted'
                 ORDER BY e.created_at DESC",
                [$viewerId, $skill->id]
            );
            $reqs = Database::fetchAll(
                'SELECT * FROM skill_requirements WHERE skill_id = ? ORDER BY target_level, id',
                [$skill->id]
            );
            for ($lvl = 1; $lvl <= 5; $lvl++) {
                $levelReqs = array_values(array_filter(
                    $reqs,
                    static fn(array $r): bool => (int) $r['target_level'] === $lvl
                ));
                if ($levelReqs === []) {
                    continue;
                }
                $checklistByLevel[$lvl] = SkillProgressService::requirementChecklist($levelReqs, $evidence);
            }
            $current = (int) $progress['level'];
            if ($current > 0 && isset($checklistByLevel[$current])) {
                $why = $checklistByLevel[$current];
            }
        } else {
            $reqs = Database::fetchAll(
                'SELECT * FROM skill_requirements WHERE skill_id = ? ORDER BY target_level, id',
                [$skill->id]
            );
            for ($lvl = 1; $lvl <= 5; $lvl++) {
                $levelReqs = array_values(array_filter(
                    $reqs,
                    static fn(array $r): bool => (int) $r['target_level'] === $lvl
                ));
                if ($levelReqs !== []) {
                    $checklistByLevel[$lvl] = SkillProgressService::requirementChecklist($levelReqs, []);
                }
            }
        }

        $prereqs = $skill->prerequisites();
        $prereqStatus = [];
        foreach ($prereqs as $pre) {
            $up = $viewerId !== null
                ? SkillProgressService::getUserSkill($viewerId, (int) $pre['id'])
                : ['level' => 0];
            $prereqStatus[] = [
                'id' => (int) $pre['id'],
                'name' => (string) $pre['name'],
                'slug' => (string) $pre['slug'],
                'minimum_level' => (int) $pre['minimum_level'],
                'user_level' => (int) ($up['level'] ?? 0),
                'met' => (int) ($up['level'] ?? 0) >= (int) $pre['minimum_level'],
            ];
        }

        $nextSteps = self::suggestedNextSteps($skill->id, $viewerId, (int) $progress['level']);
        $learning = KnowledgeArticleService::forSkill($skill->slug, $viewerId);

        return [
            'skill' => $skill,
            'category' => $category,
            'progress' => $progress,
            'evidence' => $evidence,
            'checklist_by_level' => $checklistByLevel,
            'why' => $why,
            'prerequisites' => $prereqStatus,
            'next_steps' => $nextSteps,
            'levels' => SkillLevel::mapByLevel(),
            'children' => Skill::childrenOf($skill->id),
            'learn_articles' => $learning['articles'],
            'learn_writeups' => $learning['writeups'],
        ];
    }

    /**
     * @return list<array{type:string,label:string,url:?string}>
     */
    private static function suggestedNextSteps(int $skillId, ?int $userId, int $currentLevel): array
    {
        $steps = [];
        $target = min(5, $currentLevel + 1);
        $reqs = Database::fetchAll(
            'SELECT * FROM skill_requirements WHERE skill_id = ? AND target_level = ?',
            [$skillId, $target]
        );

        $hasChallengeReq = false;
        foreach ($reqs as $req) {
            if (str_contains((string) $req['evidence_type'], 'challenge')) {
                $hasChallengeReq = true;
            }
            if (($req['evidence_type'] ?? '') === 'writeup') {
                $steps[] = [
                    'type' => 'writeup',
                    'label' => 'Write a technical writeup for this skill (coming soon)',
                    'url' => null,
                ];
            }
            if (($req['evidence_type'] ?? '') === 'community_best_answer') {
                $steps[] = [
                    'type' => 'community',
                    'label' => 'Contribute a high-quality answer in Community',
                    'url' => '/community',
                ];
            }
        }

        if ($hasChallengeReq || $reqs === []) {
            $challenges = Database::fetchAll(
                "SELECT c.id, c.title, c.difficulty
                 FROM challenge_skills cs
                 INNER JOIN challenges c ON c.id = cs.challenge_id
                 WHERE cs.skill_id = ? AND c.status = 'published' AND c.is_active = 1
                 ORDER BY FIELD(c.difficulty,'easy','medium','hard','expert'), c.points ASC
                 LIMIT 5",
                [$skillId]
            );
            foreach ($challenges as $c) {
                if ($userId !== null) {
                    $solved = Database::fetch(
                        'SELECT id FROM challenge_solves WHERE challenge_id = ? AND user_id = ? LIMIT 1',
                        [(int) $c['id'], $userId]
                    );
                    if ($solved !== null) {
                        continue;
                    }
                }
                $steps[] = [
                    'type' => 'challenge',
                    'label' => 'Solve: ' . $c['title'] . ' (' . $c['difficulty'] . ')',
                    'url' => '/arena/challenges/' . (int) $c['id'],
                ];
                if (count($steps) >= 4) {
                    break;
                }
            }
        }

        return array_slice($steps, 0, 5);
    }

    /**
     * @return array<string, int>
     */
    public static function userStats(int $userId): array
    {
        $row = Database::fetch(
            'SELECT
               COUNT(*) AS started,
               SUM(CASE WHEN current_level >= 1 THEN 1 ELSE 0 END) AS beginner_plus,
               SUM(CASE WHEN current_level >= 3 THEN 1 ELSE 0 END) AS intermediate_plus,
               SUM(CASE WHEN current_level >= 4 THEN 1 ELSE 0 END) AS advanced_plus,
               SUM(CASE WHEN current_level >= 5 THEN 1 ELSE 0 END) AS demonstrated
             FROM user_skills WHERE user_id = ? AND evidence_count > 0',
            [$userId]
        );
        return [
            'started' => (int) ($row['started'] ?? 0),
            'beginner_plus' => (int) ($row['beginner_plus'] ?? 0),
            'intermediate_plus' => (int) ($row['intermediate_plus'] ?? 0),
            'advanced_plus' => (int) ($row['advanced_plus'] ?? 0),
            'demonstrated' => (int) ($row['demonstrated'] ?? 0),
        ];
    }

    public static function canViewSkills(int $profileUserId, ?int $viewerId): bool
    {
        $row = Database::fetch(
            'SELECT skills_visibility FROM users WHERE id = ? LIMIT 1',
            [$profileUserId]
        );
        $vis = (string) ($row['skills_visibility'] ?? 'public');
        if ($viewerId !== null && $viewerId === $profileUserId) {
            return true;
        }
        return match ($vis) {
            'public' => true,
            'community' => $viewerId !== null,
            'private' => false,
            default => true,
        };
    }
}
