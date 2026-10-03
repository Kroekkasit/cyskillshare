<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

final class ProfileController extends Controller
{
    public function show(Request $request): void
    {
        $username = (string) $request->param('username');
        $profile = User::findByUsername($username);

        if ($profile === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'User Not Found',
                'message' => 'The requested profile does not exist.',
            ]);
            return;
        }

        $this->view('pages/profile/show', [
            'title' => '@' . $profile->username . ' — CySkillShare',
            'profile' => $profile,
            'roles' => $profile->roleNames(),
            'isOwner' => Auth::id() === $profile->id,
        ]);
    }

    /**
     * Foundation-only admin gate demo — used to verify RBAC.
     */
    public function adminDemo(Request $request): void
    {
        Auth::requireRole('admin');

        $this->view('pages/profile/admin-demo', [
            'title' => 'Admin Area — CySkillShare',
            'user' => Auth::user(),
        ]);
    }
}
