<?php
/**
 * @var array<string,mixed>|null $group
 * @var list<int> $skillIds
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var list<string> $groupTypes
 * @var string|null $forcedType
 * @var string $basePath
 */
$forcedType = $forcedType ?? null;
?>
<section class="page-header">
    <h1>Create Group</h1>
</section>

<div class="card">
    <form method="post" action="<?= e(url($basePath . '/create')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" name="name" required maxlength="160" value="<?= e((string) old('name', '')) ?>">
        </div>
        <div class="form-group">
            <label for="slug">Slug (optional)</label>
            <input id="slug" name="slug" maxlength="180" value="<?= e((string) old('slug', '')) ?>" placeholder="auto-generated from name">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= e((string) old('description', '')) ?></textarea>
        </div>
        <?php if ($forcedType === null): ?>
        <div class="form-group">
            <label for="group_type">Type</label>
            <select id="group_type" name="group_type">
                <?php foreach ($groupTypes as $t): ?>
                    <option value="<?= e($t) ?>"<?= old('group_type', 'study') === $t ? ' selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
            <input type="hidden" name="group_type" value="<?= e($forcedType) ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public' => 'Public', 'community' => 'Community members', 'private' => 'Private'] as $val => $label): ?>
                    <option value="<?= e($val) ?>"<?= old('visibility', 'community') === $val ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="join_policy">Join policy</label>
            <select id="join_policy" name="join_policy">
                <?php foreach (['open' => 'Open', 'approval' => 'Approval required', 'invite_only' => 'Invite only'] as $val => $label): ?>
                    <option value="<?= e($val) ?>"<?= old('join_policy', 'approval') === $val ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="max_members">Max members</label>
            <input type="number" id="max_members" name="max_members" min="2" max="50" value="<?= e((string) old('max_members', '20')) ?>">
        </div>
        <div class="form-group">
            <label>Related skills</label>
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
        <button class="btn btn-primary" type="submit">Create Group</button>
    </form>
</div>
