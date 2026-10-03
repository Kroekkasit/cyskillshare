<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\ProjectService;
use App\Services\ProjectVerificationService;

final class PortfolioAdminController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!Auth::hasAnyRole(['admin', 'instructor', 'mentor'])) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access portfolio admin.',
            ]);
            return;
        }

        $recentProjects = Database::fetchAll(
            "SELECT p.*, u.username
             FROM projects p
             INNER JOIN users u ON u.id = p.user_id
             ORDER BY p.updated_at DESC
             LIMIT 30"
        );

        foreach ($recentProjects as &$p) {
            $p['verified'] = ProjectService::isVerified((int) $p['id']);
        }
        unset($p);

        $pendingCount = count(ProjectVerificationService::pendingList());

        $this->view('portfolio/admin/index', [
            'title' => 'Portfolio Admin — CySkillShare',
            'isPortfolio' => true,
            'recentProjects' => $recentProjects,
            'pendingCount' => $pendingCount,
            'canVerify' => ProjectVerificationService::canVerify(),
        ]);
    }

    public function featureOverride(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!Auth::hasRole('admin')) {
            http_response_code(403);
            $this->withError('Admin only.');
            $this->redirect('/admin/portfolio');
            return;
        }

        $id = (int) $request->param('id');
        $featured = !empty($request->input('featured'));

        Database::execute(
            'UPDATE projects SET featured = ? WHERE id = ?',
            [$featured ? 1 : 0, $id]
        );

        $this->withSuccess($featured ? 'Project featured (admin).' : 'Feature removed (admin).');
        $this->redirect('/admin/portfolio');
    }
}
