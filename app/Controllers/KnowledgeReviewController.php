<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\ContentVisibilityService;
use App\Services\KnowledgeArticleService;
use InvalidArgumentException;
use RuntimeException;

final class KnowledgeReviewController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!ContentVisibilityService::canReview()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to review knowledge articles.',
            ]);
            return;
        }

        $pending = KnowledgeArticleService::pendingReviews();

        $this->view('admin/knowledge_review', [
            'title' => 'Knowledge Review — CySkillShare',
            'pending' => $pending,
        ]);
    }

    public function review(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!ContentVisibilityService::canReview()) {
            http_response_code(403);
            $this->withError('Forbidden.');
            $this->redirect('/admin/knowledge/review');
            return;
        }

        $id = (int) $request->param('id');
        $status = (string) $request->input('status', '');
        $note = (string) $request->input('review_note', '');

        try {
            KnowledgeArticleService::review($id, (int) Auth::id(), $status, $note);
            $this->withSuccess('Review submitted.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/knowledge/review');
    }
}
