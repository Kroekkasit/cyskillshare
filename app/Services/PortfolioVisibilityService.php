<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;

final class PortfolioVisibilityService
{
    /**
     * Can $viewerId view content with $visibility owned by $ownerId?
     */
    public static function canView(string $visibility, int $ownerId, ?int $viewerId): bool
    {
        if ($viewerId !== null && $viewerId === $ownerId) {
            return true;
        }

        return match ($visibility) {
            'public' => true,
            'community' => $viewerId !== null,
            'private' => false,
            default => false,
        };
    }

    public static function canViewPortfolio(array $portfolio, int $ownerId, ?int $viewerId = null): bool
    {
        $viewerId = $viewerId ?? Auth::id();
        if (!(bool) ($portfolio['is_enabled'] ?? false) && $viewerId !== $ownerId) {
            return false;
        }
        return self::canView((string) ($portfolio['visibility'] ?? 'private'), $ownerId, $viewerId);
    }

    public static function canViewProject(array $project, ?int $viewerId = null): bool
    {
        $viewerId = $viewerId ?? Auth::id();
        $ownerId = (int) $project['user_id'];
        if ($viewerId === $ownerId) {
            return true;
        }
        if (($project['publish_status'] ?? '') !== 'published') {
            return false;
        }
        return self::canView((string) ($project['visibility'] ?? 'private'), $ownerId, $viewerId);
    }

    public static function isSafeUrl(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return true;
        }
        $url = trim($url);
        if (mb_strlen($url) > 255) {
            return false;
        }
        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }
        $scheme = strtolower((string) $parts['scheme']);
        $allowed = config('portfolio.allowed_url_schemes', ['http', 'https']);
        return is_array($allowed) && in_array($scheme, $allowed, true);
    }
}
