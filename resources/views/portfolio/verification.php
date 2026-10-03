<?php
/**
 * @var list<array<string, mixed>> $pending
 */
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1>Verify Projects</h1>
        <p class="muted">Review pending project verification requests from students.</p>
    </div>
</section>

<?php if ($pending === []): ?>
    <div class="empty-state card">
        <h2>No pending requests</h2>
        <p class="muted">All verification requests have been processed.</p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="leaderboard-table portfolio-verification-table">
            <thead>
                <tr>
                    <th scope="col">Project</th>
                    <th scope="col">Owner</th>
                    <th scope="col">Requested</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $pv): ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/projects/' . $pv['owner_username'] . '/' . $pv['slug'])) ?>"><?= e((string) $pv['title']) ?></a>
                        </td>
                        <td>
                            <a href="<?= e(url('/portfolio/' . $pv['owner_username'])) ?>">@<?= e((string) $pv['owner_username']) ?></a>
                        </td>
                        <td class="muted"><?= e(time_ago((string) $pv['created_at'])) ?></td>
                        <td class="portfolio-verification-actions">
                            <form method="post" action="<?= e(url('/admin/projects/verification/' . (int) $pv['id'] . '/accept')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="text" name="note" placeholder="Optional note" maxlength="500" aria-label="Verification note">
                                <button class="btn btn-primary btn-sm" type="submit">Accept</button>
                            </form>
                            <form method="post" action="<?= e(url('/admin/projects/verification/' . (int) $pv['id'] . '/reject')) ?>" class="inline-form portfolio-reject-form">
                                <?= csrf_field() ?>
                                <input type="text" name="reason" placeholder="Reason (required)" maxlength="500" required aria-label="Rejection reason">
                                <button class="btn btn-sm" type="submit">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
