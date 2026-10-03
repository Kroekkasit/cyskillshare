<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ChallengeFile;
use App\Models\ChallengeHint;
use App\Models\ChallengeSolve;
use App\Services\ArenaEventService;
use App\Services\ChallengeFileService;
use App\Services\ChallengeService;
use InvalidArgumentException;
use RuntimeException;

final class ArenaAdminController extends Controller
{
    public function challenges(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('arena/admin/challenges', [
            'title' => 'Manage Challenges — Cyber Arena',
            'isArena' => true,
            'challenges' => ChallengeService::adminList(),
        ]);
    }

    public function createForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('arena/admin/challenge_form', [
            'title' => 'New Challenge — Cyber Arena',
            'isArena' => true,
            'challenge' => null,
            'categories' => ChallengeCategory::all(),
            'hints' => [],
            'files' => [],
            'solveCount' => 0,
            'tags' => [],
        ]);
    }

    public function create(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $tagInput = trim((string) $request->input('tags', ''));
        $data = $this->extractChallengeData($request, true);

        $validator = Validator::make($data, [
            'title' => 'required|string|min_length:3|max_length:200',
            'description' => 'required|string|min_length:10|max_length:50000',
            'category_id' => 'required|integer',
            'difficulty' => 'required|string',
            'points' => 'required|integer',
            'flag' => 'required|string|min_length:1|max_length:500',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/arena/admin/challenges/new');
        }

        $data['tags'] = $this->parseTags($tagInput);
        $data['is_featured'] = $request->input('featured');
        $data['case_sensitive'] = $request->input('case_sensitive');
        $data['status'] = (string) $request->input('status', 'draft');

        try {
            $challenge = ChallengeService::create((int) Auth::id(), $data);
            $this->withSuccess('Challenge created.');
            $this->redirect('/arena/admin/challenges/' . $challenge->id . '/edit');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/arena/admin/challenges/new');
        }
    }

    public function editForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        $tagList = $challenge->tags();
        $tagNames = implode(', ', array_map(static fn(array $t): string => $t['name'], $tagList));

        $this->view('arena/admin/challenge_form', [
            'title' => 'Edit Challenge — Cyber Arena',
            'isArena' => true,
            'challenge' => $challenge,
            'categories' => ChallengeCategory::all(),
            'hints' => ChallengeHint::forChallenge($challenge->id),
            'files' => ChallengeFile::forChallenge($challenge->id),
            'solveCount' => ChallengeSolve::countForChallenge($challenge->id),
            'tags' => $tagList,
            'tagString' => $tagNames,
        ]);
    }

    public function update(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        $tagInput = trim((string) $request->input('tags', ''));
        $data = $this->extractChallengeData($request, false);
        $confirmScoring = !empty($request->input('confirm_scoring'));

        $validator = Validator::make($data, [
            'title' => 'required|string|min_length:3|max_length:200',
            'description' => 'required|string|min_length:10|max_length:50000',
            'category_id' => 'required|integer',
            'difficulty' => 'required|string',
            'points' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/arena/admin/challenges/' . $id . '/edit');
        }

        $data['tags'] = $this->parseTags($tagInput);
        $data['is_featured'] = $request->input('featured');
        $data['case_sensitive'] = $request->input('case_sensitive');
        $data['status'] = (string) $request->input('status', $challenge->status);
        $flag = trim((string) $request->input('flag', ''));
        if ($flag !== '') {
            $data['flag'] = $flag;
        }

        try {
            ChallengeService::update($challenge, (int) Auth::id(), $data, $confirmScoring);
            $this->withSuccess('Challenge updated.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', array_merge($data, ['tags' => $tagInput]));
        }

        $this->redirect('/arena/admin/challenges/' . $id . '/edit');
    }

    public function publish(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        try {
            ChallengeService::setStatus($challenge, (int) Auth::id(), 'published');
            $this->withSuccess('Challenge published.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/admin/challenges/' . $id . '/edit');
    }

    public function archive(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        try {
            ChallengeService::setStatus($challenge, (int) Auth::id(), 'archived');
            $this->withSuccess('Challenge archived.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/admin/challenges');
    }

    public function addHint(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        $content = trim((string) $request->input('content', ''));
        $penalty = max(0, (int) $request->input('point_penalty', 0));
        $order = max(1, (int) $request->input('hint_order', 1));

        if ($content === '') {
            $this->withError('Hint content is required.');
            $this->redirect('/arena/admin/challenges/' . $id . '/edit');
        }

        ChallengeHint::create($id, $order, $content, $penalty);
        $this->withSuccess('Hint added.');
        $this->redirect('/arena/admin/challenges/' . $id . '/edit');
    }

    public function editHint(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $hintId = (int) $request->param('id');
        $hint = ChallengeHint::find($hintId);

        if ($hint === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Hint Not Found',
                'message' => 'This hint does not exist.',
            ]);
            return;
        }

        $content = trim((string) $request->input('content', ''));
        $penalty = max(0, (int) $request->input('point_penalty', 0));
        $order = max(1, (int) $request->input('hint_order', $hint->hint_order));

        if ($content === '') {
            $this->withError('Hint content is required.');
            $this->redirect('/arena/admin/challenges/' . $hint->challenge_id . '/edit');
        }

        ChallengeHint::updateHint($hintId, $content, $penalty, $order);
        $this->withSuccess('Hint updated.');
        $this->redirect('/arena/admin/challenges/' . $hint->challenge_id . '/edit');
    }

    public function deleteHint(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $hintId = (int) $request->param('id');
        $hint = ChallengeHint::find($hintId);

        if ($hint === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Hint Not Found',
                'message' => 'This hint does not exist.',
            ]);
            return;
        }

        $challengeId = $hint->challenge_id;
        ChallengeHint::delete($hintId);
        $this->withSuccess('Hint deleted.');
        $this->redirect('/arena/admin/challenges/' . $challengeId . '/edit');
    }

    public function uploadFile(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::find($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist.',
            ]);
            return;
        }

        $file = $_FILES['file'] ?? null;
        if (!is_array($file)) {
            $this->withError('No file uploaded.');
            $this->redirect('/arena/admin/challenges/' . $id . '/edit');
        }

        try {
            ChallengeFileService::upload($id, $file, (int) Auth::id());
            $this->withSuccess('File uploaded.');
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/admin/challenges/' . $id . '/edit');
    }

    public function deleteFile(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $fileId = (int) $request->param('id');
        $file = ChallengeFile::find($fileId);

        if ($file === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'File Not Found',
                'message' => 'This file does not exist.',
            ]);
            return;
        }

        $challengeId = $file->challenge_id;

        try {
            ChallengeFileService::delete($file, (int) Auth::id());
            $this->withSuccess('File deleted.');
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/admin/challenges/' . $challengeId . '/edit');
    }

    public function events(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('arena/admin/events', [
            'title' => 'Manage Events — Cyber Arena',
            'isArena' => true,
            'events' => ArenaEventService::adminList(),
        ]);
    }

    public function createEventForm(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }

        $this->view('arena/admin/event_form', [
            'title' => 'New Event — Cyber Arena',
            'isArena' => true,
            'event' => null,
            'challenges' => ChallengeService::adminList(),
            'selectedIds' => [],
        ]);
    }

    public function createEvent(Request $request): void
    {
        if (!$this->guardManage()) {
            return;
        }
        $this->requireCsrf();

        $data = [
            'name' => trim((string) $request->input('name', '')),
            'description' => trim((string) $request->input('description', '')),
            'event_type' => (string) $request->input('event_type', 'practice'),
            'status' => (string) $request->input('status', 'draft'),
            'visibility' => (string) $request->input('visibility', 'public'),
            'start_at' => $this->normalizeDatetime((string) $request->input('start_at', '')),
            'end_at' => $this->normalizeDatetime((string) $request->input('end_at', '')),
        ];

        $validator = Validator::make($data, [
            'name' => 'required|string|min_length:3|max_length:200',
            'description' => 'required|string|min_length:10|max_length:10000',
            'start_at' => 'required|string',
            'end_at' => 'required|string',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), $data);
            $this->redirect('/arena/admin/events/new');
        }

        $challengeIds = $request->input('challenge_ids', []);
        if (!is_array($challengeIds)) {
            $challengeIds = [];
        }
        $challengeIds = array_map('intval', $challengeIds);

        try {
            $event = ArenaEventService::create((int) Auth::id(), $data, $challengeIds);
            $this->withSuccess('Event created.');
            $this->redirect('/arena/admin/events');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', $data);
            $this->redirect('/arena/admin/events/new');
        }
    }

    private function guardManage(): bool
    {
        if (!Auth::check()) {
            Auth::requireLogin();
        }

        if (!ChallengeService::canManageArena()) {
            http_response_code(403);
            $this->view('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to manage the Cyber Arena.',
            ]);
            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractChallengeData(Request $request, bool $isCreate): array
    {
        return [
            'title' => trim((string) $request->input('title', '')),
            'description' => trim((string) $request->input('description', '')),
            'category_id' => $request->input('category_id'),
            'difficulty' => (string) $request->input('difficulty', 'easy'),
            'points' => $request->input('points', 100),
            'flag' => $isCreate ? trim((string) $request->input('flag', '')) : trim((string) $request->input('flag', '')),
        ];
    }

    /**
     * @return list<string>
     */
    private function parseTags(string $tagInput): array
    {
        if ($tagInput === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', preg_split('/[,]+/', $tagInput) ?: [])));
    }

    private function normalizeDatetime(string $input): string
    {
        if ($input === '') {
            return $input;
        }
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $input)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $input)
            ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $input);
        return $dt !== false ? $dt->format('Y-m-d H:i:s') : $input;
    }
}
