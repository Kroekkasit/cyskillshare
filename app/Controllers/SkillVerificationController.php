<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\SkillEvidenceService;
use InvalidArgumentException;
use RuntimeException;

final class SkillVerificationController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!SkillEvidenceService::canVerify()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to verify skill evidence.',
            ]);
            return;
        }

        $pending = Database::fetchAll(
            "SELECT e.*, u.username, s.name AS skill_name, s.slug AS skill_slug
             FROM skill_evidence e
             INNER JOIN users u ON u.id = e.user_id
             INNER JOIN skills s ON s.id = e.skill_id
             WHERE e.status = 'pending'
             ORDER BY e.created_at ASC"
        );

        $this->view('skills/verification', [
            'title' => 'Verify Evidence — Skill Tree',
            'pending' => $pending,
        ]);
    }

    public function accept(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!SkillEvidenceService::canVerify()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to verify skill evidence.',
            ]);
            return;
        }

        $id = (int) $request->param('id');
        $note = trim((string) $request->input('note', ''));

        try {
            SkillEvidenceService::verify($id, (int) Auth::id(), $note !== '' ? $note : null);
            $this->withSuccess('Evidence accepted.');
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->withError($e->getMessage());
            } else {
                $this->withError($e->getMessage());
            }
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/skills/verification');
    }

    public function reject(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!SkillEvidenceService::canVerify()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to verify skill evidence.',
            ]);
            return;
        }

        $id = (int) $request->param('id');
        $reason = trim((string) $request->input('reason', ''));

        if ($reason === '') {
            $this->withError('A reason is required when rejecting evidence.');
            $this->redirect('/skills/verification');
            return;
        }

        try {
            SkillEvidenceService::reject($id, (int) Auth::id(), $reason);
            $this->withSuccess('Evidence rejected.');
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->withError($e->getMessage());
            } else {
                $this->withError($e->getMessage());
            }
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/skills/verification');
    }
}
