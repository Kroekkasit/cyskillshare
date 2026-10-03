<?php
/**
 * @var \App\Models\Channel $channel
 * @var list<array<string, mixed>> $threads
 * @var int $total
 * @var int $page
 * @var int $perPage
 * @var string $sort
 * @var int $threadCount
 */
$sorts = ['latest' => 'Latest', 'popular' => 'Popular', 'unanswered' => 'Unanswered', 'solved' => 'Solved'];
?>
<section class="page-header">
    <div>
        <h1><?= e($channel->name) ?></h1>
        <p class="muted"><?= e((string) ($channel->description ?? '')) ?></p>
        <p class="muted small"><?= (int) $threadCount ?> Discussions</p>
    </div>
    <a class="btn btn-primary" href="<?= e(url('/community/new?channel=' . urlencode($channel->slug))) ?>">+ New Discussion</a>
</section>

<div class="filter-bar">
    <?php foreach ($sorts as $key => $label): ?>
        <a class="filter-chip<?= $sort === $key ? ' is-active' : '' ?>"
           href="<?= e(url('/community/' . $channel->slug . '?sort=' . $key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if ($threads === []): ?>
    <div class="empty-state card">
        <h2>No discussions in this channel</h2>
        <p class="muted">Start the first conversation here.</p>
        <a class="btn btn-primary" href="<?= e(url('/community/new?channel=' . urlencode($channel->slug))) ?>">Start Discussion</a>
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
        'baseUrl' => '/community/' . $channel->slug,
        'query' => ['sort' => $sort],
    ]); ?>
<?php endif; ?>
