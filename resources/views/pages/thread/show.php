<?php
/**
 * @var \App\Models\Thread $thread
 * @var \App\Models\User|null $author
 * @var \App\Models\Channel|null $channel
 * @var list<array<string, mixed>> $replies
 */
?>
<section class="hero" style="margin-bottom:1rem;">
    <h1><?= e($thread->title) ?></h1>
    <p class="muted">
        <?php if ($channel): ?>
            in <a href="<?= e(url('/community/' . $channel->slug)) ?>"><?= e($channel->name) ?></a> ·
        <?php endif; ?>
        by <?= e($author?->username ?? 'unknown') ?> ·
        <?= e($thread->status) ?> ·
        <?= (int) $thread->views ?> views
    </p>
</section>

<div class="card">
    <div><?= nl2br(e($thread->content)) ?></div>
</div>

<div class="card">
    <h2>Replies (<?= count($replies) ?>)</h2>
    <?php if ($replies === []): ?>
        <p class="muted">No replies yet.</p>
    <?php else: ?>
        <?php foreach ($replies as $r): ?>
            <div class="reply">
                <div class="muted">@<?= e((string) $r['username']) ?> · <?= e((string) $r['created_at']) ?></div>
                <div><?= nl2br(e((string) $r['content'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (\App\Core\Auth::check() && !$thread->is_locked): ?>
<div class="card">
    <h2>Post a reply</h2>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/thread/' . $thread->id . '/reply')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="content">Reply</label>
            <textarea id="content" name="content" rows="4" required minlength="2"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Reply</button>
    </form>
</div>
<?php elseif (!\App\Core\Auth::check()): ?>
    <p class="muted"><a href="<?= e(url('/login')) ?>">Log in</a> to reply.</p>
<?php endif; ?>
