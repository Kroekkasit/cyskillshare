<?php
/**
 * @var \App\Models\Skill $skill
 * @var list<array<string, mixed>> $requirements
 * @var list<array<string, mixed>> $prerequisites
 * @var array<int, \App\Models\SkillLevel> $levels
 * @var list<string> $evidenceTypes
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 */
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>Requirements — <?= e($skill->name) ?></h1>
        <p class="muted">Define evidence requirements and prerequisites for each level.</p>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/admin/skills/' . $skill->id . '/edit')) ?>">Edit Skill</a>
        <a class="btn" href="<?= e(url('/admin/skills')) ?>">← Back to List</a>
    </div>
</section>

<div class="card">
    <h2>Prerequisites</h2>
    <?php if ($prerequisites === []): ?>
        <p class="muted">No prerequisites defined.</p>
    <?php else: ?>
        <ul class="result-list">
            <?php foreach ($prerequisites as $pre): ?>
                <li>
                    <?= e($pre['name']) ?>
                    <span class="muted">— <?= e($levels[(int) $pre['minimum_level']]->name ?? 'Level ' . $pre['minimum_level']) ?> or above</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="admin-form skill-prereq-form" method="post"
          action="<?= e(url('/admin/skills/' . $skill->id . '/prerequisites')) ?>">
        <?= csrf_field() ?>
        <div class="filter-row">
            <div class="form-group">
                <label for="prerequisite_skill_id">Add prerequisite</label>
                <select id="prerequisite_skill_id" name="prerequisite_skill_id" required>
                    <option value="">Select skill…</option>
                    <?php foreach ($allSkills as $s): ?>
                        <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="minimum_level">Minimum level</label>
                <select id="minimum_level" name="minimum_level">
                    <?php for ($l = 1; $l <= 5; $l++): ?>
                        <option value="<?= $l ?>"><?= e($levels[$l]->name ?? 'Level ' . $l) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group filter-actions">
                <label>&nbsp;</label>
                <button class="btn btn-primary" type="submit">Add</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2>Level requirements</h2>
    <?php if ($requirements === []): ?>
        <p class="muted">No requirements defined yet.</p>
    <?php else: ?>
        <table class="leaderboard-table admin-table">
            <thead>
                <tr>
                    <th scope="col">Level</th>
                    <th scope="col">Evidence type</th>
                    <th scope="col">Count</th>
                    <th scope="col">Difficulty</th>
                    <th scope="col">Required</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requirements as $req): ?>
                    <tr>
                        <td><?= e($levels[(int) $req['target_level']]->name ?? 'Level ' . $req['target_level']) ?></td>
                        <td><?= e(str_replace('_', ' ', $req['evidence_type'])) ?></td>
                        <td><?= (int) $req['minimum_count'] ?></td>
                        <td><?= e($req['minimum_difficulty'] ?? '—') ?></td>
                        <td><?= !empty($req['is_required']) ? 'Yes' : 'No' ?></td>
                        <td><?= e($req['description'] ?? '—') ?></td>
                        <td>
                            <form method="post" action="<?= e(url('/admin/skills/requirements/' . (int) $req['id'] . '/delete')) ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm" type="submit" onclick="return confirm('Delete this requirement?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>Add requirement</h3>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form class="admin-form" method="post" action="<?= e(url('/admin/skills/' . $skill->id . '/requirements')) ?>">
        <?= csrf_field() ?>
        <div class="filter-row">
            <div class="form-group">
                <label for="target_level">Target level</label>
                <select id="target_level" name="target_level" required>
                    <?php for ($l = 1; $l <= 5; $l++): ?>
                        <option value="<?= $l ?>"><?= e($levels[$l]->name ?? 'Level ' . $l) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="evidence_type">Evidence type</label>
                <select id="evidence_type" name="evidence_type" required>
                    <?php foreach ($evidenceTypes as $type): ?>
                        <option value="<?= e($type) ?>"><?= e(str_replace('_', ' ', $type)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="minimum_count">Minimum count</label>
                <input type="number" id="minimum_count" name="minimum_count" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label for="minimum_difficulty">Min difficulty (challenges)</label>
                <select id="minimum_difficulty" name="minimum_difficulty">
                    <option value="">Any</option>
                    <?php foreach (['easy', 'medium', 'hard', 'expert'] as $d): ?>
                        <option value="<?= e($d) ?>"><?= e(ucfirst($d)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <input type="text" id="description" name="description" maxlength="255"
                   placeholder="Human-readable requirement description">
        </div>
        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="is_required" value="1" checked>
                Required for level
            </label>
        </div>
        <button class="btn btn-primary" type="submit">Add Requirement</button>
    </form>
</div>
