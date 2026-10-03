<?php
/**
 * @var array<string, mixed> $category
 * @var list<array<string, mixed>> $items
 * @var int $total
 * @var int $page
 */
$totalPages = max(1, (int) ceil($total / 20));
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/knowledge')) ?>">Knowledge Base</a>
    <span>/</span>
    <span><?= e((string) $category['name']) ?></span>
</nav>

<section class="page-header">
    <div>
        <h1><?= e((string) $category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
            <p class="muted"><?= e((string) $category['description']) ?></p>
        <?php endif; ?>
    </div>
</section>

<p class="muted"><?= (int) $total ?> article<?= $total === 1 ? '' : 's' ?></p>

<?php if ($items === []): ?>
    <div class="empty-state card">
        <h2>No articles in this category</h2>
        <a class="btn" href="<?= e(url('/knowledge')) ?>">Browse all articles</a>
    </div>
<?php else: ?>
    <div class="knowledge-grid">
        <?php foreach ($items as $item): ?>
            <article class="knowledge-card card">
                <h3>
                    <a href="<?= e(url('/knowledge/article/' . $item['slug'])) ?>"><?= e((string) $item['title']) ?></a>
                </h3>
                <div class="article-meta muted">
                    <span class="pill"><?= e((string) ($item['difficulty'] ?? 'beginner')) ?></span>
                    · @<?= e((string) $item['username']) ?>
                </div>
                <?php if (!empty($item['summary'])): ?>
                    <p class="muted"><?= e((string) $item['summary']) ?></p>
                <?php endif; ?>
                <a class="btn" href="<?= e(url('/knowledge/article/' . $item['slug'])) ?>">Read</a>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= e(url('/knowledge/category/' . $category['slug'] . '?page=' . ($page - 1))) ?>">← Previous</a>
            <?php endif; ?>
            <span class="muted">Page <?= (int) $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a class="btn" href="<?= e(url('/knowledge/category/' . $category['slug'] . '?page=' . ($page + 1))) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
