<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Services\ModerationService;

final class ReportController extends Controller
{
    public function store(Request $request): void
    {
        Auth::requireLogin();

        $data = [
            'target_type' => (string) $request->input('target_type', ''),
            'target_id' => $request->input('target_id'),
            'reason' => (string) $request->input('reason', ''),
            'description' => trim((string) $request->input('description', '')),
        ];

        $validator = Validator::make($data, [
            'target_type' => 'required|string|enum:thread,reply,user',
            'target_id' => 'required|integer',
            'reason' => 'required|string|enum:spam,harassment,malicious_content,incorrect_dangerous,academic_misconduct,other',
            'description' => 'string|max_length:2000',
        ]);

        if ($validator->fails()) {
            $this->withErrors($validator->errors());
            $redirect = (string) $request->input('redirect', '/community');
            $this->redirect(str_starts_with($redirect, '/') ? $redirect : '/community');
        }

        try {
            ModerationService::report(
                (int) Auth::id(),
                $data['target_type'],
                (int) $data['target_id'],
                $data['reason'],
                $data['description'] !== '' ? $data['description'] : null
            );
            $this->withSuccess('Report submitted.');
        } catch (\RuntimeException $e) {
            http_response_code($e->getCode() >= 400 ? (int) $e->getCode() : 400);
            $this->withError($e->getMessage());
        }

        $redirect = (string) $request->input('redirect', '/community');
        $this->redirect(str_starts_with($redirect, '/') ? $redirect : '/community');
    }
}
