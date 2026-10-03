<?php
/**
 * @var \App\Models\Channel $channel
 * @var list<array<string, mixed>> $threads
 */
?>
<section class="hero-banner">
    <h1><?= e($channel->name) ?></h1>
    <p class="subtitle"><?= e((string) ($channel->description ?? 'Channel discussions')) ?></p>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/community')) ?>">← All channels</a>
    </div>
</section>

<div class="card">
    <h2>Threads</h2>
    <?php if ($threads === []): ?>
        <p class="muted">No threads yet.</p>
    <?php else: ?>
        <?php foreach ($threads as $t): ?>
            <div class="thread-item">
                <a href="<?= e(url('/thread/' . $t['id'])) ?>"><strong><?= e((string) $t['title']) ?></strong></a>
                <div class="muted">
                    by <?= e((string) $t['username']) ?> ·
                    <?= e((string) $t['status']) ?> ·
                    <?= (int) $t['views'] ?> views ·
                    <?= e((string) $t['created_at']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
