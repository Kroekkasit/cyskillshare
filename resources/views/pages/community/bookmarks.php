<?php
/** @var list<array<string, mixed>> $threads */
?>
<section class="page-header">
    <div>
        <h1>Bookmarks</h1>
        <p class="muted">Discussions you saved for later.</p>
    </div>
</section>

<?php if ($threads === []): ?>
    <div class="empty-state card">
        <h2>You haven't bookmarked any discussions yet</h2>
        <p class="muted">Browse the community and star threads you want to revisit.</p>
        <a class="btn btn-primary" href="<?= e(url('/community')) ?>">Browse Community</a>
    </div>
<?php else: ?>
    <div class="thread-list">
        <?php foreach ($threads as $thread): ?>
            <?php \App\Core\View::partial('components/thread-card', ['thread' => $thread]); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
