<?php
/**
 * @var array<string, mixed> $lab
 * @var list<int> $skillIds
 * @var list<array<string, mixed>> $categories
 * @var list<array<string, mixed>> $templates
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var array{starts:int,completions:int}|null $analytics
 * @var bool $isEdit
 */
$l = $lab;
$id = $isEdit ? (int) ($l['id'] ?? 0) : 0;
$action = $isEdit ? url('/admin/labs/' . $id . '/edit') : url('/admin/labs/create');
?>
<section class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Lab' : 'New Lab' ?></h1>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/admin/labs')) ?>">← Back</a>
        <?php if ($isEdit): ?>
            <a class="btn" href="<?= e(url('/admin/labs/' . $id . '/tasks')) ?>">Manage Tasks</a>
        <?php endif; ?>
    </div>
</section>

<?php if ($analytics !== null): ?>
<div class="card lab-admin-analytics">
    <h2>Analytics</h2>
    <div class="arena-stats">
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $analytics['starts'] ?></span>
            <span class="arena-stat-label">Starts</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $analytics['completions'] ?></span>
            <span class="arena-stat-label">Completions</span>
        </div>
    </div>
    <?php if ($isEdit): ?>
        <div class="hero-actions">
            <form method="post" action="<?= e(url('/admin/labs/' . $id . '/publish')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn btn-primary" type="submit">Publish</button>
            </form>
            <form method="post" action="<?= e(url('/admin/labs/' . $id . '/archive')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn" type="submit">Archive</button>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<form class="card lab-admin-form" method="post" action="<?= e($action) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e((string) ($l['title'] ?? old('title', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" maxlength="220"
               value="<?= e((string) ($l['slug'] ?? old('slug', ''))) ?>"
               placeholder="auto-generated if empty">
    </div>
    <div class="form-group">
        <label for="short_description">Short Description</label>
        <input id="short_description" name="short_description" type="text" maxlength="500"
               value="<?= e((string) ($l['short_description'] ?? old('short_description', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="description">Description (Markdown) *</label>
        <textarea id="description" name="description" rows="12" required><?= e((string) ($l['description'] ?? old('description', ''))) ?></textarea>
    </div>
    <div class="form-group">
        <label for="learning_objectives">Learning Objectives</label>
        <textarea id="learning_objectives" name="learning_objectives" rows="4"><?= e((string) ($l['learning_objectives'] ?? old('learning_objectives', ''))) ?></textarea>
    </div>

    <div class="filter-row">
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">— None —</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>"<?= (int) ($l['category_id'] ?? 0) === (int) $cat['id'] ? ' selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="template_id">Template</label>
            <select id="template_id" name="template_id">
                <option value="">— Default —</option>
                <?php foreach ($templates as $tpl): ?>
                    <option value="<?= (int) $tpl['id'] ?>"<?= (int) ($l['template_id'] ?? 0) === (int) $tpl['id'] ? ' selected' : '' ?>>
                        <?= e((string) $tpl['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <?php foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>"<?= ($l['difficulty'] ?? 'beginner') === $d ? ' selected' : '' ?>><?= e(ucfirst($d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['draft', 'review', 'published', 'archived'] as $s): ?>
                    <option value="<?= e($s) ?>"<?= ($l['status'] ?? 'draft') === $s ? ' selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>"<?= ($l['visibility'] ?? 'public') === $v ? ' selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="filter-row">
        <div class="form-group">
            <label for="estimated_minutes">Estimated Minutes</label>
            <input id="estimated_minutes" name="estimated_minutes" type="number" min="5" max="600"
                   value="<?= (int) ($l['estimated_minutes'] ?? 45) ?>">
        </div>
        <div class="form-group">
            <label for="lifetime_minutes">Session Lifetime (min)</label>
            <input id="lifetime_minutes" name="lifetime_minutes" type="number" min="15" max="180"
                   value="<?= (int) ($l['lifetime_minutes'] ?? 60) ?>">
        </div>
        <div class="form-group">
            <label for="max_points">Max Points</label>
            <input id="max_points" name="max_points" type="number" min="0" max="1000"
                   value="<?= (int) ($l['max_points'] ?? 100) ?>">
        </div>
        <div class="form-group">
            <label for="environment_type">Environment Type</label>
            <input id="environment_type" name="environment_type" type="text" maxlength="60"
                   value="<?= e((string) ($l['environment_type'] ?? 'browser')) ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="prerequisite_mode">Prerequisite Mode</label>
        <select id="prerequisite_mode" name="prerequisite_mode">
            <option value="recommended"<?= ($l['prerequisite_mode'] ?? 'recommended') === 'recommended' ? ' selected' : '' ?>>Recommended</option>
            <option value="required"<?= ($l['prerequisite_mode'] ?? '') === 'required' ? ' selected' : '' ?>>Required</option>
        </select>
    </div>

    <div class="form-group">
        <label>Linked Skills</label>
        <div class="checkbox-grid">
            <?php foreach ($allSkills as $sk): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                        <?= in_array((int) $sk['id'], $skillIds, true) ? ' checked' : '' ?>>
                    <?= e((string) $sk['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <label class="checkbox-label">
        <input type="checkbox" name="allow_pause" value="1"<?= !empty($l['allow_pause']) ? ' checked' : '' ?>>
        Allow pause
    </label>

    <div class="hero-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Lab' ?></button>
    </div>
</form>
