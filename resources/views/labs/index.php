<?php
/**
 * @var list<array<string, mixed>> $labs
 * @var int $total
 * @var int $page
 * @var int $perPage
 * @var array<string, string> $filters
 * @var list<array<string, mixed>> $categories
 * @var list<array<string, mixed>> $recommendations
 */
use App\Core\Auth;

$sorts = [
    'recent' => 'Recently Published',
    'popular' => 'Most Started',
    'completed' => 'Most Completed',
    'difficulty' => 'Difficulty',
];
$totalPages = max(1, (int) ceil($total / max(1, $perPage)));
?>
<section class="page-header">
    <div>
        <h1>Cyber Labs</h1>
        <p class="muted">Hands-on practice environments with guided tasks and simulated targets.</p>
    </div>
    <?php if (Auth::check()): ?>
        <div class="hero-actions">
            <a class="btn" href="<?= e(url('/labs/history')) ?>">My Lab History</a>
        </div>
    <?php endif; ?>
</section>

<?php if ($recommendations !== []): ?>
<section class="card lab-recommendations">
    <h2>Recommended for you</h2>
    <div class="lab-grid lab-grid-compact">
        <?php foreach ($recommendations as $lab): ?>
            <?php \App\Core\View::partial('labs/partials/lab-card', ['lab' => $lab]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<form class="card lab-filters" method="get" action="<?= e(url('/labs')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Title or description…">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e((string) $cat['slug']) ?>"<?= ($filters['category'] ?? '') === $cat['slug'] ? ' selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <option value="">All difficulties</option>
                <?php foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>"<?= ($filters['difficulty'] ?? '') === $d ? ' selected' : '' ?>>
                        <?= e(ucfirst($d)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="skill">Skill slug</label>
            <input type="text" id="skill" name="skill" value="<?= e($filters['skill'] ?? '') ?>" placeholder="e.g. web-security">
        </div>
        <div class="form-group">
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <?php foreach ($sorts as $key => $label): ?>
                    <option value="<?= e($key) ?>"<?= ($filters['sort'] ?? 'recent') === $key ? ' selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Apply Filters</button>
</form>

<?php if ($labs === []): ?>
    <div class="empty-state card">
        <h2>No labs found</h2>
        <p class="muted">Try adjusting your filters or check back later.</p>
    </div>
<?php else: ?>
    <p class="muted lab-result-count"><?= (int) $total ?> lab<?= $total === 1 ? '' : 's' ?> found</p>
    <div class="lab-grid">
        <?php foreach ($labs as $lab): ?>
            <?php \App\Core\View::partial('labs/partials/lab-card', ['lab' => $lab]); ?>
        <?php endforeach; ?>
    </div>
    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Lab pagination">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= e(url('/labs?' . http_build_query(array_merge($filters, ['page' => $page - 1])))) ?>">← Previous</a>
            <?php endif; ?>
            <span class="muted">Page <?= (int) $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a class="btn" href="<?= e(url('/labs?' . http_build_query(array_merge($filters, ['page' => $page + 1])))) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
