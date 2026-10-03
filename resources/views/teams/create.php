<?php
/** @var list<int> $skillIds */
/** @var list<array{id:int,name:string,slug:string}> $allSkills */
/** @var string $basePath */
$forcedType = 'ctf';
$groupTypes = ['ctf'];
$group = null;
?>
<section class="page-header">
    <h1>Create CTF Team</h1>
    <p class="muted">Form a team for competitions and practice events.</p>
</section>

<div class="card">
    <form method="post" action="<?= e(url('/teams/create')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="group_type" value="ctf">
        <div class="form-group">
            <label for="name">Team name</label>
            <input id="name" name="name" required maxlength="160" value="<?= e((string) old('name', '')) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= e((string) old('description', '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>"<?= old('visibility', 'community') === $v ? ' selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="join_policy">Recruitment</label>
            <select id="join_policy" name="join_policy">
                <?php foreach (['open' => 'Open', 'approval' => 'Tryouts / approval', 'invite_only' => 'Invite only'] as $val => $label): ?>
                    <option value="<?= e($val) ?>"<?= old('join_policy', 'approval') === $val ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="max_members">Roster size</label>
            <input type="number" id="max_members" name="max_members" min="2" max="50" value="<?= e((string) old('max_members', '20')) ?>">
        </div>
        <div class="form-group">
            <label>Category strengths</label>
            <div class="skill-checkbox-grid">
                <?php foreach ($allSkills as $sk): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                            <?= in_array((int) $sk['id'], $skillIds, true) ? 'checked' : '' ?>>
                        <?= e($sk['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Create Team</button>
    </form>
</div>
