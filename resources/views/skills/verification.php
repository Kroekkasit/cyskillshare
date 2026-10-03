<?php
/**
 * @var list<array<string, mixed>> $pending
 */
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>Verify Evidence</h1>
        <p class="muted">Review pending manual and project evidence submitted by students.</p>
    </div>
</section>

<?php if ($pending === []): ?>
    <div class="empty-state card">
        <h2>No pending evidence</h2>
        <p class="muted">All submissions have been reviewed.</p>
    </div>
<?php else: ?>
    <div class="card">
        <table class="leaderboard-table skill-verification-table">
            <thead>
                <tr>
                    <th scope="col">Student</th>
                    <th scope="col">Skill</th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Submitted</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending as $ev): ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/profile/' . $ev['username'])) ?>"><?= e($ev['username']) ?></a>
                        </td>
                        <td>
                            <a href="<?= e(url('/skills/' . $ev['skill_slug'])) ?>"><?= e($ev['skill_name']) ?></a>
                        </td>
                        <td>
                            <?= e($ev['title']) ?>
                            <?php if (!empty($ev['description'])): ?>
                                <div class="muted small"><?= e(mb_strimwidth((string) $ev['description'], 0, 120, '…')) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill"><?= e(str_replace('_', ' ', $ev['evidence_type'])) ?></span></td>
                        <td class="muted"><?= e(time_ago((string) $ev['created_at'])) ?></td>
                        <td class="skill-verification-actions">
                            <form method="post" action="<?= e(url('/skills/verification/' . (int) $ev['id'] . '/accept')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <button class="btn btn-primary btn-sm" type="submit">Accept</button>
                            </form>
                            <form method="post" action="<?= e(url('/skills/verification/' . (int) $ev['id'] . '/reject')) ?>" class="inline-form skill-reject-form">
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
