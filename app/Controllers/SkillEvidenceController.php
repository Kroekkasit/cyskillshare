<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Skill;
use App\Services\SkillEvidenceService;
use InvalidArgumentException;

final class SkillEvidenceController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();

        $status = (string) $request->input('status', 'all');
        if (!in_array($status, ['all', 'accepted', 'pending', 'rejected'], true)) {
            $status = 'all';
        }

        $params = [$userId];
        $where = 'e.user_id = ?';
        if ($status !== 'all') {
            $where .= ' AND e.status = ?';
            $params[] = $status;
        }

        $evidence = Database::fetchAll(
            "SELECT e.*, s.name AS skill_name, s.slug AS skill_slug
             FROM skill_evidence e
             INNER JOIN skills s ON s.id = e.skill_id
             WHERE {$where}
             ORDER BY e.created_at DESC",
            $params
        );

        $skills = Database::fetchAll(
            'SELECT id, name FROM skills WHERE is_active = 1 ORDER BY name ASC'
        );

        $this->view('skills/evidence', [
            'title' => 'My Evidence — Skill Tree',
            'evidence' => $evidence,
            'statusFilter' => $status,
            'skills' => $skills,
        ]);
    }

    public function submitManual(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $skillId = (int) $request->input('skill_id', 0);
        $title = trim((string) $request->input('title', ''));
        $description = trim((string) $request->input('description', ''));

        if (Skill::findActive($skillId) === null) {
            $this->withError('Please select a valid skill.');
            $this->redirect('/skills/evidence');
            return;
        }

        try {
            SkillEvidenceService::recordManualEvidence((int) Auth::id(), $skillId, $title, $description);
            $this->withSuccess('Evidence submitted for review.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/skills/evidence');
    }
}
