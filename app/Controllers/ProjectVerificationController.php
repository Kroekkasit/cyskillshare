<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\ProjectVerificationService;
use InvalidArgumentException;
use RuntimeException;

final class ProjectVerificationController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!ProjectVerificationService::canVerify()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to verify projects.',
            ]);
            return;
        }

        $this->view('portfolio/verification', [
            'title' => 'Verify Projects — CySkillShare',
            'isPortfolio' => true,
            'pending' => ProjectVerificationService::pendingList(),
        ]);
    }

    public function accept(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!ProjectVerificationService::canVerify()) {
            http_response_code(403);
            $this->withError('Forbidden.');
            $this->redirect('/admin/projects/verification');
            return;
        }

        $id = (int) $request->param('id');
        $note = trim((string) $request->input('note', ''));

        try {
            ProjectVerificationService::approve($id, (int) Auth::id(), $note !== '' ? $note : null);
            $this->withSuccess('Project verified.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? $e->getMessage() : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/projects/verification');
    }

    public function reject(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!ProjectVerificationService::canVerify()) {
            http_response_code(403);
            $this->withError('Forbidden.');
            $this->redirect('/admin/projects/verification');
            return;
        }

        $id = (int) $request->param('id');
        $reason = trim((string) $request->input('reason', ''));

        if ($reason === '') {
            $this->withError('A reason is required when rejecting.');
            $this->redirect('/admin/projects/verification');
            return;
        }

        try {
            ProjectVerificationService::reject($id, (int) Auth::id(), $reason);
            $this->withSuccess('Verification rejected.');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/projects/verification');
    }
}
