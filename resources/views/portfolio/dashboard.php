<?php
/**
 * @var \App\Models\User|null $user
 * @var array{percent:int,missing:list<string>,counts:array<string,int>} $completeness
 * @var array<string, int> $analytics
 * @var list<array<string, mixed>> $projects
 * @var list<string> $recommendations
 */
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1>Portfolio Dashboard</h1>
        <p class="muted">Track completeness, analytics, and manage your showcase.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/settings/portfolio')) ?>">Settings</a>
        <a class="btn" href="<?= e(url('/projects/create')) ?>">New Project</a>
        <?php if ($user): ?>
            <a class="btn" href="<?= e(url('/portfolio/' . $user->username)) ?>">View Portfolio</a>
        <?php endif; ?>
    </div>
</section>

<section class="card portfolio-completeness">
    <h2>Profile Completeness</h2>
    <div class="completeness-bar" role="progressbar" aria-valuenow="<?= (int) $completeness['percent'] ?>" aria-valuemin="0" aria-valuemax="100">
        <div class="completeness-bar-fill" style="width: <?= (int) $completeness['percent'] ?>%"></div>
    </div>
    <p class="completeness-percent"><?= (int) $completeness['percent'] ?>% complete</p>
    <?php if ($recommendations !== []): ?>
        <h3>Suggestions</h3>
        <ul class="portfolio-suggestions">
            <?php foreach ($recommendations as $tip): ?>
                <li><?= e($tip) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="arena-stats card portfolio-analytics">
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($completeness['counts']['projects'] ?? 0) ?></span>
        <span class="arena-stat-label">Projects</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($completeness['counts']['published_projects'] ?? 0) ?></span>
        <span class="arena-stat-label">Published</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($analytics['portfolio_view'] ?? 0) ?></span>
        <span class="arena-stat-label">Portfolio Views</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($analytics['project_view'] ?? 0) ?></span>
        <span class="arena-stat-label">Project Views</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($completeness['counts']['challenges'] ?? 0) ?></span>
        <span class="arena-stat-label">Challenges Solved</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($completeness['counts']['verified_projects'] ?? 0) ?></span>
        <span class="arena-stat-label">Verified Projects</span>
    </div>
</section>

<section class="portfolio-section">
    <h2>Your Projects</h2>
    <?php if ($projects === []): ?>
        <div class="empty-state card">
            <h3>No projects yet</h3>
            <p class="muted">Create your first project to showcase your work.</p>
            <a class="btn btn-primary" href="<?= e(url('/projects/create')) ?>">Create Project</a>
        </div>
    <?php else: ?>
        <div class="project-list-admin">
            <?php foreach ($projects as $p): ?>
                <div class="card portfolio-project-row">
                    <div>
                        <strong><a href="<?= e(url('/projects/edit/' . (int) $p['id'])) ?>"><?= e((string) $p['title']) ?></a></strong>
                        <div class="muted small">
                            <span class="pill"><?= e((string) $p['publish_status']) ?></span>
                            <?php if (!empty($p['featured'])): ?><span class="pill featured">Featured</span><?php endif; ?>
                            <?php if (!empty($p['verified'])): ?><span class="pill verified">Verified</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <?php if (($p['publish_status'] ?? '') === 'published' && $user): ?>
                            <a class="btn btn-sm" href="<?= e(url('/projects/' . $user->username . '/' . $p['slug'])) ?>">View</a>
                        <?php endif; ?>
                        <a class="btn btn-sm" href="<?= e(url('/projects/edit/' . (int) $p['id'])) ?>">Edit</a>
                        <?php if (($p['publish_status'] ?? '') !== 'published'): ?>
                            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/publish')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="redirect" value="/dashboard/portfolio">
                                <button class="btn btn-primary btn-sm" type="submit">Publish</button>
                            </form>
                        <?php endif; ?>
                        <?php if (($p['publish_status'] ?? '') === 'published'): ?>
                            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/feature')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="redirect" value="/dashboard/portfolio">
                                <input type="hidden" name="featured" value="<?= !empty($p['featured']) ? '0' : '1' ?>">
                                <button class="btn btn-sm" type="submit"><?= !empty($p['featured']) ? 'Unfeature' : 'Feature' ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
