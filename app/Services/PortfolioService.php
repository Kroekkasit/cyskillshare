<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\SkillLevel;
use App\Models\User;
use InvalidArgumentException;
use RuntimeException;

final class PortfolioService
{
    /**
     * @return array<string, mixed>|null
     */
    public static function findByUserId(int $userId): ?array
    {
        return Database::fetch('SELECT * FROM portfolios WHERE user_id = ? LIMIT 1', [$userId]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function ensure(int $userId): array
    {
        $existing = self::findByUserId($userId);
        if ($existing !== null) {
            return $existing;
        }
        $sections = json_encode(config('portfolio.default_sections', []), JSON_UNESCAPED_UNICODE);
        Database::execute(
            'INSERT INTO portfolios (user_id, sections_json) VALUES (?, ?)',
            [$userId, $sections]
        );
        ActivityLogService::log($userId, 'portfolio_updated', 'portfolio', (int) Database::lastInsertId());
        $row = self::findByUserId($userId);
        if ($row === null) {
            throw new RuntimeException('Failed to create portfolio.');
        }
        return $row;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function updateSettings(int $userId, array $data): void
    {
        self::ensure($userId);

        $visibility = (string) ($data['visibility'] ?? 'public');
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            throw new InvalidArgumentException('Invalid visibility.');
        }

        foreach (['github_url', 'linkedin_url', 'website_url', 'resume_url'] as $key) {
            $url = isset($data[$key]) ? trim((string) $data[$key]) : '';
            if ($url !== '' && !PortfolioVisibilityService::isSafeUrl($url)) {
                throw new InvalidArgumentException('Invalid URL for ' . $key . '.');
            }
            $data[$key] = $url !== '' ? $url : null;
        }

        $defaults = config('portfolio.default_sections', []);
        $sections = is_array($defaults) ? $defaults : [];
        if (isset($data['sections']) && is_array($data['sections'])) {
            foreach ($sections as $key => $_) {
                $sections[$key] = !empty($data['sections'][$key]);
            }
        }

        Database::execute(
            'UPDATE portfolios SET
               is_enabled = ?, visibility = ?, headline = ?, about = ?,
               university = ?, program = ?, graduation_year = ?, location = ?,
               show_location = ?, show_email = ?,
               github_url = ?, linkedin_url = ?, website_url = ?, resume_url = ?,
               featured_project_limit = ?, sections_json = ?,
               show_challenge_stats = ?, show_skill_evidence = ?, show_community_stats = ?
             WHERE user_id = ?',
            [
                !empty($data['is_enabled']) ? 1 : 0,
                $visibility,
                self::nullStr($data['headline'] ?? null, 200),
                self::nullStr($data['about'] ?? null, 20000),
                self::nullStr($data['university'] ?? null, 150),
                self::nullStr($data['program'] ?? null, 150),
                !empty($data['graduation_year']) ? (int) $data['graduation_year'] : null,
                self::nullStr($data['location'] ?? null, 120),
                !empty($data['show_location']) ? 1 : 0,
                !empty($data['show_email']) ? 1 : 0,
                $data['github_url'],
                $data['linkedin_url'],
                $data['website_url'],
                $data['resume_url'],
                max(1, min(6, (int) ($data['featured_project_limit'] ?? 3))),
                json_encode($sections, JSON_UNESCAPED_UNICODE),
                !empty($data['show_challenge_stats']) ? 1 : 0,
                !empty($data['show_skill_evidence']) ? 1 : 0,
                !empty($data['show_community_stats']) ? 1 : 0,
                $userId,
            ]
        );

        ActivityLogService::log($userId, 'portfolio_updated', 'user', $userId);
    }

    private static function nullStr(mixed $v, int $max): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        return mb_substr($s, 0, $max);
    }

    /**
     * @return array<string, mixed>
     */
    public static function publicView(string $username, ?int $viewerId): array
    {
        $user = User::findByUsername($username);
        if ($user === null || $user->status !== 'active') {
            throw new InvalidArgumentException('Portfolio not found.', 404);
        }

        $portfolio = self::ensure($user->id);
        if (!PortfolioVisibilityService::canViewPortfolio($portfolio, $user->id, $viewerId)) {
            throw new RuntimeException('This portfolio is not available.', 403);
        }

        if ($viewerId !== $user->id) {
            PortfolioAnalyticsService::increment($user->id, 'portfolio_view');
        }

        $sections = json_decode((string) ($portfolio['sections_json'] ?? '{}'), true);
        if (!is_array($sections)) {
            $sections = config('portfolio.default_sections', []);
        }

        return [
            'user' => $user,
            'portfolio' => $portfolio,
            'sections' => $sections,
            'featured_skills' => self::featuredSkills($user->id, (bool) $portfolio['show_skill_evidence']),
            'featured_projects' => ProjectService::featuredForUser($user->id, $viewerId, (int) $portfolio['featured_project_limit']),
            'projects' => ProjectService::publishedForUser($user->id, $viewerId),
            'challenge_stats' => !empty($portfolio['show_challenge_stats'])
                ? self::challengeStats($user->id)
                : [],
            'community_stats' => !empty($portfolio['show_community_stats'])
                ? self::communityStats($user->id)
                : [],
            'education' => self::education($user->id),
            'experience' => self::experience($user->id, $viewerId),
            'certifications' => self::certifications($user->id, $viewerId),
            'is_owner' => $viewerId === $user->id,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featuredSkills(int $userId, bool $withEvidencePreview): array
    {
        $rows = Database::fetchAll(
            'SELECT s.id, s.name, s.slug, us.current_level, us.evidence_count, us.progress_score, pfs.display_order
             FROM portfolio_featured_skills pfs
             INNER JOIN skills s ON s.id = pfs.skill_id AND s.is_active = 1
             LEFT JOIN user_skills us ON us.skill_id = s.id AND us.user_id = ?
             WHERE pfs.user_id = ?
             ORDER BY pfs.display_order ASC, us.current_level DESC
             LIMIT 12',
            [$userId, $userId]
        );

        if ($rows === []) {
            // Fallback: top skills by level
            $rows = Database::fetchAll(
                'SELECT s.id, s.name, s.slug, us.current_level, us.evidence_count, us.progress_score, 0 AS display_order
                 FROM user_skills us
                 INNER JOIN skills s ON s.id = us.skill_id AND s.is_active = 1
                 WHERE us.user_id = ? AND us.evidence_count > 0
                 ORDER BY us.current_level DESC, us.evidence_count DESC
                 LIMIT 6',
                [$userId]
            );
        }

        $out = [];
        foreach ($rows as $row) {
            $level = (int) ($row['current_level'] ?? 0);
            $item = [
                'id' => (int) $row['id'],
                'name' => (string) $row['name'],
                'slug' => (string) $row['slug'],
                'level' => $level,
                'level_name' => SkillLevel::nameFor($level),
                'evidence_count' => (int) ($row['evidence_count'] ?? 0),
                'progress' => (int) ($row['progress_score'] ?? 0),
            ];
            if ($withEvidencePreview) {
                $item['evidence_summary'] = self::evidenceSummary($userId, (int) $row['id']);
            }
            $out[] = $item;
        }
        return $out;
    }

    /**
     * @return array<string, int>
     */
    private static function evidenceSummary(int $userId, int $skillId): array
    {
        $rows = Database::fetchAll(
            "SELECT evidence_type, COUNT(*) AS cnt
             FROM skill_evidence
             WHERE user_id = ? AND skill_id = ? AND status = 'accepted'
             GROUP BY evidence_type",
            [$userId, $skillId]
        );
        $map = [
            'challenges' => 0,
            'writeups' => 0,
            'projects' => 0,
            'best_answers' => 0,
            'labs' => 0,
            'verified' => 0,
        ];
        foreach ($rows as $row) {
            $t = (string) $row['evidence_type'];
            $c = (int) $row['cnt'];
            if (str_contains($t, 'challenge')) {
                $map['challenges'] += $c;
            } elseif ($t === 'writeup') {
                $map['writeups'] += $c;
            } elseif ($t === 'project') {
                $map['projects'] += $c;
            } elseif ($t === 'community_best_answer') {
                $map['best_answers'] += $c;
            } elseif ($t === 'lab') {
                $map['labs'] += $c;
            } elseif ($t === 'instructor_verification') {
                $map['verified'] += $c;
            }
        }
        return $map;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function challengeStats(int $userId): array
    {
        return Database::fetchAll(
            "SELECT cat.name, COUNT(*) AS solved
             FROM challenge_solves s
             INNER JOIN challenges c ON c.id = s.challenge_id
             INNER JOIN challenge_categories cat ON cat.id = c.category_id
             WHERE s.user_id = ?
             GROUP BY cat.id, cat.name
             ORDER BY solved DESC",
            [$userId]
        );
    }

    /**
     * @return array<string, int>
     */
    public static function communityStats(int $userId): array
    {
        $user = User::find($userId);
        return [
            'best_answers' => $user?->bestAnswerCount() ?? 0,
            'discussions' => $user?->discussionCount() ?? 0,
            'replies' => $user?->replyCount() ?? 0,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function education(int $userId): array
    {
        return Database::fetchAll(
            'SELECT * FROM portfolio_education WHERE user_id = ? ORDER BY display_order, start_year DESC',
            [$userId]
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function experience(int $userId, ?int $viewerId): array
    {
        $rows = Database::fetchAll(
            'SELECT * FROM portfolio_experience WHERE user_id = ? ORDER BY display_order, start_date DESC',
            [$userId]
        );
        return array_values(array_filter(
            $rows,
            static fn(array $r): bool => PortfolioVisibilityService::canView(
                (string) $r['visibility'],
                $userId,
                $viewerId
            )
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function certifications(int $userId, ?int $viewerId): array
    {
        $rows = Database::fetchAll(
            'SELECT * FROM portfolio_certifications WHERE user_id = ? ORDER BY issued_date DESC',
            [$userId]
        );
        return array_values(array_filter(
            $rows,
            static fn(array $r): bool => PortfolioVisibilityService::canView(
                (string) $r['visibility'],
                $userId,
                $viewerId
            )
        ));
    }

    /**
     * @return array{percent:int,missing:list<string>,counts:array<string,int>}
     */
    public static function completeness(int $userId): array
    {
        $p = self::ensure($userId);
        $checks = [
            'Add a headline' => trim((string) ($p['headline'] ?? '')) !== '',
            'Write an about section' => trim((string) ($p['about'] ?? '')) !== '',
            'Add university/program' => trim((string) ($p['university'] ?? '')) !== '',
            'Add GitHub' => trim((string) ($p['github_url'] ?? '')) !== '',
            'Publish a project' => (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM projects WHERE user_id = ? AND publish_status = 'published'",
                [$userId]
            )['c'] ?? 0) > 0,
            'Feature a project' => (int) (Database::fetch(
                "SELECT COUNT(*) AS c FROM projects WHERE user_id = ? AND featured = 1 AND publish_status = 'published'",
                [$userId]
            )['c'] ?? 0) > 0,
            'Connect projects to skills' => (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM project_skills ps
                 INNER JOIN projects p ON p.id = ps.project_id WHERE p.user_id = ?',
                [$userId]
            )['c'] ?? 0) > 0,
            'Add education' => (int) (Database::fetch(
                'SELECT COUNT(*) AS c FROM portfolio_education WHERE user_id = ?',
                [$userId]
            )['c'] ?? 0) > 0,
        ];
        $done = count(array_filter($checks));
        $total = count($checks);
        $missing = [];
        foreach ($checks as $label => $ok) {
            if (!$ok) {
                $missing[] = $label;
            }
        }

        return [
            'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            'missing' => $missing,
            'counts' => [
                'projects' => (int) (Database::fetch(
                    'SELECT COUNT(*) AS c FROM projects WHERE user_id = ?',
                    [$userId]
                )['c'] ?? 0),
                'published_projects' => (int) (Database::fetch(
                    "SELECT COUNT(*) AS c FROM projects WHERE user_id = ? AND publish_status = 'published'",
                    [$userId]
                )['c'] ?? 0),
                'featured_skills' => (int) (Database::fetch(
                    'SELECT COUNT(*) AS c FROM portfolio_featured_skills WHERE user_id = ?',
                    [$userId]
                )['c'] ?? 0),
                'challenges' => (int) (Database::fetch(
                    'SELECT COUNT(*) AS c FROM challenge_solves WHERE user_id = ?',
                    [$userId]
                )['c'] ?? 0),
                'verified_projects' => (int) (Database::fetch(
                    "SELECT COUNT(DISTINCT pv.project_id) AS c
                     FROM project_verifications pv
                     INNER JOIN projects p ON p.id = pv.project_id
                     WHERE p.user_id = ? AND pv.status = 'verified'",
                    [$userId]
                )['c'] ?? 0),
            ],
        ];
    }

    /**
     * @param list<int> $skillIds
     */
    public static function setFeaturedSkills(int $userId, array $skillIds): void
    {
        $max = (int) config('portfolio.max_featured_skills', 6);
        Database::execute('DELETE FROM portfolio_featured_skills WHERE user_id = ?', [$userId]);
        $order = 0;
        foreach ($skillIds as $sid) {
            if ($order >= $max) {
                break;
            }
            $sid = (int) $sid;
            if ($sid <= 0) {
                continue;
            }
            Database::execute(
                'INSERT IGNORE INTO portfolio_featured_skills (user_id, skill_id, display_order) VALUES (?, ?, ?)',
                [$userId, $sid, $order]
            );
            $order++;
        }
    }
}
