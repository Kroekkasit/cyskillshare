<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Channel;
use App\Models\Tag;
use App\Models\Thread;
use App\Services\SearchService;

final class SearchController extends Controller
{
    public function index(Request $request): void
    {
        $q = trim((string) $request->input('q', ''));
        $results = $q === ''
            ? ['threads' => [], 'users' => [], 'tags' => [], 'projects' => []]
            : SearchService::search($q);

        $this->view('pages/search/index', [
            'title' => 'Search — CySkillShare',
            'q' => $q,
            'results' => $results,
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }

    public function tag(Request $request): void
    {
        $slug = (string) $request->param('slug');
        $tag = Tag::findBySlug($slug);
        if ($tag === null) {
            http_response_code(404);
            $this->view('pages/errors/404', [
                'title' => 'Tag Not Found',
                'message' => 'That tag does not exist.',
            ]);
            return;
        }

        $sort = (string) $request->input('sort', 'latest');
        $page = max(1, (int) $request->input('page', 1));
        $result = Thread::paginate([
            'tag_id' => $tag->id,
            'sort' => $sort,
        ], $page, 15);

        $this->view('pages/community/tag', [
            'title' => '#' . $tag->slug . ' — CySkillShare',
            'tag' => $tag,
            'threadCount' => $tag->threadCount(),
            'threads' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'perPage' => $result['per_page'],
            'sort' => in_array($sort, ['latest', 'popular', 'unanswered', 'solved'], true) ? $sort : 'latest',
            'channelsGrouped' => Channel::groupedForSidebar(),
            'activeChannel' => null,
        ]);
    }
}
