<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ChallengeFile;
use App\Models\ChallengeHint;
use App\Models\ChallengeSolve;
use App\Models\User;
use App\Services\ArenaProgressService;
use App\Services\ChallengeDiscussionService;
use App\Services\ChallengeFileService;
use App\Services\ChallengeService;
use App\Services\ChallengeSubmissionService;
use App\Services\HintService;
use App\Services\SkillService;
use InvalidArgumentException;
use RuntimeException;

final class ChallengeController extends Controller
{
    public function index(Request $request): void
    {
        $filters = [
            'search' => trim((string) $request->input('search', '')),
            'category' => (string) $request->input('category', ''),
            'difficulty' => (string) $request->input('difficulty', ''),
            'solve_status' => (string) $request->input('status', 'all'),
            'sort' => (string) $request->input('sort', 'newest'),
        ];

        $page = max(1, (int) $request->input('page', 1));
        $result = ChallengeService::listPublished($filters, $page, 12);

        $this->view('arena/challenges/index', [
            'title' => 'Challenges — Cyber Arena',
            'isArena' => true,
            'challenges' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['per_page'],
            'filters' => $filters,
            'categories' => ChallengeCategory::allActive(),
        ]);
    }

    public function show(Request $request): void
    {
        $id = (int) $request->param('id');
        $canManage = ChallengeService::canManageArena();
        $challenge = $canManage ? Challenge::find($id) : Challenge::findPublished($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist or is not available.',
            ]);
            return;
        }

        $category = ChallengeCategory::find($challenge->category_id);
        $author = User::find($challenge->author_id);
        $hintsRaw = ChallengeHint::forChallenge($challenge->id);
        $userId = Auth::id();
        $hintUsage = $userId !== null ? HintService::usageMap($userId, $challenge->id) : [];

        $hints = [];
        foreach ($hintsRaw as $hint) {
            $revealed = isset($hintUsage[$hint->id]);
            $hints[] = [
                'id' => $hint->id,
                'hint_order' => $hint->hint_order,
                'point_penalty' => $hint->point_penalty,
                'revealed' => $revealed,
                'content' => $revealed ? $hint->content : null,
            ];
        }

        $this->view('arena/challenges/show', [
            'title' => $challenge->title . ' — Cyber Arena',
            'isArena' => true,
            'challenge' => $challenge,
            'category' => $category,
            'author' => $author,
            'hints' => $hints,
            'hintUsage' => $hintUsage,
            'files' => ChallengeFile::forChallenge($challenge->id),
            'stats' => ChallengeService::stats($challenge->id),
            'solve' => $userId !== null ? ChallengeSolve::findForUser($challenge->id, $userId) : null,
            'threadId' => ChallengeDiscussionService::threadIdForChallenge($challenge->id),
            'tags' => $challenge->tags(),
            'practicedSkills' => SkillService::skillsForChallenge($challenge->id),
        ]);
    }

    public function submit(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $flag = (string) $request->input('flag', '');

        try {
            $result = ChallengeSubmissionService::submit((int) Auth::id(), $id, $flag);
            if ($result['correct']) {
                $this->withSuccess($result['message']);
            } else {
                $this->withError($result['message']);
            }
        } catch (RuntimeException $e) {
            if ($e->getCode() === 429) {
                http_response_code(429);
            }
            $this->withError($e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/challenges/' . $id);
    }

    public function revealHint(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $challengeId = (int) $request->param('id');
        $hintId = (int) $request->param('hintId');

        try {
            $result = HintService::reveal((int) Auth::id(), $challengeId, $hintId);
            if ($result['already']) {
                $this->withSuccess('Hint already revealed.');
            } else {
                $msg = 'Hint revealed.';
                if ($result['penalty'] > 0) {
                    $msg .= ' −' . $result['penalty'] . ' Arena points.';
                }
                $this->withSuccess($msg);
            }
        } catch (RuntimeException $e) {
            if ($e->getCode() === 429) {
                http_response_code(429);
            }
            $this->withError($e->getMessage());
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/arena/challenges/' . $challengeId);
    }

    public function download(Request $request): void
    {
        $challengeId = (int) $request->param('id');
        $fileId = (int) $request->param('fileId');

        $canManage = ChallengeService::canManageArena();
        $challenge = $canManage ? Challenge::find($challengeId) : Challenge::findPublished($challengeId);

        if ($challenge === null) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }

        $file = ChallengeFile::findForChallenge($fileId, $challengeId);
        if ($file === null) {
            http_response_code(404);
            echo 'Not found';
            exit;
        }

        ChallengeFileService::streamDownload($file, $challenge);
    }

    public function discuss(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $id = (int) $request->param('id');
        $challenge = Challenge::findPublished($id);

        if ($challenge === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Challenge Not Found',
                'message' => 'This challenge does not exist or is not available.',
            ]);
            return;
        }

        try {
            $thread = ChallengeDiscussionService::openOrCreate((int) Auth::id(), $challenge);
            $this->redirect('/thread/' . $thread->id);
        } catch (RuntimeException $e) {
            $this->withError($e->getMessage());
            $this->redirect('/arena/challenges/' . $id);
        }
    }

    public function category(Request $request): void
    {
        $slug = (string) $request->param('slug');

        try {
            $data = ArenaProgressService::categoryPage($slug, Auth::id());
        } catch (InvalidArgumentException $e) {
            if ($e->getCode() === 404) {
                http_response_code(404);
                $this->view('pages/errors/404', [
                    'title' => 'Category Not Found',
                    'message' => 'The requested category does not exist.',
                ]);
                return;
            }
            $this->withError($e->getMessage());
            $this->redirect('/arena/challenges');
            return;
        }

        $this->view('arena/categories/show', [
            'title' => $data['category']->name . ' — Cyber Arena',
            'isArena' => true,
            'category' => $data['category'],
            'total' => $data['total'],
            'solved' => $data['solved'],
            'difficulty' => $data['difficulty'],
            'challenges' => $data['challenges'],
        ]);
    }
}
