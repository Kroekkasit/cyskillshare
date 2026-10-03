<?php
/** @var list<array<string,mixed>> $items */
/** @var int $total */
/** @var int $page */
/** @var array<string,string> $filters */
/** @var list<array{id:int,name:string,slug:string}> $skills */
/** @var string $basePath */
use App\Core\Auth;
?>
<section class="page-header">
    <div>
        <h1>CTF Teams</h1>
        <p class="muted">Find or form a team for capture-the-flag competitions.</p>
    </div>
    <?php if (Auth::check()): ?>
        <a class="btn btn-primary" href="<?= e(url('/teams/create')) ?>">+ Create Team</a>
    <?php endif; ?>
</section>

<form class="card collab-filters" method="get" action="<?= e(url('/teams')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" value="<?= e($filters['search'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="skill">Skill focus</label>
            <select id="skill" name="skill">
                <option value="">Any</option>
                <?php foreach ($skills as $sk): ?>
                    <option value="<?= e($sk['slug']) ?>"<?= ($filters['skill'] ?? '') === $sk['slug'] ? ' selected' : '' ?>><?= e($sk['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Filter</button>
    </div>
</form>

<?php if ($items === []): ?>
<div class="card"><p class="muted">No CTF teams yet. Be the first to create one!</p></div>
<?php else: ?>
<div class="group-grid">
    <?php foreach ($items as $group): ?>
        <?php \App\Core\View::partial('partials/group-card', ['group' => $group, 'basePath' => '/teams']); ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
