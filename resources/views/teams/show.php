<?php
/**
 * @var array<string,mixed> $group
 * @var list<array<string,mixed>> $skills
 * @var list<array<string,mixed>> $members
 * @var string|null $viewer_role
 * @var array<string,mixed> $stats
 * @var string $basePath
 */
use App\Core\Auth;
$groupId = (int) $group['id'];
?>
<section class="page-header ctf-team-header">
    <div>
        <h1><?= e((string) $group['name']) ?></h1>
        <p class="muted">CTF Team · <?= (int) ($stats['member_count'] ?? 0) ?> players</p>
    </div>
    <div class="hero-actions">
        <?php if (Auth::check() && !$viewer_role): ?>
            <form method="post" action="<?= e(url('/groups/' . $groupId . '/join')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn btn-primary" type="submit">Request to join</button>
            </form>
        <?php elseif (Auth::check() && $viewer_role): ?>
            <form method="post" action="<?= e(url('/groups/' . $groupId . '/leave')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn" type="submit">Leave team</button>
            </form>
        <?php else: ?>
            <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login to join</a>
        <?php endif; ?>
        <a class="btn" href="<?= e(url('/teams')) ?>">All teams</a>
    </div>
</section>

<div class="card ctf-dashboard">
    <h2>Team stats</h2>
    <div class="stats-grid collab-stats">
        <div class="stat-card"><strong><?= (int) ($stats['challenges_solved'] ?? 0) ?></strong><span>Challenges solved</span></div>
        <div class="stat-card"><strong><?= (int) ($stats['labs_completed'] ?? 0) ?></strong><span>Labs completed</span></div>
        <div class="stat-card"><strong><?= (int) ($stats['writeups'] ?? 0) ?></strong><span>Writeups</span></div>
    </div>

    <?php if (!empty($stats['category_strength'])): ?>
    <h3>Category strength</h3>
    <div class="tag-row">
        <?php foreach ($stats['category_strength'] as $cat): ?>
            <span class="pill"><?= e((string) $cat['name']) ?> · <?= (int) $cat['count'] ?> solves</span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Roster</h2>
    <ul class="member-list">
        <?php foreach ($members as $m): ?>
            <li>
                <a href="<?= e(url('/profile/' . $m['username'])) ?>">@<?= e((string) $m['username']) ?></a>
                <span class="pill"><?= e(str_replace('_', ' ', (string) $m['role'])) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if ($skills !== []): ?>
<div class="card">
    <h2>Focus areas</h2>
    <div class="tag-row">
        <?php foreach ($skills as $sk): ?>
            <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h2>About</h2>
    <p><?= nl2br(e((string) $group['description'])) ?></p>
</div>
