<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ChallengeSolve;
use App\Models\Tag;
use InvalidArgumentException;
use RuntimeException;

final class ChallengeService
{
    public static function canManageArena(): bool
    {
        return Auth::hasAnyRole(['instructor', 'moderator', 'admin']);
    }

    public static function requireManageArena(): void
    {
        Auth::requireLogin();
        if (!self::canManageArena()) {
            throw new RuntimeException('Forbidden', 403);
        }
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{items: list<array<string, mixed>>, total: int, page: int, per_page: int}
     */
    public static function listPublished(array $filters, int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(48, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = ["c.status = 'published'", 'c.is_active = 1'];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = 'cat.slug = ?';
            $params[] = (string) $filters['category'];
        }
        if (!empty($filters['difficulty']) && in_array($filters['difficulty'], ['easy', 'medium', 'hard', 'expert'], true)) {
            $where[] = 'c.difficulty = ?';
            $params[] = $filters['difficulty'];
        }
        if (!empty($filters['search'])) {
            $q = '%' . (string) $filters['search'] . '%';
            $where[] = '(c.title LIKE ? OR c.description LIKE ? OR cat.name LIKE ? OR EXISTS (
                SELECT 1 FROM challenge_tags ct
                INNER JOIN tags t ON t.id = ct.tag_id
                WHERE ct.challenge_id = c.id AND (t.name LIKE ? OR t.slug LIKE ?)
            ))';
            array_push($params, $q, $q, $q, $q, $q);
        }

        $userId = Auth::id();
        $solveStatus = $filters['solve_status'] ?? 'all';
        if ($userId !== null && $solveStatus === 'solved') {
            $where[] = 'EXISTS (SELECT 1 FROM challenge_solves s WHERE s.challenge_id = c.id AND s.user_id = ?)';
            $params[] = $userId;
        } elseif ($userId !== null && $solveStatus === 'unsolved') {
            $where[] = 'NOT EXISTS (SELECT 1 FROM challenge_solves s WHERE s.challenge_id = c.id AND s.user_id = ?)';
            $params[] = $userId;
        }

        $orderMap = [
            'newest' => 'c.published_at DESC, c.id DESC',
            'oldest' => 'c.published_at ASC, c.id ASC',
            'points' => 'c.points DESC, c.id DESC',
            'most_solved' => 'solve_count DESC, c.id DESC',
            'least_solved' => 'solve_count ASC, c.id DESC',
            'difficulty' => "FIELD(c.difficulty, 'easy','medium','hard','expert'), c.id DESC",
        ];
        $sort = (string) ($filters['sort'] ?? 'newest');
        $orderBy = $orderMap[$sort] ?? $orderMap['newest'];

        $whereSql = implode(' AND ', $where);

        $countRow = Database::fetch(
            "SELECT COUNT(*) AS cnt
             FROM challenges c
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE {$whereSql}",
            $params
        );
        $total = (int) ($countRow['cnt'] ?? 0);

        $sql = "SELECT c.id, c.title, c.slug, c.description, c.difficulty, c.points, c.is_featured,
                       c.published_at, c.created_at,
                       cat.name AS category_name, cat.slug AS category_slug,
                       (SELECT COUNT(*) FROM challenge_solves s WHERE s.challenge_id = c.id) AS solve_count
                FROM challenges c
                INNER JOIN challenge_categories cat ON cat.id = c.category_id
                WHERE {$whereSql}
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        $items = Database::fetchAll($sql, $params);
        $ids = array_map(static fn(array $r): int => (int) $r['id'], $items);
        $tags = Tag::forChallenges($ids);
        $solved = $userId !== null ? ChallengeSolve::solvedSetForUser($userId, $ids) : [];

        foreach ($items as &$item) {
            $id = (int) $item['id'];
            $item['tags'] = $tags[$id] ?? [];
            $item['solved'] = isset($solved[$id]);
            $item['excerpt'] = mb_strimwidth(strip_tags((string) $item['description']), 0, 140, '…');
            unset($item['description']);
        }
        unset($item);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dashboardStats(?int $userId): array
    {
        $total = Database::fetch(
            "SELECT COUNT(*) AS cnt FROM challenges WHERE status = 'published' AND is_active = 1"
        );
        $solved = 0;
        $points = 0;
        $streak = 0;
        if ($userId !== null) {
            $s = Database::fetch(
                'SELECT COUNT(*) AS cnt, COALESCE(SUM(points_awarded), 0) AS pts
                 FROM challenge_solves WHERE user_id = ?',
                [$userId]
            );
            $solved = (int) ($s['cnt'] ?? 0);
            $points = (int) ($s['pts'] ?? 0);
            $streak = self::currentStreak($userId);
        }

        return [
            'challenges' => (int) ($total['cnt'] ?? 0),
            'solved' => $solved,
            'points' => $points,
            'streak' => $streak,
        ];
    }

    private static function currentStreak(int $userId): int
    {
        $rows = Database::fetchAll(
            'SELECT DISTINCT DATE(solved_at) AS d
             FROM challenge_solves WHERE user_id = ?
             ORDER BY d DESC LIMIT 60',
            [$userId]
        );
        if ($rows === []) {
            return 0;
        }
        $dates = array_map(static fn(array $r): string => (string) $r['d'], $rows);
        $today = (new \DateTimeImmutable('today'))->format('Y-m-d');
        $yesterday = (new \DateTimeImmutable('yesterday'))->format('Y-m-d');
        if ($dates[0] !== $today && $dates[0] !== $yesterday) {
            return 0;
        }
        $streak = 0;
        $expect = new \DateTimeImmutable($dates[0]);
        foreach ($dates as $d) {
            if ($d === $expect->format('Y-m-d')) {
                $streak++;
                $expect = $expect->modify('-1 day');
            } else {
                break;
            }
        }
        return $streak;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featured(int $limit = 3): array
    {
        $limit = max(1, min(12, $limit));
        return Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, c.points, cat.name AS category_name, cat.slug AS category_slug
             FROM challenges c
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE c.status = 'published' AND c.is_active = 1 AND c.is_featured = 1
             ORDER BY c.published_at DESC
             LIMIT {$limit}"
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function recommended(?int $userId, int $limit = 3): array
    {
        $limit = max(1, min(12, $limit));
        $params = [];
        $extra = '';
        if ($userId !== null) {
            $extra = 'AND NOT EXISTS (
                SELECT 1 FROM challenge_solves s WHERE s.challenge_id = c.id AND s.user_id = ?
            )';
            $params[] = $userId;
        }
        return Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, c.points, cat.name AS category_name
             FROM challenges c
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE c.status = 'published' AND c.is_active = 1 {$extra}
             ORDER BY FIELD(c.difficulty, 'easy','medium','hard','expert'), c.points ASC
             LIMIT {$limit}",
            $params
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function continueLearning(?int $userId): ?array
    {
        if ($userId === null) {
            return null;
        }
        $row = Database::fetch(
            "SELECT c.id, c.title, cat.name AS category_name, 'Solved' AS progress
             FROM challenge_solves s
             INNER JOIN challenges c ON c.id = s.challenge_id
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE s.user_id = ?
             ORDER BY s.solved_at DESC LIMIT 1",
            [$userId]
        );
        if ($row !== null) {
            return $row;
        }
        $rec = self::recommended($userId, 1);
        if ($rec === []) {
            return null;
        }
        return [
            'id' => $rec[0]['id'],
            'title' => $rec[0]['title'],
            'category_name' => $rec[0]['category_name'],
            'progress' => 'Not started',
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(int $authorId, array $data): Challenge
    {
        self::requireManageArena();

        $category = ChallengeCategory::find((int) $data['category_id']);
        if ($category === null) {
            throw new InvalidArgumentException('Invalid category.');
        }

        $flag = (string) ($data['flag'] ?? '');
        if ($flag === '' || mb_strlen($flag) > 500) {
            throw new InvalidArgumentException('Invalid flag.');
        }

        $caseSensitive = !empty($data['case_sensitive']);
        $hash = ChallengeScoringService::hashFlag($flag, $caseSensitive);
        $slug = Challenge::uniqueSlug((string) ($data['slug'] ?: $data['title']));
        $status = in_array($data['status'] ?? 'draft', ['draft', 'published'], true)
            ? (string) $data['status']
            : 'draft';
        $difficulty = (string) $data['difficulty'];
        if (!in_array($difficulty, ['easy', 'medium', 'hard', 'expert'], true)) {
            throw new InvalidArgumentException('Invalid difficulty.');
        }
        $points = max(1, min(10000, (int) $data['points']));

        Database::beginTransaction();
        try {
            Database::execute(
                'INSERT INTO challenges
                 (title, slug, description, category_id, difficulty, points, author_id,
                  status, flag_type, flag_hash, case_sensitive, is_active, is_featured, published_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)',
                [
                    (string) $data['title'],
                    $slug,
                    (string) $data['description'],
                    $category->id,
                    $difficulty,
                    $points,
                    $authorId,
                    $status,
                    'static',
                    $hash,
                    $caseSensitive ? 1 : 0,
                    !empty($data['is_featured']) ? 1 : 0,
                    $status === 'published' ? date('Y-m-d H:i:s') : null,
                ]
            );
            $id = (int) Database::lastInsertId();
            Tag::syncForChallenge($id, $data['tags'] ?? []);
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($authorId, 'challenge_created', 'challenge', $id);
        if ($status === 'published') {
            ActivityLogService::log($authorId, 'challenge_published', 'challenge', $id);
        }

        $challenge = Challenge::find($id);
        if ($challenge === null) {
            throw new RuntimeException('Challenge missing after create.');
        }
        return $challenge;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function update(Challenge $challenge, int $actorId, array $data, bool $confirmScoringChange = false): Challenge
    {
        self::requireManageArena();

        $solveCount = ChallengeSolve::countForChallenge($challenge->id);
        $newFlag = isset($data['flag']) ? trim((string) $data['flag']) : '';
        $newPoints = isset($data['points']) ? (int) $data['points'] : $challenge->points;
        $scoringChange = ($newFlag !== '') || ($newPoints !== $challenge->points);

        if ($solveCount > 0 && $scoringChange && !$confirmScoringChange) {
            throw new InvalidArgumentException(
                'This challenge has solves. Confirm changing flag/points to continue.'
            );
        }

        $category = ChallengeCategory::find((int) ($data['category_id'] ?? $challenge->category_id));
        if ($category === null) {
            throw new InvalidArgumentException('Invalid category.');
        }

        $difficulty = (string) ($data['difficulty'] ?? $challenge->difficulty);
        if (!in_array($difficulty, ['easy', 'medium', 'hard', 'expert'], true)) {
            throw new InvalidArgumentException('Invalid difficulty.');
        }

        $status = (string) ($data['status'] ?? $challenge->status);
        if (!in_array($status, ['draft', 'published', 'archived'], true)) {
            $status = $challenge->status;
        }

        $caseSensitive = array_key_exists('case_sensitive', $data)
            ? !empty($data['case_sensitive'])
            : $challenge->case_sensitive;

        $sets = [
            'title = ?',
            'description = ?',
            'category_id = ?',
            'difficulty = ?',
            'points = ?',
            'status = ?',
            'case_sensitive = ?',
            'is_featured = ?',
            'is_active = ?',
        ];
        $params = [
            (string) $data['title'],
            (string) $data['description'],
            $category->id,
            $difficulty,
            max(1, min(10000, $newPoints)),
            $status,
            $caseSensitive ? 1 : 0,
            !empty($data['is_featured']) ? 1 : 0,
            array_key_exists('is_active', $data) ? (!empty($data['is_active']) ? 1 : 0) : 1,
        ];

        if (!empty($data['slug'])) {
            $sets[] = 'slug = ?';
            $params[] = Challenge::uniqueSlug((string) $data['slug'], $challenge->id);
        }

        if ($newFlag !== '') {
            $sets[] = 'flag_hash = ?';
            $params[] = ChallengeScoringService::hashFlag($newFlag, $caseSensitive);
        }

        if ($status === 'published' && $challenge->published_at === null) {
            $sets[] = 'published_at = NOW()';
        }

        $params[] = $challenge->id;

        Database::beginTransaction();
        try {
            Database::execute(
                'UPDATE challenges SET ' . implode(', ', $sets) . ' WHERE id = ?',
                $params
            );
            if (isset($data['tags'])) {
                Tag::syncForChallenge($challenge->id, $data['tags']);
            }
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            throw $e;
        }

        ActivityLogService::log($actorId, 'challenge_updated', 'challenge', $challenge->id);
        if ($status === 'published' && $challenge->status !== 'published') {
            ActivityLogService::log($actorId, 'challenge_published', 'challenge', $challenge->id);
        }
        if ($status === 'archived' && $challenge->status !== 'archived') {
            ActivityLogService::log($actorId, 'challenge_archived', 'challenge', $challenge->id);
        }

        $updated = Challenge::find($challenge->id);
        if ($updated === null) {
            throw new RuntimeException('Challenge missing after update.');
        }
        return $updated;
    }

    public static function setStatus(Challenge $challenge, int $actorId, string $status): void
    {
        self::requireManageArena();
        if (!in_array($status, ['draft', 'published', 'archived'], true)) {
            throw new InvalidArgumentException('Invalid status.');
        }
        $sql = "UPDATE challenges SET status = ?";
        $params = [$status];
        if ($status === 'published') {
            $sql .= ', published_at = COALESCE(published_at, NOW()), is_active = 1';
        }
        $sql .= ' WHERE id = ?';
        $params[] = $challenge->id;
        Database::execute($sql, $params);

        $action = match ($status) {
            'published' => 'challenge_published',
            'archived' => 'challenge_archived',
            default => 'challenge_updated',
        };
        ActivityLogService::log($actorId, $action, 'challenge', $challenge->id);
    }

    /**
     * @return array{solves:int, attempts:int, solve_rate:int}
     */
    public static function stats(int $challengeId): array
    {
        $solves = ChallengeSolve::countForChallenge($challengeId);
        $attempts = Database::fetch(
            'SELECT COUNT(*) AS cnt FROM challenge_submissions WHERE challenge_id = ?',
            [$challengeId]
        );
        $att = (int) ($attempts['cnt'] ?? 0);
        $rate = $att > 0 ? (int) round(($solves / $att) * 100) : 0;
        return ['solves' => $solves, 'attempts' => $att, 'solve_rate' => $rate];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function adminList(): array
    {
        return Database::fetchAll(
            "SELECT c.id, c.title, c.difficulty, c.points, c.status, c.is_featured, c.updated_at,
                    cat.name AS category_name, u.username AS author_name,
                    (SELECT COUNT(*) FROM challenge_solves s WHERE s.challenge_id = c.id) AS solve_count
             FROM challenges c
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             INNER JOIN users u ON u.id = c.author_id
             ORDER BY c.updated_at DESC"
        );
    }
}
