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
 */
$w = $writeup;
?>
<section class="page-header">
    <div>
        <h1>New Writeup</h1>
        <p class="muted">Share your technical analysis, CTF solution, or research.</p>
    </div>
</section>

<form class="card writeup-editor-form" method="post" action="<?= e(url('/writeups/create')) ?>">
    <?= csrf_field() ?>

    <div class="editor-toolbar" role="toolbar" aria-label="Editor tools">
        <div class="form-group editor-template-select">
            <label for="template">Template</label>
            <select id="template" name="template_key">
                <option value="">Blank</option>
                <?php foreach ($templates as $key => $body): ?>
                    <option value="<?= e($key) ?>"><?= e(str_replace('_', ' ', ucfirst($key))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-sm" type="button" data-md-insert="## ">Heading</button>
        <button class="btn btn-sm" type="button" data-md-insert="**bold**">Bold</button>
        <button class="btn btn-sm" type="button" data-md-insert="`code`">Code</button>
        <button class="btn btn-sm" type="button" data-md-insert="```\n\n```">Code Block</button>
    </div>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e((string) ($w['title'] ?? old('title', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="slug">URL Slug</label>
        <input id="slug" name="slug" type="text" maxlength="220"
               value="<?= e((string) ($w['slug'] ?? old('slug', ''))) ?>"
               placeholder="auto-generated from title if empty">
    </div>
    <div class="form-group">
        <label for="short_description">Summary</label>
        <input id="short_description" name="short_description" type="text" maxlength="500"
               value="<?= e((string) ($w['short_description'] ?? old('short_description', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="content">Content (Markdown) *</label>
        <textarea id="content" name="content" rows="18" required class="writeup-content-input"><?= e((string) ($w['content'] ?? old('content', ''))) ?></textarea>
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
                <?php foreach (['draft', 'published'] as $s): ?>
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

    <div class="form-group">
        <label for="tags">Tags (comma-separated)</label>
        <input id="tags" name="tags" type="text" value="<?= e(implode(', ', $tagNames)) ?>" placeholder="web, xss, ctf">
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
        <button class="btn btn-primary" type="submit" name="action" value="save">Create Writeup</button>
        <button class="btn" type="submit" name="action" value="preview">Preview</button>
        <a class="btn" href="<?= e(url('/writeups')) ?>">Cancel</a>
    </div>
</form>

<?php if ($preview !== null): ?>
<section class="card writeup-preview">
    <h2>Preview</h2>
    <div class="writeup-layout">
        <?php \App\Core\View::partial('partials/toc', ['toc' => $preview['toc']]); ?>
        <div class="writeup-body content-body markdown-body"><?= $preview['html'] ?></div>
    </div>
</section>
<?php endif; ?>

<script type="application/json" id="writeup-templates"><?= json_encode($templates, JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
