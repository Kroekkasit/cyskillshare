<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\User;
use App\Services\CollaborationMatchService;
use App\Services\PeopleDiscoveryService;
use App\Services\UserBlockService;
use RuntimeException;

final class PeopleController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'q' => trim((string) $request->input('q', '')),
            'skill' => trim((string) $request->input('skill', '')),
            'min_level' => $request->input('min_level'),
            'looking_for' => trim((string) $request->input('looking_for', '')),
        ];
        $page = max(1, (int) $request->input('page', 1));

        try {
            $result = PeopleDiscoveryService::search($filters, $page, 12, Auth::id());
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $result = ['items' => [], 'total' => 0, 'page' => 1];
        }

        $skills = Database::fetchAll(
            'SELECT id, name, slug FROM skills WHERE is_active = 1 ORDER BY name ASC LIMIT 100'
        );

        $showInDiscovery = true;
        if (Auth::check()) {
            $row = Database::fetch(
                'SELECT show_in_discovery FROM users WHERE id = ? LIMIT 1',
                [(int) Auth::id()]
            );
            $showInDiscovery = (int) ($row['show_in_discovery'] ?? 1) === 1;
        }

        $this->view('people/index', [
            'title' => 'Discover People — CySkillShare',
            'items' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'filters' => $filters,
            'skills' => $skills,
            'showInDiscovery' => $showInDiscovery,
        ]);
    }

    public function collaboration(Request $request): void
    {
        $recommended = CollaborationMatchService::recommended(Auth::id());

        $this->view('collaboration/index', [
            'title' => 'Collaboration — CySkillShare',
            'recommended' => $recommended,
        ]);
    }

    public function privacy(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $enabled = !empty($request->input('show_in_discovery'));

        PeopleDiscoveryService::setDiscovery((int) Auth::id(), $enabled);
        $this->withSuccess($enabled ? 'You are visible in people discovery.' : 'You are hidden from people discovery.');

        $referer = (string) ($request->input('redirect', '/people'));
        $this->redirect($referer !== '' ? $referer : '/people');
    }

    public function block(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $username = (string) $request->param('username');
        $target = User::findByUsername($username);

        if ($target === null) {
            $this->withError('User not found.');
            $this->redirect('/people');
            return;
        }

        try {
            UserBlockService::block((int) Auth::id(), $target->id);
            $this->withSuccess('User blocked.');
        } catch (\InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $referer = (string) ($request->input('redirect', '/people'));
        $this->redirect($referer !== '' ? $referer : '/people');
    }

    public function unblock(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $username = (string) $request->param('username');
        $target = User::findByUsername($username);

        if ($target === null) {
            $this->withError('User not found.');
            $this->redirect('/people');
            return;
        }

        UserBlockService::unblock((int) Auth::id(), $target->id);
        $this->withSuccess('User unblocked.');

        $referer = (string) ($request->input('redirect', '/people'));
        $this->redirect($referer !== '' ? $referer : '/people');
    }
}
