<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Channel;
use App\Models\Tag;
use App\Models\Thread;
use App\Services\BookmarkService;
use App\Services\ThreadService;

final class CommunityController extends Controller
{
    public function index(Request $request): void
    {
        $sort = (string) $request->input('sort', 'latest');
        $page = max(1, (int) $request->input('page', 1));

        $result = Thread::paginate([
            'sort' => $sort,
            'current_user_id' => Auth::id(),
        ], $page, 15);

        $this->view('pages/community/index', [
            'title' => 'Community — CySkillShare',
            'channelsGrouped' => Channel::groupedForSidebar(),
            'threads' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['per_page'],
            'sort' => in_array($sort, ['latest', 'popular', 'unanswered', 'solved', 'mine'], true) ? $sort : 'latest',
            'activeChannel' => null,
        ]);
    }

    public function show(Request $request): void
    {
        $slug = (string) $request->param('channel');
        $channel = Channel::findBySlug($slug);

        if ($channel === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Channel Not Found',
                'message' => 'The requested channel does not exist.',
            ]);
            return;
        }

        $sort = (string) $request->input('sort', 'latest');
        $page = max(1, (int) $request->input('page', 1));

        $result = Thread::paginate([
            'channel_id' => $channel->id,
            'sort' => $sort,
            'current_user_id' => Auth::id(),
        ], $page, 15);

        $this->view('pages/community/channel', [
            'title' => $channel->name . ' — CySkillShare',
            'channel' => $channel,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'threads' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['per_page'],
            'sort' => in_array($sort, ['latest', 'popular', 'unanswered', 'solved', 'mine'], true) ? $sort : 'latest',
            'activeChannel' => $channel->slug,
            'threadCount' => $channel->threadCount(),
        ]);
    }

    public function createForm(Request $request): void
    {
        Auth::requireLogin();

        $preselect = (string) $request->input('channel', '');

        $this->view('pages/community/create', [
            'title' => 'New Discussion — CySkillShare',
            'channels' => Channel::allActive(),
            'channelsGrouped' => Channel::groupedForSidebar(),
            'preselect' => $preselect,
            'activeChannel' => $preselect !== '' ? $preselect : null,
            'knownTags' => Tag::all(),
        ]);
    }

    public function create(Request $request): void
    {
        Auth::requireLogin();

        $tagInput = trim((string) $request->input('tags', ''));
        $tagNames = $tagInput === ''
            ? []
            : array_values(array_filter(array_map('trim', preg_split('/[,]+/', $tagInput) ?: [])));

        $data = [
            'channel_id' => $request->input('channel_id'),
            'title' => trim((string) $request->input('title', '')),
            'content' => trim((string) $request->input('content', '')),
        ];

        $validator = Validator::make($data, [
            'channel_id' => 'required|integer',
            'title' => 'required|string|min_length:5|max_length:200',
            'content' => 'required|string|min_length:10|max_length:20000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors(), array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/community/new');
        }

        try {
            $thread = ThreadService::create(
                (int) Auth::id(),
                (int) $data['channel_id'],
                $data['title'],
                $data['content'],
                $tagNames
            );
        } catch (\RuntimeException $e) {
            http_response_code($e->getCode() === 429 ? 429 : 400);
            $this->withError($e->getMessage());
            Session::flash('_old_input', array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/community/new');
        } catch (\InvalidArgumentException $e) {
            $this->withError($e->getMessage());
            Session::flash('_old_input', array_merge($data, ['tags' => $tagInput]));
            $this->redirect('/community/new');
        }

        $this->withSuccess('Discussion created successfully.');
        $this->redirect('/thread/' . $thread->id);
    }

    public function bookmarks(Request $request): void
    {
        Auth::requireLogin();

        $page = max(1, (int) $request->input('page', 1));
        $items = BookmarkService::forUser((int) Auth::id(), $page, 15);

        $ids = array_map(static fn(array $row): int => (int) $row['id'], $items);
        $tags = Tag::forThreads($ids);
        foreach ($items as &$item) {
            $item['tags'] = $tags[(int) $item['id']] ?? [];
        }
        unset($item);

        $this->view('pages/community/bookmarks', [
            'title' => 'Bookmarks — CySkillShare',
            'threads' => $items,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
            'page' => $page,
        ]);
    }
}
