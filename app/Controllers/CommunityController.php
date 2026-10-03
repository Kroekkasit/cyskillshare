<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Channel;

final class CommunityController extends Controller
{
    public function index(Request $request): void
    {
        $channels = Channel::withCategories();

        $this->view('pages/community/index', [
            'title' => 'Community — CySkillShare',
            'channels' => $channels,
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

        $threads = \App\Models\Thread::byChannel($channel->id);

        $this->view('pages/community/show', [
            'title' => $channel->name . ' — CySkillShare',
            'channel' => $channel,
            'threads' => $threads,
        ]);
    }
}
