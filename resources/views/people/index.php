<?php
/**
 * @var list<array<string,mixed>> $items
 * @var int $total
 * @var int $page
 * @var array<string,mixed> $filters
 * @var list<array{id:int,name:string,slug:string}> $skills
 * @var bool $showInDiscovery
 */
use App\Core\Auth;

$totalPages = max(1, (int) ceil($total / 12));
?>
<section class="page-header">
    <div>
        <h1>Discover People</h1>
        <p class="muted">Find students and mentors to collaborate with.</p>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/collaboration')) ?>">Collaboration Hub</a>
        <a class="btn" href="<?= e(url('/mentors')) ?>">Mentors</a>
    </div>
</section>

<?php if (Auth::check()): ?>
<div class="card collab-privacy-card">
    <h2>Discovery privacy</h2>
    <form method="post" action="<?= e(url('/people/privacy')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= e(url('/people')) ?>">
        <label class="checkbox-label">
            <input type="checkbox" name="show_in_discovery" value="1"<?= $showInDiscovery ? ' checked' : '' ?>>
            Show me in people discovery and collaboration recommendations
        </label>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</div>
<?php endif; ?>

<form class="card collab-filters" method="get" action="<?= e(url('/people')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="q">Search</label>
            <input type="search" id="q" name="q" value="<?= e((string) ($filters['q'] ?? '')) ?>" placeholder="Username or name…">
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
        <div class="form-group">
            <label for="min_level">Min level</label>
            <select id="min_level" name="min_level">
                <option value="">Any</option>
                <?php for ($l = 1; $l <= 5; $l++): ?>
                    <option value="<?= $l ?>"<?= (string) ($filters['min_level'] ?? '') === (string) $l ? ' selected' : '' ?>><?= $l ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="looking_for">Filter</label>
            <select id="looking_for" name="looking_for">
                <option value="">Everyone</option>
                <option value="mentor"<?= ($filters['looking_for'] ?? '') === 'mentor' ? ' selected' : '' ?>>Mentors only</option>
            </select>
        </div>
        <div class="form-group filter-actions">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </div>
</form>

<?php if ($items === []): ?>
<div class="card"><p class="muted">No people match your search.</p></div>
<?php else: ?>
<div class="people-grid">
    <?php foreach ($items as $person): ?>
        <?php \App\Core\View::partial('partials/person-card', ['person' => $person]); ?>
    <?php endforeach; ?>
</div>
<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="People pages">
    <?php for ($p = 1; $p <= min($totalPages, 10); $p++): ?>
        <a class="btn btn-sm<?= $p === $page ? ' is-active' : '' ?>" href="<?= e(url('/people?' . http_build_query(array_merge($filters, ['page' => $p])))) ?>"><?= $p ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
