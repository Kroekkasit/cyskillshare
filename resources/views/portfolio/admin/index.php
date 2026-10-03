<?php
/**
 * @var list<array<string, mixed>> $recentProjects
 * @var int $pendingCount
 * @var bool $canVerify
 */
use App\Core\Auth;
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1>Portfolio Admin</h1>
        <p class="muted">Overview of recent projects and verification queue.</p>
    </div>
    <?php if ($canVerify): ?>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('/admin/projects/verification')) ?>">
                Pending Verifications<?= $pendingCount > 0 ? ' (' . $pendingCount . ')' : '' ?>
            </a>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Recent Projects</h2>
    <?php if ($recentProjects === []): ?>
        <p class="muted">No projects yet.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Owner</th>
                    <th scope="col">Status</th>
                    <th scope="col">Updated</th>
                    <?php if (Auth::hasRole('admin')): ?>
                        <th scope="col">Feature</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentProjects as $p): ?>
                    <tr>
                        <td>
                            <?php if (($p['publish_status'] ?? '') === 'published'): ?>
                                <a href="<?= e(url('/projects/' . $p['username'] . '/' . $p['slug'])) ?>"><?= e((string) $p['title']) ?></a>
                            <?php else: ?>
                                <?= e((string) $p['title']) ?>
                            <?php endif; ?>
                            <?php if (!empty($p['verified'])): ?><span class="pill verified">✓</span><?php endif; ?>
                        </td>
                        <td><a href="<?= e(url('/portfolio/' . $p['username'])) ?>">@<?= e((string) $p['username']) ?></a></td>
                        <td><span class="pill"><?= e((string) $p['publish_status']) ?></span></td>
                        <td class="muted"><?= e(time_ago((string) $p['updated_at'])) ?></td>
                        <?php if (Auth::hasRole('admin')): ?>
                            <td>
                                <form method="post" action="<?= e(url('/admin/portfolio/' . (int) $p['id'] . '/feature')) ?>" class="inline-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="featured" value="<?= !empty($p['featured']) ? '0' : '1' ?>">
                                    <button class="btn btn-sm" type="submit"><?= !empty($p['featured']) ? 'Unfeature' : 'Feature' ?></button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
