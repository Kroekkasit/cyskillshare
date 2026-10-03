<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Auth;
use InvalidArgumentException;
use RuntimeException;

/**
 * Printable HTML resume (browser Print → PDF).
 * No third-party PDF library — secure server-rendered HTML only.
 */
final class PortfolioResumeService
{
    /**
     * @param list<string> $includeSections
     * @return array<string, mixed>
     */
    public static function build(string $username, ?int $viewerId, array $includeSections = []): array
    {
        $data = PortfolioService::publicView($username, $viewerId);
        $allowed = [
            'about', 'education', 'experience', 'skills', 'projects',
            'certifications', 'challenges', 'community', 'links',
        ];
        if ($includeSections === []) {
            $includeSections = $allowed;
        }
        $includeSections = array_values(array_intersect($includeSections, $allowed));

        return [
            'data' => $data,
            'include' => $includeSections,
        ];
    }

    public static function assertCanView(string $username): void
    {
        try {
            PortfolioService::publicView($username, Auth::id());
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}
