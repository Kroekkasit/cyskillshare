<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class PortfolioAnalyticsService
{
    public static function increment(int $userId, string $metric): void
    {
        $allowed = ['portfolio_view', 'project_view', 'github_click', 'website_click', 'linkedin_click'];
        if (!in_array($metric, $allowed, true)) {
            return;
        }
        Database::execute(
            'INSERT INTO portfolio_analytics_daily (user_id, metric, metric_date, count)
             VALUES (?, ?, CURDATE(), 1)
             ON DUPLICATE KEY UPDATE count = count + 1',
            [$userId, $metric]
        );
        if ($metric === 'portfolio_view') {
            Database::execute('UPDATE portfolios SET view_count = view_count + 1 WHERE user_id = ?', [$userId]);
        }
    }

    /**
     * @return array<string, int>
     */
    public static function summary(int $userId): array
    {
        $rows = Database::fetchAll(
            'SELECT metric, SUM(count) AS total
             FROM portfolio_analytics_daily WHERE user_id = ?
             GROUP BY metric',
            [$userId]
        );
        $out = [
            'portfolio_view' => 0,
            'project_view' => 0,
            'github_click' => 0,
            'website_click' => 0,
            'linkedin_click' => 0,
        ];
        foreach ($rows as $row) {
            $out[(string) $row['metric']] = (int) $row['total'];
        }
        return $out;
    }
}
