<?php
/**
 * @var list<array<string, mixed>> $items
 * @var int $total
 * @var int $page
 * @var array<string, string> $filters
 * @var list<array<string, mixed>> $categoryGroups
 */
use App\Core\Auth;

$totalPages = max(1, (int) ceil($total / 20));
?>
<section class="page-header">
    <div>
        <h1>Knowledge Base</h1>
        <p class="muted">Curated guides and reference articles for cybersecurity learning.</p>
    </div>
    <?php if (Auth::check()): ?>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('/knowledge/create')) ?>">+ New Article</a>
        </div>
    <?php endif; ?>
</section>

<?php if ($categoryGroups !== []): ?>
<section class="knowledge-category-grid">
    <?php foreach ($categoryGroups as $cat): ?>
        <a class="card knowledge-category-card" href="<?= e(url('/knowledge/category/' . $cat['slug'])) ?>">
            <h2><?= e((string) $cat['name']) ?></h2>
            <?php if (!empty($cat['description'])): ?>
                <p class="muted"><?= e((string) $cat['description']) ?></p>
            <?php endif; ?>
            <span class="muted small"><?= (int) ($cat['article_count'] ?? 0) ?> article<?= (int) ($cat['article_count'] ?? 0) === 1 ? '' : 's' ?></span>
        </a>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<form class="card writeup-filters" method="get" action="<?= e(url('/knowledge')) ?>">
    <div class="form-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input id="search" name="search" type="search" value="<?= e($filters['search']) ?>" placeholder="SQL injection, nmap…">
        </div>
        <div class="form-group">
            <label for="skill">Skill</label>
            <input id="skill" name="skill" type="text" value="<?= e($filters['skill']) ?>" placeholder="web-security">
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Search</button>
</form>

<h2 class="knowledge-section-title">All Articles</h2>
<p class="muted"><?= (int) $total ?> article<?= $total === 1 ? '' : 's' ?></p>

<?php if ($items === []): ?>
    <div class="empty-state card">
        <h2>No articles found</h2>
        <p class="muted">Try a different search or browse by category above.</p>
    </div>
<?php else: ?>
    <div class="knowledge-grid">
        <?php foreach ($items as $item): ?>
            <article class="knowledge-card card">
                <header>
                    <h3>
                        <a href="<?= e(url('/knowledge/article/' . $item['slug'])) ?>"><?= e((string) $item['title']) ?></a>
                    </h3>
                    <?php if (!empty($item['is_official'])): ?>
                        <span class="pill verified">Official</span>
                    <?php endif; ?>
                    <?php if (!empty($item['featured'])): ?>
                        <span class="pill featured">★</span>
                    <?php endif; ?>
                </header>
                <div class="article-meta muted">
                    <?php if (!empty($item['category_name'])): ?>
                        <a href="<?= e(url('/knowledge/category/' . $item['category_slug'])) ?>"><?= e((string) $item['category_name']) ?></a>
                        ·
                    <?php endif; ?>
                    <span class="pill"><?= e((string) ($item['difficulty'] ?? 'beginner')) ?></span>
                    · @<?= e((string) $item['username']) ?>
                </div>
                <?php if (!empty($item['summary'])): ?>
                    <p class="muted"><?= e((string) $item['summary']) ?></p>
                <?php endif; ?>
                <a class="btn" href="<?= e(url('/knowledge/article/' . $item['slug'])) ?>">Read Article</a>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= e(url('/knowledge?' . http_build_query(array_merge($filters, ['page' => $page - 1])))) ?>">← Previous</a>
            <?php endif; ?>
            <span class="muted">Page <?= (int) $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a class="btn" href="<?= e(url('/knowledge?' . http_build_query(array_merge($filters, ['page' => $page + 1])))) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
