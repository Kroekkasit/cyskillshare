<?php
/**
 * @var array<string, mixed> $article
 * @var bool $isEdit
 * @var list<int> $skillIds
 * @var list<array<string, mixed>> $categories
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var array{html:string,toc:list<array{level:int,id:string,text:string}>}|null $preview
 * @var bool $canManage
 */
$a = $article;
$id = $isEdit ? (int) $a['id'] : 0;
?>
<section class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Article' : 'New Article' ?></h1>
        <?php if ($isEdit): ?>
            <p class="muted">Status: <span class="pill"><?= e((string) ($a['status'] ?? 'draft')) ?></span></p>
        <?php endif; ?>
    </div>
</section>

<form class="card writeup-editor-form" method="post"
      action="<?= e(url($isEdit ? '/knowledge/edit/' . $id : '/knowledge/create')) ?>">
    <?= csrf_field() ?>

    <div class="editor-toolbar" role="toolbar" aria-label="Editor tools">
        <button class="btn btn-sm" type="button" data-md-insert="## ">Heading</button>
        <button class="btn btn-sm" type="button" data-md-insert="**bold**">Bold</button>
        <button class="btn btn-sm" type="button" data-md-insert="`code`">Code</button>
        <button class="btn btn-sm" type="button" data-md-insert="```\n\n```">Code Block</button>
    </div>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e((string) ($a['title'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="slug">URL Slug</label>
        <input id="slug" name="slug" type="text" maxlength="220"
               value="<?= e((string) ($a['slug'] ?? '')) ?>"
               placeholder="auto-generated from title if empty">
    </div>
    <div class="form-group">
        <label for="summary">Summary</label>
        <input id="summary" name="summary" type="text" maxlength="500"
               value="<?= e((string) ($a['summary'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="content">Content (Markdown) *</label>
        <textarea id="content" name="content" rows="18" required class="writeup-content-input"><?= e((string) ($a['content'] ?? '')) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">None</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (string) ($a['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <?php foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>" <?= ($a['difficulty'] ?? 'beginner') === $d ? 'selected' : '' ?>><?= e(ucfirst($d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>" <?= ($a['visibility'] ?? 'public') === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($canManage): ?>
    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_official" value="1" <?= !empty($a['is_official']) ? 'checked' : '' ?>>
            Mark as official CySkillShare article
        </label>
    </div>
    <?php endif; ?>

    <?php if ($isEdit): ?>
    <div class="form-group">
        <label for="change_summary">Change Summary</label>
        <input id="change_summary" name="change_summary" type="text" maxlength="500"
               placeholder="Brief note about this edit">
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label>Related Skills</label>
        <div class="checkbox-grid">
            <?php foreach ($allSkills as $sk): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                        <?= in_array((int) $sk['id'], $skillIds, true) ? 'checked' : '' ?>>
                    <?= e($sk['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="hero-actions">
        <button class="btn btn-primary" type="submit" name="action" value="save"><?= $isEdit ? 'Save Changes' : 'Create Draft' ?></button>
        <button class="btn" type="submit" name="action" value="preview">Preview</button>
        <a class="btn" href="<?= e(url('/knowledge')) ?>">Cancel</a>
    </div>
</form>

<?php if ($isEdit && ($a['status'] ?? '') === 'draft'): ?>
<form method="post" action="<?= e(url('/knowledge/' . $id . '/submit-review')) ?>" class="card-inline-actions">
    <?= csrf_field() ?>
    <button class="btn btn-primary" type="submit">Submit for Review</button>
</form>
<?php endif; ?>

<?php if ($preview !== null): ?>
<section class="card writeup-preview">
    <h2>Preview</h2>
    <div class="writeup-layout">
        <?php \App\Core\View::partial('partials/toc', ['toc' => $preview['toc']]); ?>
        <div class="writeup-body content-body markdown-body"><?= $preview['html'] ?></div>
    </div>
</section>
<?php endif; ?>
