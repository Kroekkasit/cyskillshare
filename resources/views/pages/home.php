<?php
/**
 * @var array<string, bool> $checks
 * @var \App\Models\User|null $user
 * @var string $phpVersion
 * @var array{discussions:int,solved_week:int,members:int} $stats
 * @var list<array<string, mixed>> $recentDiscussions
 * @var list<array<string, mixed>> $latestWriteups
 * @var list<array<string, mixed>> $recommendedLabs
 */
$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
?>
<section class="hero-banner">
    <h1><?= e($greeting) ?><?= $user ? ', ' . e($user->username) : '' ?>.</h1>
    <p class="subtitle">Cybersecurity Community for students who want to
        <span class="accent-italic">learn together</span>.</p>
    <p class="org">College of Computing · Khon Kaen University</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/community')) ?>">Open Community</a>
        <a class="btn" href="<?= e(url('/community/new')) ?>">+ New Discussion</a>
    </div>
</section>

<div class="stats-grid">
    <div class="stat-card"><strong><?= (int) $stats['discussions'] ?></strong><span>Active Discussions</span></div>
    <div class="stat-card"><strong><?= (int) $stats['solved_week'] ?></strong><span>Solved This Week</span></div>
    <div class="stat-card"><strong><?= (int) $stats['members'] ?></strong><span>Community Members</span></div>
</div>

<div class="card">
    <h2>Recent Discussions</h2>
    <?php if ($recentDiscussions === []): ?>
        <p class="muted">No discussions yet. <a href="<?= e(url('/community/new')) ?>">Start one</a>.</p>
    <?php else: ?>
        <ul class="result-list">
            <?php foreach ($recentDiscussions as $t): ?>
                <li>
                    <a href="<?= e(url('/thread/' . $t['id'])) ?>"><?= e((string) $t['title']) ?></a>
                    <div class="muted small">
                        [<?= e((string) $t['channel_name']) ?>]
                        · <?= (int) $t['reply_count'] ?> replies
                        <?php if ($t['status'] === 'solved'): ?> · ✓ Solved<?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php if (($recommendedLabs ?? []) !== []): ?>
<div class="card">
    <h2>Recommended Labs</h2>
    <div class="lab-grid lab-grid-compact">
        <?php foreach ($recommendedLabs as $lab): ?>
            <?php \App\Core\View::partial('labs/partials/lab-card', ['lab' => $lab]); ?>
        <?php endforeach; ?>
    </div>
    <a class="btn" href="<?= e(url('/labs')) ?>">Browse all labs</a>
</div>
<?php endif; ?>

<?php if ($latestWriteups !== []): ?>
<div class="card">
    <h2>Latest Writeups</h2>
    <div class="writeup-grid writeup-grid-compact">
        <?php foreach ($latestWriteups as $wu): ?>
            <?php \App\Core\View::partial('partials/writeup-card', ['writeup' => $wu]); ?>
        <?php endforeach; ?>
    </div>
    <a class="btn" href="<?= e(url('/writeups')) ?>">Browse all writeups</a>
</div>
<?php endif; ?>

<details class="card">
    <summary><strong>System Status</strong> <span class="muted">(foundation checks)</span></summary>
    <ul class="status-list">
        <?php foreach ($checks as $label => $ok): ?>
            <li>
                <span><?= e($label) ?><?= $label === 'PHP' ? ' ' . e($phpVersion) : '' ?></span>
                <span class="<?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? '✓ Ready' : '✗ Failed' ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</details>
