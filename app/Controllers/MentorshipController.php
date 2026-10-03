<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\MentorshipService;
use InvalidArgumentException;
use RuntimeException;

final class MentorshipController extends Controller
{
    public function index(Request $request): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::id();
        $items = MentorshipService::forUser($userId);

        $this->view('mentorship/index', [
            'title' => 'My Mentorships — CySkillShare',
            'items' => $items,
        ]);
    }

    public function request(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $userId = (int) Auth::id();
        $mentorId = (int) $request->input('mentor_id', 0);

        $data = [
            'learning_goal' => (string) $request->input('learning_goal', ''),
            'message' => (string) $request->input('message', ''),
            'preferred_frequency' => (string) $request->input('preferred_frequency', ''),
            'preferred_session_length' => (string) $request->input('preferred_session_length', ''),
        ];

        $skillIds = [];
        foreach ((array) $request->input('skill_ids', []) as $sid) {
            $sid = (int) $sid;
            if ($sid > 0) {
                $skillIds[] = $sid;
            }
        }

        try {
            $row = MentorshipService::request($userId, $mentorId, $data, $skillIds);
            $this->withSuccess('Mentorship request sent.');
            $this->redirect('/mentorship/' . (int) $row['id']);
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            $referer = (string) ($request->input('redirect', '/mentors'));
            $this->redirect($referer !== '' ? $referer : '/mentors');
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $referer = (string) ($request->input('redirect', '/mentors'));
            $this->redirect($referer !== '' ? $referer : '/mentors');
        }
    }

    public function respond(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');
        $decision = (string) $request->input('decision', '');

        try {
            MentorshipService::respond($id, (int) Auth::id(), $decision);
            $msg = match ($decision) {
                'accepted' => 'Mentorship accepted.',
                'declined' => 'Mentorship declined.',
                'cancelled' => 'Request cancelled.',
                default => 'Updated.',
            };
            $this->withSuccess($msg);
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/mentorship/' . $id);
    }

    public function show(Request $request): void
    {
        Auth::requireLogin();
        $id = (int) $request->param('id');
        $userId = (int) Auth::id();

        try {
            $detail = MentorshipService::dashboard($id, $userId);
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Mentorship Not Found',
                    'message' => 'This mentorship does not exist.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/mentorship');
            return;
        } catch (RuntimeException $e) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You are not a participant in this mentorship.',
            ]);
            return;
        }

        $mentorUserId = (int) ($detail['mentor_user']['id'] ?? 0);
        $isMentor = $userId === $mentorUserId;

        $this->view('mentorship/show', [
            'title' => 'Mentorship Dashboard — CySkillShare',
            ...$detail,
            'isMentor' => $isMentor,
        ]);
    }

    public function addGoal(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');

        try {
            MentorshipService::addGoal($id, (int) Auth::id(), [
                'title' => (string) $request->input('title', ''),
                'description' => (string) $request->input('description', ''),
                'skill_id' => $request->input('skill_id'),
                'target_level' => $request->input('target_level'),
                'progress' => $request->input('progress', 0),
            ]);
            $this->withSuccess('Goal added.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        }

        $this->redirect('/mentorship/' . $id);
    }

    public function addSession(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();
        $id = (int) $request->param('id');

        try {
            MentorshipService::scheduleSession($id, (int) Auth::id(), [
                'title' => (string) $request->input('title', ''),
                'scheduled_at' => (string) $request->input('scheduled_at', ''),
                'duration_minutes' => (int) $request->input('duration_minutes', 45),
                'notes' => (string) $request->input('notes', ''),
                'meeting_link' => (string) $request->input('meeting_link', ''),
            ]);
            $this->withSuccess('Session scheduled.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        } catch (RuntimeException $e) {
            $this->withError($e->getCode() === 403 ? 'Forbidden.' : $e->getMessage());
        }

        $this->redirect('/mentorship/' . $id);
    }
}
