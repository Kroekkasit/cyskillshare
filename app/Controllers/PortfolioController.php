<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\PortfolioAnalyticsService;
use App\Services\PortfolioResumeService;
use App\Services\PortfolioService;
use App\Services\ProjectService;
use InvalidArgumentException;
use RuntimeException;

final class PortfolioController extends Controller
{
    public function show(Request $request): void
    {
        $username = (string) $request->param('username');

        try {
            $data = PortfolioService::publicView($username, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Portfolio Not Found',
                    'message' => 'This portfolio does not exist or is not available.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/');
            return;
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->view('pages/errors/403', [
                    'title' => 'Portfolio Unavailable',
                    'message' => $e->getMessage(),
                ]);
                return;
            }
            throw $e;
        }

        $user = $data['user'];

        $this->view('portfolio/show', [
            'title' => $user->username . ' — Portfolio',
            'isPortfolio' => true,
            ...$data,
        ]);
    }

    public function dashboard(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $user = Auth::user();

        PortfolioService::ensure($userId);
        $completeness = PortfolioService::completeness($userId);
        $analytics = PortfolioAnalyticsService::summary($userId);

        $projects = Database::fetchAll(
            'SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC LIMIT 20',
            [$userId]
        );
        foreach ($projects as &$p) {
            $p['technologies'] = ProjectService::technologies((int) $p['id']);
            $p['verified'] = ProjectService::isVerified((int) $p['id']);
        }
        unset($p);

        $recommendations = $completeness['missing'];

        $this->view('portfolio/dashboard', [
            'title' => 'Portfolio Dashboard — CySkillShare',
            'isPortfolio' => true,
            'user' => $user,
            'completeness' => $completeness,
            'analytics' => $analytics,
            'projects' => $projects,
            'recommendations' => $recommendations,
        ]);
    }

    public function resume(Request $request): void
    {
        $username = (string) $request->param('username');

        try {
            $resume = PortfolioResumeService::build($username, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Resume Not Found',
                    'message' => 'This resume is not available.',
                ], 'layouts/app');
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/');
            return;
        } catch (RuntimeException $e) {
            if ($e->getCode() === 403) {
                http_response_code(403);
                $this->view('pages/errors/403', [
                    'title' => 'Resume Unavailable',
                    'message' => $e->getMessage(),
                ], 'layouts/app');
                return;
            }
            throw $e;
        }

        $this->view('portfolio/resume', [
            'title' => $username . ' — Resume',
            'resume' => $resume,
        ], 'layouts/print');
    }
}
