<?php
/**
 * @var \App\Models\Skill|null $skill
 * @var list<\App\Models\SkillCategory> $categories
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 */
$isEdit = $skill !== null;
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Skill' : 'New Skill' ?></h1>
        <p class="muted"><?= $isEdit ? 'Update skill definition and visibility.' : 'Create a new skill in the tree.' ?></p>
    </div>
    <a class="btn" href="<?= e(url('/admin/skills')) ?>">← Back to List</a>
</section>

<?php \App\Core\View::partial('components/form-errors'); ?>

<form class="card admin-form" method="post"
      action="<?= e(url($isEdit ? '/admin/skills/' . $skill->id . '/edit' : '/admin/skills/create')) ?>">
    <?= csrf_field() ?>

    <fieldset>
        <legend><h2>Basic</h2></legend>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required maxlength="150"
                   value="<?= e(old('name', $skill?->name ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat->id ?>"
                        <?= (int) old('category_id', $skill?->category_id ?? 0) === $cat->id ? 'selected' : '' ?>>
                        <?= e($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="parent_skill_id">Parent skill (optional)</label>
            <select id="parent_skill_id" name="parent_skill_id">
                <option value="">None (root skill)</option>
                <?php foreach ($allSkills as $s): ?>
                    <option value="<?= (int) $s['id'] ?>"
                        <?= (int) old('parent_skill_id', $skill?->parent_skill_id ?? 0) === (int) $s['id'] ? 'selected' : '' ?>>
                        <?= e($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="icon">Icon (optional)</label>
            <input type="text" id="icon" name="icon" maxlength="50"
                   value="<?= e(old('icon', $skill?->icon ?? '')) ?>" placeholder="e.g. shield">
        </div>
        <div class="form-group">
            <label for="display_order">Display order</label>
            <input type="number" id="display_order" name="display_order" min="0"
                   value="<?= (int) old('display_order', $skill?->display_order ?? 0) ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Content</h2></legend>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6" required maxlength="5000"><?= e(old('description', $skill?->description ?? '')) ?></textarea>
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Settings</h2></legend>
        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="is_active" value="1"
                    <?= old('is_active', $skill === null || $skill->is_active ? '1' : '') ? 'checked' : '' ?>>
                Active (visible in skill tree)
            </label>
        </div>
        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="is_gated" value="1"
                    <?= old('is_gated', $skill?->is_gated ? '1' : '') ? 'checked' : '' ?>>
                Gated (requires prerequisites)
            </label>
        </div>
    </fieldset>

    <div class="hero-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Skill' ?></button>
        <?php if ($isEdit): ?>
            <a class="btn" href="<?= e(url('/admin/skills/' . $skill->id . '/requirements')) ?>">Manage Requirements</a>
        <?php endif; ?>
    </div>
</form>
