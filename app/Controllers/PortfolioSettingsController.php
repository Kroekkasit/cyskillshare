<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\PortfolioService;
use App\Services\PortfolioVisibilityService;
use InvalidArgumentException;

final class PortfolioSettingsController extends Controller
{
    public function editForm(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();

        $portfolio = PortfolioService::ensure($userId);
        $sections = json_decode((string) ($portfolio['sections_json'] ?? '{}'), true);
        if (!is_array($sections)) {
            $sections = config('portfolio.default_sections', []);
        }

        $featuredSkillIds = Database::fetchAll(
            'SELECT skill_id FROM portfolio_featured_skills WHERE user_id = ? ORDER BY display_order',
            [$userId]
        );
        $featuredSkillIds = array_map(static fn(array $r): int => (int) $r['skill_id'], $featuredSkillIds);

        $userSkills = Database::fetchAll(
            'SELECT s.id, s.name, s.slug, us.current_level
             FROM user_skills us
             INNER JOIN skills s ON s.id = us.skill_id AND s.is_active = 1
             WHERE us.user_id = ?
             ORDER BY us.current_level DESC, s.name ASC',
            [$userId]
        );

        $this->view('portfolio/settings', [
            'title' => 'Portfolio Settings — CySkillShare',
            'isPortfolio' => true,
            'portfolio' => $portfolio,
            'sections' => $sections,
            'featuredSkillIds' => $featuredSkillIds,
            'userSkills' => $userSkills,
            'education' => PortfolioService::education($userId),
            'experience' => PortfolioService::experience($userId, $userId),
            'certifications' => PortfolioService::certifications($userId, $userId),
            'sectionLabels' => self::sectionLabels(),
        ]);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $data = [
            'is_enabled' => $request->input('is_enabled'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'headline' => (string) $request->input('headline', ''),
            'about' => (string) $request->input('about', ''),
            'university' => (string) $request->input('university', ''),
            'program' => (string) $request->input('program', ''),
            'graduation_year' => $request->input('graduation_year'),
            'location' => (string) $request->input('location', ''),
            'show_location' => $request->input('show_location'),
            'show_email' => $request->input('show_email'),
            'github_url' => (string) $request->input('github_url', ''),
            'linkedin_url' => (string) $request->input('linkedin_url', ''),
            'website_url' => (string) $request->input('website_url', ''),
            'resume_url' => (string) $request->input('resume_url', ''),
            'featured_project_limit' => (int) $request->input('featured_project_limit', 3),
            'show_challenge_stats' => $request->input('show_challenge_stats'),
            'show_skill_evidence' => $request->input('show_skill_evidence'),
            'show_community_stats' => $request->input('show_community_stats'),
            'sections' => (array) $request->input('sections', []),
        ];

        try {
            PortfolioService::updateSettings($userId, $data);

            $skillIds = array_map('intval', (array) $request->input('skill_ids', []));
            PortfolioService::setFeaturedSkills($userId, $skillIds);

            $this->withSuccess('Portfolio settings saved.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/settings/portfolio');
    }

    public function addEducation(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $institution = trim((string) $request->input('institution', ''));
        if ($institution === '') {
            $this->withError('Institution is required.');
            $this->redirect('/settings/portfolio');
            return;
        }

        Database::execute(
            'INSERT INTO portfolio_education
             (user_id, institution, program, field, start_year, end_year, description, display_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                mb_substr($institution, 0, 200),
                self::nullStr($request->input('program'), 200),
                self::nullStr($request->input('field'), 200),
                self::nullInt($request->input('start_year')),
                self::nullInt($request->input('end_year')),
                self::nullStr($request->input('description'), 5000),
                (int) $request->input('display_order', 0),
            ]
        );

        $this->withSuccess('Education entry added.');
        $this->redirect('/settings/portfolio');
    }

    public function deleteEducation(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $id = (int) $request->param('id');

        Database::execute(
            'DELETE FROM portfolio_education WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );

        $this->withSuccess('Education entry removed.');
        $this->redirect('/settings/portfolio');
    }

    public function addExperience(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $organization = trim((string) $request->input('organization', ''));
        $role = trim((string) $request->input('role', ''));
        if ($organization === '' || $role === '') {
            $this->withError('Organization and role are required.');
            $this->redirect('/settings/portfolio');
            return;
        }

        $visibility = (string) $request->input('visibility', 'public');
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            $visibility = 'public';
        }

        Database::execute(
            'INSERT INTO portfolio_experience
             (user_id, organization, role, description, start_date, end_date, is_current, visibility, display_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                mb_substr($organization, 0, 200),
                mb_substr($role, 0, 200),
                self::nullStr($request->input('description'), 5000),
                self::nullDate($request->input('start_date')),
                self::nullDate($request->input('end_date')),
                !empty($request->input('is_current')) ? 1 : 0,
                $visibility,
                (int) $request->input('display_order', 0),
            ]
        );

        $this->withSuccess('Experience entry added.');
        $this->redirect('/settings/portfolio');
    }

    public function deleteExperience(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $id = (int) $request->param('id');

        Database::execute(
            'DELETE FROM portfolio_experience WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );

        $this->withSuccess('Experience entry removed.');
        $this->redirect('/settings/portfolio');
    }

    public function addCertification(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();

        $name = trim((string) $request->input('name', ''));
        if ($name === '') {
            $this->withError('Certification name is required.');
            $this->redirect('/settings/portfolio');
            return;
        }

        $credentialUrl = trim((string) $request->input('credential_url', ''));
        if ($credentialUrl !== '' && !PortfolioVisibilityService::isSafeUrl($credentialUrl)) {
            $this->withError('Invalid credential URL.');
            $this->redirect('/settings/portfolio');
            return;
        }

        $visibility = (string) $request->input('visibility', 'public');
        if (!in_array($visibility, ['public', 'community', 'private'], true)) {
            $visibility = 'public';
        }

        Database::execute(
            'INSERT INTO portfolio_certifications
             (user_id, name, issuer, credential_id, credential_url, issued_date, expiration_date,
              description, visibility, is_verified)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)',
            [
                $userId,
                mb_substr($name, 0, 200),
                self::nullStr($request->input('issuer'), 200),
                self::nullStr($request->input('credential_id'), 120),
                $credentialUrl !== '' ? $credentialUrl : null,
                self::nullDate($request->input('issued_date')),
                self::nullDate($request->input('expiration_date')),
                self::nullStr($request->input('description'), 5000),
                $visibility,
            ]
        );

        $this->withSuccess('Certification added (user-provided).');
        $this->redirect('/settings/portfolio');
    }

    public function deleteCertification(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $id = (int) $request->param('id');

        Database::execute(
            'DELETE FROM portfolio_certifications WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );

        $this->withSuccess('Certification removed.');
        $this->redirect('/settings/portfolio');
    }

    /**
     * @return array<string, string>
     */
    private static function sectionLabels(): array
    {
        return [
            'about' => 'About',
            'skills' => 'Featured Skills',
            'projects' => 'Projects',
            'challenges' => 'Challenge Stats',
            'writeups' => 'Writeups',
            'community' => 'Community',
            'education' => 'Education',
            'experience' => 'Experience',
            'certifications' => 'Certifications',
            'links' => 'Links',
        ];
    }

    private static function nullStr(mixed $v, int $max): ?string
    {
        if ($v === null) {
            return null;
        }
        $s = trim((string) $v);
        return $s === '' ? null : mb_substr($s, 0, $max);
    }

    private static function nullInt(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        return (int) $v;
    }

    private static function nullDate(mixed $v): ?string
    {
        if ($v === null || trim((string) $v) === '') {
            return null;
        }
        $d = trim((string) $v);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;
    }
}
