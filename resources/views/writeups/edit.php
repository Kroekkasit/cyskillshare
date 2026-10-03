<?php
/**
 * @var array<string, mixed> $writeup
 * @var list<int> $skillIds
 * @var list<string> $tagNames
 * @var list<int> $challengeIds
 * @var list<array<string, mixed>> $categories
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var array<string, string> $templates
 * @var array{html:string,toc:list<array{level:int,id:string,text:string}>}|null $preview
 * @var array{done:list<string>,missing:list<string>}|null $suggestions
 */
use App\Core\Auth;

$w = $writeup;
$id = (int) $w['id'];
?>
<section class="page-header">
    <div>
        <h1>Edit Writeup</h1>
        <p class="muted">
            Status: <span class="pill"><?= e((string) ($w['status'] ?? 'draft')) ?></span>
            <?php if (($w['status'] ?? '') === 'published'): ?>
                · <a href="<?= e(url('/writeups/' . Auth::user()?->username . '/' . $w['slug'])) ?>">View published</a>
            <?php endif; ?>
        </p>
    </div>
</section>

<form class="card writeup-editor-form" method="post" action="<?= e(url('/writeups/edit/' . $id)) ?>">
    <?= csrf_field() ?>

    <div class="editor-toolbar" role="toolbar" aria-label="Editor tools">
        <button class="btn btn-sm" type="button" data-md-insert="## ">Heading</button>
        <button class="btn btn-sm" type="button" data-md-insert="**bold**">Bold</button>
        <button class="btn btn-sm" type="button" data-md-insert="`code`">Code</button>
        <button class="btn btn-sm" type="button" data-md-insert="```\n\n```">Code Block</button>
        <span class="autosave-status muted small" id="autosave-status"></span>
    </div>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e((string) ($w['title'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="slug">URL Slug</label>
        <input id="slug" name="slug" type="text" maxlength="220" value="<?= e((string) ($w['slug'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="short_description">Summary</label>
        <input id="short_description" name="short_description" type="text" maxlength="500"
               value="<?= e((string) ($w['short_description'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="content">Content (Markdown) *</label>
        <textarea id="content" name="content" rows="18" required class="writeup-content-input"><?= e((string) ($w['content'] ?? '')) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">None</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (string) ($w['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>>
                        <?= e((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <?php foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>" <?= ($w['difficulty'] ?? 'beginner') === $d ? 'selected' : '' ?>><?= e(ucfirst($d)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= ($w['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>" <?= ($w['visibility'] ?? 'public') === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if (($w['status'] ?? '') === 'published'): ?>
    <div class="form-group">
        <label for="change_summary">Change Summary</label>
        <input id="change_summary" name="change_summary" type="text" maxlength="500"
               placeholder="Brief note about this edit">
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="tags">Tags (comma-separated)</label>
        <input id="tags" name="tags" type="text" value="<?= e(implode(', ', $tagNames)) ?>">
    </div>

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
        <button class="btn btn-primary" type="submit" name="action" value="save">Save Changes</button>
        <button class="btn" type="submit" name="action" value="preview">Preview</button>
        <a class="btn" href="<?= e(url('/writeups')) ?>">Cancel</a>
    </div>
</form>

<div class="hero-actions card-inline-actions">
    <?php if (($w['status'] ?? '') !== 'published'): ?>
        <form method="post" action="<?= e(url('/writeups/' . $id . '/publish')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <button class="btn btn-primary" type="submit">Publish</button>
        </form>
    <?php endif; ?>
    <?php if (($w['status'] ?? '') !== 'archived'): ?>
        <form method="post" action="<?= e(url('/writeups/' . $id . '/archive')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <button class="btn" type="submit">Archive</button>
        </form>
    <?php endif; ?>
</div>

<?php if ($suggestions !== null): ?>
    <?php \App\Core\View::partial('partials/quality-checklist', ['suggestions' => $suggestions]); ?>
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

<form id="autosave-form" method="post" action="<?= e(url('/writeups/' . $id . '/autosave')) ?>" class="visually-hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="format" value="json">
</form>
