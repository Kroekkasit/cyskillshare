<?php
/**
 * @var array<string, int> $summary
 * @var list<array<string, mixed>> $byCategory
 * @var list<array<string, mixed>> $recentActivity
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>My Progress</h1>
        <p class="muted">Track your Cyber Arena journey across categories and challenges.</p>
    </div>
</section>

<section class="arena-stats card" aria-label="Your statistics">
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($summary['points'] ?? 0) ?></span>
        <span class="arena-stat-label">Arena Points</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($summary['solved'] ?? 0) ?></span>
        <span class="arena-stat-label">Solved</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($summary['attempted'] ?? 0) ?></span>
        <span class="arena-stat-label">Attempted</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($summary['hints_used'] ?? 0) ?></span>
        <span class="arena-stat-label">Hints Used</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($summary['categories_practiced'] ?? 0) ?></span>
        <span class="arena-stat-label">Categories</span>
    </div>
</section>

<section class="arena-section">
    <h2>Progress by Category</h2>
    <?php if ($byCategory === []): ?>
        <div class="empty-state card">
            <p class="muted">No category progress yet. Start solving challenges!</p>
            <a class="btn btn-primary" href="<?= e(url('/arena/challenges')) ?>">Browse Challenges</a>
        </div>
    <?php else: ?>
        <div class="card">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th scope="col">Category</th>
                        <th scope="col">Solved</th>
                        <th scope="col">Total</th>
                        <th scope="col">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($byCategory as $row): ?>
                        <?php
                        $total = (int) ($row['total'] ?? 0);
                        $solved = (int) ($row['solved'] ?? 0);
                        $pct = $total > 0 ? (int) round(($solved / $total) * 100) : 0;
                        ?>
                        <tr>
                            <td><a href="<?= e(url('/arena/categories/' . $row['slug'])) ?>"><?= e($row['name']) ?></a></td>
                            <td><?= $solved ?></td>
                            <td><?= $total ?></td>
                            <td>
                                <div class="progress-bar" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-fill" style="width: <?= $pct ?>%"></div>
                                </div>
                                <span class="muted small"><?= $pct ?>%</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="arena-section">
    <h2>Recent Activity</h2>
    <?php if ($recentActivity === []): ?>
        <div class="empty-state card">
            <p class="muted">No activity yet. Submit your first flag!</p>
        </div>
    <?php else: ?>
        <ul class="activity-list card">
            <?php foreach ($recentActivity as $item): ?>
                <li>
                    <?php if ($item['kind'] === 'solve'): ?>
                        <span class="pill solved">Solved</span>
                    <?php elseif ($item['kind'] === 'hint'): ?>
                        <span class="pill">Hint</span>
                    <?php else: ?>
                        <span class="pill lock">Attempt</span>
                    <?php endif; ?>
                    <a href="<?= e(url('/arena/challenges/' . (int) $item['challenge_id'])) ?>"><?= e($item['title']) ?></a>
                    <?php if ($item['kind'] === 'solve' && (int) $item['points'] > 0): ?>
                        <span class="muted">+<?= (int) $item['points'] ?> pts</span>
                    <?php elseif ($item['kind'] === 'hint' && (int) $item['points'] > 0): ?>
                        <span class="muted">−<?= (int) $item['points'] ?> pts</span>
                    <?php endif; ?>
                    <span class="muted"><?= e(time_ago((string) $item['at'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
