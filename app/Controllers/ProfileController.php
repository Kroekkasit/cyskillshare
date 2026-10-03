<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;

final class ProfileController extends Controller
{
    public function show(Request $request): void
    {
        $username = (string) ($request->param('username') ?? '');
        $profile = User::findByUsername($username);

        if ($profile === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'User Not Found',
                'message' => 'The requested profile does not exist.',
            ]);
            return;
        }

        $discussions = Thread::paginate([
            'user_id' => $profile->id,
            'sort' => 'latest',
        ], 1, 10);

        $this->view('pages/profile/show', [
            'title' => '@' . $profile->username . ' — CySkillShare',
            'profile' => $profile,
            'roles' => $profile->roleNames(),
            'isOwner' => Auth::id() === $profile->id,
            'discussionCount' => $profile->discussionCount(),
            'replyCount' => $profile->replyCount(),
            'bestAnswerCount' => $profile->bestAnswerCount(),
            'recentDiscussions' => $discussions['items'],
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }

    public function update(Request $request): void
    {
        Auth::requireLogin();
        $username = (string) $request->param('username');
        $profile = User::findByUsername($username);

        if ($profile === null || Auth::id() !== $profile->id) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You can only edit your own profile.',
            ]);
            return;
        }

        $data = [
            'full_name' => trim((string) $request->input('full_name', '')),
            'bio' => trim((string) $request->input('bio', '')),
        ];

        $validator = Validator::make($data, [
            'full_name' => 'string|max_length:150',
            'bio' => 'string|max_length:2000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), $data);
            $this->redirect('/profile/' . $profile->username);
        }

        $profile->updateProfile(
            $data['bio'] !== '' ? $data['bio'] : null,
            $data['full_name'] !== '' ? $data['full_name'] : null
        );

        $this->withSuccess('Profile updated.');
        $this->redirect('/profile/' . $profile->username);
    }

    public function adminDemo(Request $request): void
    {
        Auth::requireRole('admin');

        $this->view('pages/profile/admin-demo', [
            'title' => 'Admin Area — CySkillShare',
            'user' => Auth::user(),
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }
}
