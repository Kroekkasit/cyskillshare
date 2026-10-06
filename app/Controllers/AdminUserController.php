<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Role;
use App\Services\AdminDashboardService;
use App\Services\AdminUserService;
use InvalidArgumentException;
use RuntimeException;

final class AdminUserController extends Controller
{
    public function index(Request $request): void
    {
        AdminDashboardService::requireAdmin();

        $q = trim((string) $request->input('q', ''));
        $status = trim((string) $request->input('status', ''));
        $role = trim((string) $request->input('role', ''));
        $page = max(1, (int) $request->input('page', 1));

        $result = AdminUserService::paginate(
            $page,
            30,
            $q !== '' ? $q : null,
            $status !== '' ? $status : null,
            $role !== '' ? $role : null
        );

        $this->view('admin/users/index', [
            'title' => 'Users — CySkillShare Admin',
            'result' => $result,
            'roles' => Role::all(),
            'filters' => [
                'q' => $q,
                'status' => $status,
                'role' => $role,
            ],
        ]);
    }

    public function show(Request $request): void
    {
        AdminDashboardService::requireAdmin();

        $id = (int) $request->param('id');
        $user = AdminUserService::detail($id);
        if ($user === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Not Found',
                'message' => 'User not found.',
            ]);
            return;
        }

        $this->view('admin/users/show', [
            'title' => '@' . $user['username'] . ' — Admin',
            'user' => $user,
            'allRoles' => Role::all(),
            'actorId' => (int) Auth::id(),
        ]);
    }

    public function updateStatus(Request $request): void
    {
        AdminDashboardService::requireAdmin();
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $status = trim((string) $request->input('status', ''));

        try {
            AdminUserService::setStatus($id, $status, (int) Auth::id());
            $this->withSuccess('User status updated to ' . $status . '.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/users/' . $id);
    }

    public function assignRole(Request $request): void
    {
        AdminDashboardService::requireAdmin();
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $role = trim((string) $request->input('role', ''));

        try {
            AdminUserService::assignRole($id, $role, (int) Auth::id());
            $this->withSuccess('Role assigned: ' . $role);
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/users/' . $id);
    }

    public function removeRole(Request $request): void
    {
        AdminDashboardService::requireAdmin();
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $role = trim((string) $request->input('role', ''));

        try {
            AdminUserService::removeRole($id, $role, (int) Auth::id());
            $this->withSuccess('Role removed: ' . $role);
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/admin/users/' . $id);
    }
}
