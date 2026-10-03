<?php
/** @var list<array<string, mixed>> $reports */
$reasonLabels = [
    'spam' => 'Spam',
    'harassment' => 'Harassment',
    'malicious_content' => 'Malicious Content',
    'incorrect_dangerous' => 'Incorrect / Dangerous',
    'academic_misconduct' => 'Academic Misconduct',
    'other' => 'Other',
];
?>
<section class="page-header">
    <div>
        <h1>Pending Reports</h1>
        <p class="muted">Moderator queue — review community reports.</p>
    </div>
</section>

<?php if ($reports === []): ?>
    <div class="empty-state card">
        <h2>No pending reports</h2>
        <p class="muted">The queue is clear.</p>
    </div>
<?php else: ?>
    <div class="card table-card">
        <table class="data-table">
            <thead>
            <tr>
                <th>Target</th>
                <th>Reporter</th>
                <th>Reason</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reports as $r): ?>
                <tr>
                    <td>
                        <?= e((string) $r['target_type']) ?> #<?= (int) $r['target_id'] ?>
                        <?php if ($r['target_type'] === 'thread'): ?>
                            · <a href="<?= e(url('/thread/' . $r['target_id'])) ?>">Open</a>
                        <?php endif; ?>
                        <?php if (!empty($r['description'])): ?>
                            <div class="muted small"><?= e((string) $r['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= e((string) $r['reporter_username']) ?></td>
                    <td><?= e($reasonLabels[$r['reason']] ?? (string) $r['reason']) ?></td>
                    <td><?= e(time_ago((string) $r['created_at'])) ?></td>
                    <td class="table-actions">
                        <form method="post" action="<?= e(url('/moderation/reports/' . $r['id'] . '/resolve')) ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-primary" type="submit">Resolve</button>
                        </form>
                        <form method="post" action="<?= e(url('/moderation/reports/' . $r['id'] . '/dismiss')) ?>">
                            <?= csrf_field() ?>
                            <button class="btn" type="submit">Dismiss</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
