<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;

final class ContentVisibilityService
{
    public static function canView(string $visibility, int $ownerId, ?int $viewerId, string $status): bool
    {
        if ($viewerId !== null && $viewerId === $ownerId) {
            return true;
        }
        if (!in_array($status, ['published'], true)) {
            // Staff can preview published-only; under_review visible to reviewers
            if ($status === 'under_review' && self::canReview()) {
                return true;
            }
            return false;
        }
        return match ($visibility) {
            'public' => true,
            'community' => $viewerId !== null,
            'private' => false,
            default => false,
        };
    }

    public static function canReview(): bool
    {
        $roles = config('writeups.reviewer_roles', ['instructor', 'mentor', 'admin']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }

    public static function canManage(): bool
    {
        $roles = config('writeups.manager_roles', ['instructor', 'admin', 'moderator']);
        return is_array($roles) && Auth::hasAnyRole(array_map('strval', $roles));
    }
}
