<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\ProjectMediaService;
use InvalidArgumentException;

final class ProjectMediaController extends Controller
{
    public function upload(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $projectId = (int) $request->param('id');
        $file = $_FILES['image'] ?? null;
        $caption = trim((string) $request->input('caption', ''));

        if (!is_array($file)) {
            $this->withError('No image uploaded.');
            $this->redirect('/projects/edit/' . $projectId);
            return;
        }

        try {
            ProjectMediaService::upload(
                $projectId,
                (int) Auth::id(),
                $file,
                $caption !== '' ? $caption : null
            );
            $this->withSuccess('Image uploaded.');
        } catch (InvalidArgumentException $e) {
            $this->withError($e->getMessage());
        }

        $this->redirect('/projects/edit/' . $projectId);
    }

    public function show(Request $request): void
    {
        $id = (int) $request->param('id');
        ProjectMediaService::stream($id, Auth::id());
    }

    public function delete(Request $request): void
    {
        Auth::requireLogin();
        $this->requireCsrf();

        $id = (int) $request->param('id');

        try {
            ProjectMediaService::delete($id, (int) Auth::id());
            $this->withSuccess('Image deleted.');
        } catch (\Throwable) {
            $this->withError('Unable to delete image.');
        }

        $referer = (string) ($request->input('redirect', '/dashboard/portfolio'));
        $this->redirect($referer !== '' ? $referer : '/dashboard/portfolio');
    }
}
