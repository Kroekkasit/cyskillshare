<?php
/** @var list<\App\Models\Notification> $notifications */
?>
<section class="page-header">
    <div>
        <h1>Notifications</h1>
        <p class="muted">Replies, mentions, and best-answer updates.</p>
    </div>
    <?php if ($notifications !== []): ?>
        <form method="post" action="<?= e(url('/notifications/read')) ?>">
            <?= csrf_field() ?>
            <button class="btn" type="submit">Mark all read</button>
        </form>
    <?php endif; ?>
</section>

<?php if ($notifications === []): ?>
    <div class="empty-state card">
        <h2>You're all caught up</h2>
        <p class="muted">No notifications yet.</p>
    </div>
<?php else: ?>
    <div class="notification-list">
        <?php foreach ($notifications as $n): ?>
            <article class="card notification-item<?= $n->is_read ? '' : ' is-unread' ?>">
                <div>
                    <strong><?= e($n->title) ?></strong>
                    <?php if ($n->message): ?><p class="muted"><?= e($n->message) ?></p><?php endif; ?>
                    <div class="muted small"><?= e(time_ago($n->created_at)) ?></div>
                </div>
                <div class="hero-actions">
                    <?php if ($n->reference_type === 'thread' && $n->reference_id): ?>
                        <a class="btn" href="<?= e(url('/thread/' . $n->reference_id)) ?>">Open</a>
                    <?php endif; ?>
                    <?php if (!$n->is_read): ?>
                        <form method="post" action="<?= e(url('/notifications/read')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="notification_id" value="<?= (int) $n->id ?>">
                            <button class="btn" type="submit">Mark read</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
