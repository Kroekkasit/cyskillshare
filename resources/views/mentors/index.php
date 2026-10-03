<?php
/** @var list<array<string,mixed>> $items */
/** @var int $total */
/** @var int $page */
/** @var array<string,mixed> $filters */
/** @var list<array{id:int,name:string,slug:string}> $skills */
use App\Core\Auth;
?>
<section class="page-header">
    <div>
        <h1>Mentors</h1>
        <p class="muted">Connect with experienced students and instructors.</p>
    </div>
    <div class="hero-actions">
        <?php if (Auth::check()): ?>
            <a class="btn btn-primary" href="<?= e(url('/mentors/settings')) ?>">Mentor settings</a>
            <a class="btn" href="<?= e(url('/mentorship')) ?>">My mentorships</a>
        <?php endif; ?>
    </div>
</section>

<form class="card collab-filters" method="get" action="<?= e(url('/mentors')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="skill">Skill</label>
            <select id="skill" name="skill">
                <option value="">Any</option>
                <?php foreach ($skills as $sk): ?>
                    <option value="<?= e($sk['slug']) ?>"<?= ($filters['skill'] ?? '') === $sk['slug'] ? ' selected' : '' ?>><?= e($sk['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="verified" value="1"<?= !empty($filters['verified']) ? ' checked' : '' ?>>
                Verified only
            </label>
        </div>
        <button class="btn btn-primary" type="submit">Filter</button>
    </div>
</form>

<?php if ($items === []): ?>
<div class="card"><p class="muted">No mentors match your filters.</p></div>
<?php else: ?>
<div class="people-grid">
    <?php foreach ($items as $m): ?>
        <article class="person-card card collab-card">
            <h3><a href="<?= e(url('/mentors/' . $m['username'])) ?>">@<?= e((string) $m['username']) ?></a></h3>
            <?php if (($m['verification_status'] ?? '') === 'verified'): ?>
                <span class="pill verified">✓ Verified</span>
            <?php endif; ?>
            <?php if (!empty($m['bio'])): ?>
                <p class="muted"><?= e(mb_strimwidth((string) $m['bio'], 0, 120, '…')) ?></p>
            <?php endif; ?>
            <?php if (!empty($m['skills'])): ?>
                <div class="tag-row">
                    <?php foreach (array_slice($m['skills'], 0, 4) as $sk): ?>
                        <span class="tag-pill"><?= e((string) $sk['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <p class="muted small"><?= (int) ($m['active_mentees'] ?? 0) ?> active mentees</p>
            <a class="btn btn-sm btn-primary" href="<?= e(url('/mentors/' . $m['username'])) ?>">View profile</a>
        </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
