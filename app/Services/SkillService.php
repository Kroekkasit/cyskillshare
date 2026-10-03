<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Skill;
use App\Models\SkillCategory;
use InvalidArgumentException;
use RuntimeException;

final class SkillService
{
    public static function canManage(): bool
    {
        $roles = config('skills.manager_roles', ['instructor', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function canRecalculate(): bool
    {
        $roles = config('skills.recalculate_roles', ['admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function requireManage(): void
    {
        Auth::requireLogin();
        if (!self::canManage()) {
            throw new RuntimeException('Forbidden', 403);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createSkill(array $data, int $actorId): Skill
    {
        self::requireManage();

        $category = SkillCategory::find((int) $data['category_id']);
        if ($category === null) {
            throw new InvalidArgumentException('Invalid category.');
        }

        $parentId = !empty($data['parent_skill_id']) ? (int) $data['parent_skill_id'] : null;
        if ($parentId !== null) {
            $parent = Skill::find($parentId);
            if ($parent === null) {
                throw new InvalidArgumentException('Invalid parent skill.');
            }
        }

        $slug = Skill::slugify((string) ($data['slug'] ?: $data['name']));
        if (Skill::findBySlug($slug) !== null || Database::fetch('SELECT id FROM skills WHERE slug = ?', [$slug])) {
            $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }

        Database::execute(
            'INSERT INTO skills
             (category_id, parent_skill_id, name, slug, description, icon, display_order, is_active, is_gated)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $category->id,
                $parentId,
                (string) $data['name'],
                $slug,
                (string) $data['description'],
                $data['icon'] ?? null,
                (int) ($data['display_order'] ?? 0),
                !empty($data['is_active']) ? 1 : 0,
                !empty($data['is_gated']) ? 1 : 0,
            ]
        );

        $id = (int) Database::lastInsertId();
        ActivityLogService::log($actorId, 'skill_created', 'skill', $id);
        $skill = Skill::find($id);
        if ($skill === null) {
            throw new RuntimeException('Skill missing after create.');
        }
        return $skill;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function updateSkill(Skill $skill, array $data, int $actorId): Skill
    {
        self::requireManage();

        $parentId = array_key_exists('parent_skill_id', $data)
            ? (!empty($data['parent_skill_id']) ? (int) $data['parent_skill_id'] : null)
            : $skill->parent_skill_id;

        if ($parentId !== null) {
            if ($parentId === $skill->id) {
                throw new InvalidArgumentException('A skill cannot be its own parent.');
            }
            if (self::wouldCreateParentCycle($skill->id, $parentId)) {
                throw new InvalidArgumentException('Circular parent relationship is not allowed.');
            }
        }

        $categoryId = (int) ($data['category_id'] ?? $skill->category_id);
        if (SkillCategory::find($categoryId) === null) {
            throw new InvalidArgumentException('Invalid category.');
        }

        Database::execute(
            'UPDATE skills SET
               category_id = ?, parent_skill_id = ?, name = ?, description = ?,
               icon = ?, display_order = ?, is_active = ?, is_gated = ?
             WHERE id = ?',
            [
                $categoryId,
                $parentId,
                (string) $data['name'],
                (string) $data['description'],
                $data['icon'] ?? null,
                (int) ($data['display_order'] ?? 0),
                !empty($data['is_active']) ? 1 : 0,
                !empty($data['is_gated']) ? 1 : 0,
                $skill->id,
            ]
        );

        ActivityLogService::log($actorId, 'skill_updated', 'skill', $skill->id);
        $updated = Skill::find($skill->id);
        if ($updated === null) {
            throw new RuntimeException('Skill missing after update.');
        }
        return $updated;
    }

    private static function wouldCreateParentCycle(int $skillId, int $newParentId): bool
    {
        $current = $newParentId;
        $guard = 0;
        while ($current !== null && $guard < 50) {
            if ($current === $skillId) {
                return true;
            }
            $row = Database::fetch('SELECT parent_skill_id FROM skills WHERE id = ?', [$current]);
            $current = $row && $row['parent_skill_id'] !== null ? (int) $row['parent_skill_id'] : null;
            $guard++;
        }
        return false;
    }

    public static function addPrerequisite(int $skillId, int $prereqId, int $minLevel, int $actorId): void
    {
        self::requireManage();
        if ($skillId === $prereqId) {
            throw new InvalidArgumentException('A skill cannot require itself.');
        }
        if (self::wouldCreatePrerequisiteCycle($skillId, $prereqId)) {
            throw new InvalidArgumentException('Circular prerequisite is not allowed.');
        }
        Database::execute(
            'INSERT IGNORE INTO skill_prerequisites (skill_id, prerequisite_skill_id, minimum_level)
             VALUES (?, ?, ?)',
            [$skillId, $prereqId, max(1, min(5, $minLevel))]
        );
        ActivityLogService::log($actorId, 'skill_requirement_created', 'skill', $skillId, [
            'prerequisite_skill_id' => $prereqId,
        ]);
    }

    private static function wouldCreatePrerequisiteCycle(int $skillId, int $prereqId): bool
    {
        // If prereq already (transitively) requires skillId, adding skillId→prereq creates a cycle
        $queue = [$prereqId];
        $seen = [];
        while ($queue !== []) {
            $id = array_shift($queue);
            if ($id === $skillId) {
                return true;
            }
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $rows = Database::fetchAll(
                'SELECT prerequisite_skill_id FROM skill_prerequisites WHERE skill_id = ?',
                [$id]
            );
            foreach ($rows as $row) {
                $queue[] = (int) $row['prerequisite_skill_id'];
            }
        }
        return false;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function addRequirement(int $skillId, array $data, int $actorId): void
    {
        self::requireManage();
        $types = config('skills.evidence_types', []);
        $type = (string) ($data['evidence_type'] ?? '');
        if (!is_array($types) || !in_array($type, $types, true)) {
            throw new InvalidArgumentException('Invalid evidence type.');
        }
        $level = max(1, min(5, (int) ($data['target_level'] ?? 1)));
        $diff = $data['minimum_difficulty'] ?? null;
        if ($diff !== null && $diff !== '' && !in_array($diff, ['easy', 'medium', 'hard', 'expert'], true)) {
            throw new InvalidArgumentException('Invalid difficulty.');
        }

        Database::execute(
            'INSERT INTO skill_requirements
             (skill_id, target_level, evidence_type, minimum_count, minimum_difficulty, is_required, weight, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $skillId,
                $level,
                $type,
                max(1, (int) ($data['minimum_count'] ?? 1)),
                $diff !== '' ? $diff : null,
                !isset($data['is_required']) || !empty($data['is_required']) ? 1 : 0,
                (float) ($data['weight'] ?? 1),
                $data['description'] ?? null,
            ]
        );
        ActivityLogService::log($actorId, 'skill_requirement_created', 'skill', $skillId);
    }

    public static function deleteRequirement(int $requirementId, int $actorId): void
    {
        self::requireManage();
        $row = Database::fetch('SELECT skill_id FROM skill_requirements WHERE id = ?', [$requirementId]);
        Database::execute('DELETE FROM skill_requirements WHERE id = ?', [$requirementId]);
        if ($row) {
            ActivityLogService::log($actorId, 'skill_requirement_updated', 'skill', (int) $row['skill_id'], [
                'deleted_requirement_id' => $requirementId,
            ]);
        }
    }

    public static function setSkillsVisibility(int $userId, string $visibility): void
    {
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            throw new InvalidArgumentException('Invalid visibility.');
        }
        Database::execute('UPDATE users SET skills_visibility = ? WHERE id = ?', [$visibility, $userId]);
        ActivityLogService::log($userId, 'skill_privacy_updated', 'user', $userId, [
            'visibility' => $visibility,
        ]);
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    public static function skillsForChallenge(int $challengeId): array
    {
        return Database::fetchAll(
            'SELECT s.id, s.name, s.slug
             FROM challenge_skills cs
             INNER JOIN skills s ON s.id = cs.skill_id AND s.is_active = 1
             WHERE cs.challenge_id = ?
             ORDER BY cs.weight DESC, s.name ASC',
            [$challengeId]
        );
    }

    /**
     * @return list<array{id:int,name:string,slug:string}>
     */
    public static function skillsForThread(int $threadId): array
    {
        return Database::fetchAll(
            'SELECT s.id, s.name, s.slug
             FROM thread_skills ts
             INNER JOIN skills s ON s.id = ts.skill_id AND s.is_active = 1
             WHERE ts.thread_id = ?
             ORDER BY ts.weight DESC, s.name ASC',
            [$threadId]
        );
    }
}
