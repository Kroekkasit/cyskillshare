<?php
/**
 * @var list<array<string, mixed>> $items
 * @var int $total
 * @var int $page
 * @var array<string, string> $filters
 * @var list<array<string, mixed>> $categories
 * @var list<array{id:int,name:string,slug:string}> $skills
 */
use App\Core\Auth;

$totalPages = max(1, (int) ceil($total / 12));
?>
<section class="page-header">
    <div>
        <h1>Writeups</h1>
        <p class="muted">Technical write-ups, CTF solutions, and security research from the community.</p>
    </div>
    <?php if (Auth::check()): ?>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('/writeups/create')) ?>">+ New Writeup</a>
        </div>
    <?php endif; ?>
</section>

<form class="card writeup-filters" method="get" action="<?= e(url('/writeups')) ?>">
    <div class="form-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input id="search" name="search" type="search" value="<?= e($filters['search']) ?>" placeholder="SQL injection, forensics…">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e((string) $cat['slug']) ?>" <?= $filters['category'] === $cat['slug'] ? 'selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="skill">Skill</label>
            <select id="skill" name="skill">
                <option value="">All skills</option>
                <?php foreach ($skills as $sk): ?>
                    <option value="<?= e($sk['slug']) ?>" <?= $filters['skill'] === $sk['slug'] ? 'selected' : '' ?>>
                        <?= e($sk['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tag">Tag</label>
            <input id="tag" name="tag" type="text" value="<?= e($filters['tag']) ?>" placeholder="ctf, web">
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <option value="">Any</option>
                <?php foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>" <?= $filters['difficulty'] === $d ? 'selected' : '' ?>><?= e(ucfirst($d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <?php foreach (['recent' => 'Recent', 'popular' => 'Popular', 'helpful' => 'Most Helpful', 'featured' => 'Featured'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $filters['sort'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Filter</button>
</form>

<p class="muted writeup-result-count"><?= (int) $total ?> writeup<?= $total === 1 ? '' : 's' ?> found</p>

<?php if ($items === []): ?>
    <div class="empty-state card">
        <h2>No writeups found</h2>
        <p class="muted">Try adjusting your filters or be the first to publish one.</p>
        <?php if (Auth::check()): ?>
            <a class="btn btn-primary" href="<?= e(url('/writeups/create')) ?>">Create Writeup</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="writeup-grid">
        <?php foreach ($items as $item): ?>
            <?php \App\Core\View::partial('partials/writeup-card', ['writeup' => $item]); ?>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Writeup pages">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= e(url('/writeups?' . http_build_query(array_merge($filters, ['page' => $page - 1])))) ?>">← Previous</a>
            <?php endif; ?>
            <span class="muted">Page <?= (int) $page ?> of <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a class="btn" href="<?= e(url('/writeups?' . http_build_query(array_merge($filters, ['page' => $page + 1])))) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
