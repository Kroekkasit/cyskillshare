<?php
/**
 * @var array{
 *   mentors:list<array<string,mixed>>,
 *   groups:list<array<string,mixed>>,
 *   teams:list<array<string,mixed>>,
 *   projects:list<array<string,mixed>>,
 *   recruitment:list<array<string,mixed>>
 * } $recommended
 */
use App\Core\Auth;
?>
<section class="page-header">
    <div>
        <h1>Collaboration</h1>
        <p class="muted">Find study groups, CTF teams, mentors, and collaborators matched to your skills.</p>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/people')) ?>">Discover People</a>
        <a class="btn" href="<?= e(url('/groups')) ?>">Browse Groups</a>
        <?php if (Auth::check()): ?>
            <a class="btn btn-primary" href="<?= e(url('/mentorship')) ?>">My Mentorships</a>
        <?php endif; ?>
    </div>
</section>

<?php if (($recommended['mentors'] ?? []) !== []): ?>
<section class="card collab-section">
    <h2>Recommended Mentors</h2>
    <div class="people-grid">
        <?php foreach ($recommended['mentors'] as $m): ?>
            <article class="person-card card collab-card">
                <h3><a href="<?= e(url('/mentors/' . ($m['username'] ?? ''))) ?>">@<?= e((string) ($m['username'] ?? '')) ?></a></h3>
                <?php if (!empty($m['full_name'])): ?><p class="muted small"><?= e((string) $m['full_name']) ?></p><?php endif; ?>
                <?php if (!empty($m['reasons'])): ?>
                    <?php \App\Core\View::partial('partials/reason-list', ['reasons' => $m['reasons']]); ?>
                <?php endif; ?>
                <a class="btn btn-sm" href="<?= e(url('/mentors/' . ($m['username'] ?? ''))) ?>">View mentor</a>
            </article>
        <?php endforeach; ?>
    </div>
    <a class="btn" href="<?= e(url('/mentors')) ?>">All mentors</a>
</section>
<?php endif; ?>

<?php if (($recommended['groups'] ?? []) !== []): ?>
<section class="card collab-section">
    <h2>Study Groups for You</h2>
    <div class="group-grid">
        <?php foreach ($recommended['groups'] as $g): ?>
            <?php \App\Core\View::partial('partials/group-card', ['group' => $g, 'basePath' => '/groups']); ?>
        <?php endforeach; ?>
    </div>
    <a class="btn" href="<?= e(url('/groups')) ?>">All groups</a>
</section>
<?php endif; ?>

<?php if (($recommended['teams'] ?? []) !== []): ?>
<section class="card collab-section">
    <h2>CTF Teams for You</h2>
    <div class="group-grid">
        <?php foreach ($recommended['teams'] as $g): ?>
            <?php \App\Core\View::partial('partials/group-card', ['group' => $g, 'basePath' => '/teams']); ?>
        <?php endforeach; ?>
    </div>
    <a class="btn" href="<?= e(url('/teams')) ?>">All teams</a>
</section>
<?php endif; ?>

<?php if (($recommended['projects'] ?? []) !== []): ?>
<section class="card collab-section">
    <h2>Project Teams</h2>
    <div class="group-grid">
        <?php foreach ($recommended['projects'] as $g): ?>
            <?php \App\Core\View::partial('partials/group-card', ['group' => $g, 'basePath' => '/groups']); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (($recommended['recruitment'] ?? []) !== []): ?>
<section class="card collab-section">
    <h2>Open Recruitment</h2>
    <ul class="result-list">
        <?php foreach ($recommended['recruitment'] as $r): ?>
            <li>
                <strong><?= e((string) ($r['title'] ?? '')) ?></strong>
                <span class="muted"> · @<?= e((string) ($r['username'] ?? '')) ?></span>
                <?php if (!empty($r['group_name'])): ?>
                    <span class="muted"> · <?= e((string) $r['group_name']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <a class="btn" href="<?= e(url('/recruitment')) ?>">All recruitment posts</a>
</section>
<?php endif; ?>

<?php if (
    ($recommended['mentors'] ?? []) === [] &&
    ($recommended['groups'] ?? []) === [] &&
    ($recommended['teams'] ?? []) === []
): ?>
<div class="card">
    <p class="muted">Build your skill profile to get personalized collaboration recommendations, or browse manually.</p>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/skills')) ?>">Skill Tree</a>
        <a class="btn" href="<?= e(url('/people')) ?>">Discover People</a>
    </div>
</div>
<?php endif; ?>
