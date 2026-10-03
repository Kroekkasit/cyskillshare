<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Services\ContentVisibilityService;
use App\Services\WriteupService;
use InvalidArgumentException;
use RuntimeException;

final class WriteupAdminController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();

        if (!ContentVisibilityService::canManage()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to manage writeups.',
            ]);
            return;
        }

        $recent = Database::fetchAll(
            "SELECT w.id, w.title, w.slug, w.status, w.featured, w.published_at, w.updated_at,
                    u.username
             FROM writeups w
             INNER JOIN users u ON u.id = w.user_id
             WHERE w.deleted_at IS NULL
             ORDER BY w.updated_at DESC
             LIMIT 40"
        );

        $this->view('admin/writeups', [
            'title' => 'Writeup Admin — CySkillShare',
            'writeups' => $recent,
        ]);
    }

    public function feature(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        if (!ContentVisibilityService::canManage()) {
            http_response_code(403);
            $this->withError('Forbidden.');
            $this->redirect('/admin/writeups');
            return;
        }

        $id = (int) $request->param('id');
        $featured = !empty($request->input('featured'));

        try {
            WriteupService::setFeatured($id, (int) Auth::id(), $featured);
            $this->withSuccess($featured ? 'Writeup featured.' : 'Feature removed.');
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/writeups');
    }
}
