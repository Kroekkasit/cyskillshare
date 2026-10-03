<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use App\Core\RateLimiter;
use App\Models\Report;
use RuntimeException;

final class ModerationService
{
    public static function report(
        int $reporterId,
        string $targetType,
        int $targetId,
        string $reason,
        ?string $description = null
    ): void {
        $allowedTargets = ['thread', 'reply', 'user'];
        $allowedReasons = [
            'spam',
            'harassment',
            'malicious_content',
            'incorrect_dangerous',
            'academic_misconduct',
            'other',
        ];

        if (!in_array($targetType, $allowedTargets, true)) {
            throw new RuntimeException('Invalid report target.', 400);
        }
        if (!in_array($reason, $allowedReasons, true)) {
            throw new RuntimeException('Invalid report reason.', 400);
        }

        if (!RateLimiter::attempt($reporterId, 'report_created', 10, 3600)) {
            throw new RuntimeException('Too many reports. Please try again later.', 429);
        }

        if (Report::existsPending($reporterId, $targetType, $targetId)) {
            throw new RuntimeException('You already reported this content.', 400);
        }

        $id = Report::create([
            'reporter_id' => $reporterId,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'reason' => $reason,
            'description' => $description,
        ]);

        ActivityLogService::log($reporterId, 'report_created', $targetType, $targetId, [
            'report_id' => $id,
            'reason' => $reason,
        ]);
    }

    public static function resolve(int $reportId, int $moderatorId, string $status): void
    {
        if (!Auth::hasAnyRole(['moderator', 'admin'])) {
            throw new RuntimeException('Forbidden', 403);
        }

        if (!in_array($status, ['reviewed', 'resolved', 'dismissed'], true)) {
            throw new RuntimeException('Invalid status.', 400);
        }

        $report = Report::find($reportId);
        if ($report === null) {
            throw new RuntimeException('Report not found.', 404);
        }

        Report::updateStatus($reportId, $status, $moderatorId);
        ActivityLogService::log($moderatorId, 'report_reviewed', 'report', $reportId, [
            'status' => $status,
        ]);
    }
}
