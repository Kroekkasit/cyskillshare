<?php
/**
 * @var list<array<string, mixed>> $items
 * @var int $total
 * @var int $page
 * @var array<string, mixed> $filters
 * @var list<string> $projectTypes
 * @var list<array{id:int,name:string,slug:string}> $skills
 */
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1>Discover Projects</h1>
        <p class="muted">Browse cybersecurity projects from the CySkillShare community.</p>
    </div>
</section>

<form class="card portfolio-filters" method="get" action="<?= e(url('/projects')) ?>">
    <div class="form-row portfolio-filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input id="search" name="search" type="search" value="<?= e((string) ($filters['search'] ?? '')) ?>" placeholder="Title, description…">
        </div>
        <div class="form-group">
            <label for="skill">Skill</label>
            <select id="skill" name="skill">
                <option value="">All skills</option>
                <?php foreach ($skills as $sk): ?>
                    <option value="<?= e((string) $sk['slug']) ?>" <?= ($filters['skill'] ?? '') === $sk['slug'] ? 'selected' : '' ?>><?= e((string) $sk['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="">All types</option>
                <?php foreach ($projectTypes as $t): ?>
                    <option value="<?= e($t) ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $t)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="technology">Technology</label>
            <input id="technology" name="technology" type="text" value="<?= e((string) ($filters['technology'] ?? '')) ?>" placeholder="Python, Docker…">
        </div>
        <div class="form-group">
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <?php foreach (['recent' => 'Recent', 'featured' => 'Featured', 'popular' => 'Popular'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($filters['sort'] ?? 'recent') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group portfolio-filter-check">
            <label><input type="checkbox" name="verified" value="1" <?= !empty($filters['verified']) ? 'checked' : '' ?>> Verified only</label>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Filter</button>
</form>

<?php if ($items === []): ?>
    <div class="empty-state card">
        <h2>No projects found</h2>
        <p class="muted">Try adjusting your filters or check back later.</p>
    </div>
<?php else: ?>
    <p class="muted portfolio-result-count"><?= (int) $total ?> project<?= $total !== 1 ? 's' : '' ?> found</p>
    <div class="project-grid">
        <?php foreach ($items as $project): ?>
            <?php \App\Core\View::partial('portfolio/partials/project-card', ['project' => $project]); ?>
        <?php endforeach; ?>
    </div>
    <?php if ($total > 12): ?>
        <nav class="pagination" aria-label="Project pages">
            <?php if ($page > 1): ?>
                <a class="btn" href="<?= e(url('/projects?' . http_build_query(array_merge($filters, ['page' => $page - 1])))) ?>">← Previous</a>
            <?php endif; ?>
            <span class="muted">Page <?= (int) $page ?></span>
            <?php if ($page * 12 < $total): ?>
                <a class="btn" href="<?= e(url('/projects?' . http_build_query(array_merge($filters, ['page' => $page + 1])))) ?>">Next →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
