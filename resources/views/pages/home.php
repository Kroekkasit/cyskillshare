<?php
/**
 * @var array<string, bool> $checks
 * @var \App\Models\User|null $user
 * @var string $phpVersion
 */
?>
<section class="hero-banner">
    <h1>CySkillShare</h1>
    <p class="subtitle">Cybersecurity skill sharing for students who want to
        <span class="accent-italic">learn together</span>.</p>
    <p class="org">College of Computing · Khon Kaen University</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/community')) ?>">Explore community</a>
        <?php if (!$user): ?>
            <a class="btn" href="<?= e(url('/register')) ?>">Create account</a>
        <?php else: ?>
            <a class="btn" href="<?= e(url('/u/' . $user->username)) ?>">View profile</a>
        <?php endif; ?>
    </div>
</section>

<div class="card">
    <h2>System Status</h2>
    <p class="muted">Phase 1 foundation health check.</p>
    <ul class="status-list">
        <?php foreach ($checks as $label => $ok): ?>
            <li>
                <span><?= e($label) ?><?= $label === 'PHP' ? ' ' . e($phpVersion) : '' ?></span>
                <span class="<?= $ok ? 'ok' : 'fail' ?>"><?= $ok ? '✓ Ready' : '✗ Failed' ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <h2>Session</h2>
    <?php if ($user): ?>
        <p>Logged in as <strong><?= e($user->username) ?></strong>
            (<?= e(implode(', ', $user->roleNames())) ?>)</p>
        <p class="muted">XSS check sample (escaped): <?= e('<script>alert(1)</script>') ?></p>
    <?php else: ?>
        <p>Not authenticated. <a href="<?= e(url('/login')) ?>">Log in</a> or
            <a href="<?= e(url('/register')) ?>">register</a>.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Quick Links</h2>
    <p class="muted" style="margin-top:0;">Jump into the foundation routes.</p>
    <div class="hero-actions" style="margin-top:0.85rem;">
        <a class="btn btn-primary" href="<?= e(url('/community')) ?>">Community</a>
        <a class="btn" href="<?= e(url('/login')) ?>">Login</a>
        <a class="btn" href="<?= e(url('/register')) ?>">Register</a>
    </div>
</div>
