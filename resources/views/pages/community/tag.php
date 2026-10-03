<?php
/**
 * @var \App\Models\Tag $tag
 * @var int $threadCount
 * @var list<array<string, mixed>> $threads
 * @var int $total
 * @var int $page
 * @var int $perPage
 * @var string $sort
 */
?>
<section class="page-header">
    <div>
        <h1>#<?= e($tag->slug) ?></h1>
        <p class="muted">Threads: <?= (int) $threadCount ?></p>
    </div>
</section>

<div class="filter-bar">
    <a class="filter-chip<?= $sort === 'latest' ? ' is-active' : '' ?>" href="<?= e(url('/tag/' . $tag->slug . '?sort=latest')) ?>">Latest</a>
    <a class="filter-chip<?= $sort === 'popular' ? ' is-active' : '' ?>" href="<?= e(url('/tag/' . $tag->slug . '?sort=popular')) ?>">Popular</a>
</div>

<?php if ($threads === []): ?>
    <div class="empty-state card">
        <h2>No discussions with this tag</h2>
        <p class="muted">Try a different keyword or tag.</p>
    </div>
<?php else: ?>
    <div class="thread-list">
        <?php foreach ($threads as $thread): ?>
            <?php \App\Core\View::partial('components/thread-card', ['thread' => $thread]); ?>
        <?php endforeach; ?>
    </div>
    <?php \App\Core\View::partial('components/pagination', [
        'page' => $page,
        'total' => $total,
        'perPage' => $perPage,
        'baseUrl' => '/tag/' . $tag->slug,
        'query' => ['sort' => $sort],
    ]); ?>
<?php endif; ?>
