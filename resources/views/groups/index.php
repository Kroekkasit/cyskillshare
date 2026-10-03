<?php
/**
 * @var list<array<string,mixed>> $items
 * @var int $total
 * @var int $page
 * @var array<string,string> $filters
 * @var list<string> $groupTypes
 * @var list<array{id:int,name:string,slug:string}> $skills
 * @var string $basePath
 */
use App\Core\Auth;

$totalPages = max(1, (int) ceil($total / 12));
?>
<section class="page-header">
    <div>
        <h1>Study Groups</h1>
        <p class="muted">Join or create groups to learn together.</p>
    </div>
    <?php if (Auth::check()): ?>
        <a class="btn btn-primary" href="<?= e(url($basePath . '/create')) ?>">+ Create Group</a>
    <?php endif; ?>
</section>

<form class="card collab-filters" method="get" action="<?= e(url($basePath)) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Name or description…">
        </div>
        <div class="form-group">
            <label for="type">Type</label>
            <select id="type" name="type">
                <option value="">All types</option>
                <?php foreach ($groupTypes as $t): ?>
                    <option value="<?= e($t) ?>"<?= ($filters['type'] ?? '') === $t ? ' selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="skill">Skill</label>
            <select id="skill" name="skill">
                <option value="">Any skill</option>
                <?php foreach ($skills as $sk): ?>
                    <option value="<?= e($sk['slug']) ?>"<?= ($filters['skill'] ?? '') === $sk['slug'] ? ' selected' : '' ?>><?= e($sk['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group filter-actions">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </div>
</form>

<?php if ($items === []): ?>
<div class="card"><p class="muted">No groups found.</p></div>
<?php else: ?>
<div class="group-grid">
    <?php foreach ($items as $group): ?>
        <?php \App\Core\View::partial('partials/group-card', ['group' => $group, 'basePath' => $basePath]); ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
