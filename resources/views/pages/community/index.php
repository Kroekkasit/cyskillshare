<?php
/**
 * @var list<array<string, mixed>> $threads
 * @var int $total
 * @var int $page
 * @var int $perPage
 * @var string $sort
 */
$sorts = [
    'latest' => 'Latest',
    'popular' => 'Popular',
    'unanswered' => 'Unanswered',
    'solved' => 'Solved',
    'mine' => 'My Discussions',
];
?>
<section class="page-header">
    <div>
        <h1>Community</h1>
        <p class="muted">Ask questions, share knowledge, discuss cybersecurity, and learn together.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(url('/community/new')) ?>">+ New Discussion</a>
</section>

<div class="filter-bar" role="navigation" aria-label="Discussion filters">
    <?php foreach ($sorts as $key => $label): ?>
        <?php if ($key === 'mine' && !\App\Core\Auth::check()) {
            continue;
        } ?>
        <a class="filter-chip<?= $sort === $key ? ' is-active' : '' ?>"
           href="<?= e(url('/community?sort=' . $key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
    <?php if (\App\Core\Auth::check()): ?>
        <a class="filter-chip" href="<?= e(url('/community/bookmarks')) ?>">Bookmarked</a>
    <?php endif; ?>
</div>

<?php if ($threads === []): ?>
    <div class="empty-state card">
        <h2>No discussions yet</h2>
        <p class="muted">Be the first person to start a discussion.</p>
        <a class="btn btn-primary" href="<?= e(url('/community/new')) ?>">Start Discussion</a>
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
        'baseUrl' => '/community',
        'query' => ['sort' => $sort],
    ]); ?>
<?php endif; ?>
