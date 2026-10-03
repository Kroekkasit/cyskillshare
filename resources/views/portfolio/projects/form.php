<?php
/**
 * @var array<string, mixed>|null $project
 * @var list<string> $technologies
 * @var list<int> $skillIds
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var list<string> $projectTypes
 * @var list<array<string, mixed>>|null $images
 */
$isEdit = $project !== null;
$images = $images ?? [];
$p = $project ?? [];
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Project' : 'New Project' ?></h1>
        <p class="muted"><?= $isEdit ? 'Update project details, skills, and media.' : 'Showcase your cybersecurity work.' ?></p>
    </div>
</section>

<form class="card portfolio-project-form" method="post"
      action="<?= e(url($isEdit ? '/projects/edit/' . (int) $p['id'] : '/projects/create')) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200"
               value="<?= e((string) ($p['title'] ?? old('title', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="slug">URL Slug</label>
        <input id="slug" name="slug" type="text" maxlength="220"
               value="<?= e((string) ($p['slug'] ?? old('slug', ''))) ?>"
               placeholder="auto-generated from title if empty">
    </div>
    <div class="form-group">
        <label for="short_description">Short Description</label>
        <input id="short_description" name="short_description" type="text" maxlength="500"
               value="<?= e((string) ($p['short_description'] ?? old('short_description', ''))) ?>">
    </div>
    <div class="form-group">
        <label for="description">Full Description (Markdown)</label>
        <textarea id="description" name="description" rows="10"><?= e((string) ($p['description'] ?? old('description', ''))) ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="project_type">Type</label>
            <select id="project_type" name="project_type">
                <?php foreach ($projectTypes as $t): ?>
                    <option value="<?= e($t) ?>" <?= ($p['project_type'] ?? 'other') === $t ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $t)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['planning', 'in_progress', 'completed', 'archived'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= ($p['status'] ?? 'planning') === $s ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="publish_status">Publish Status</label>
            <select id="publish_status" name="publish_status">
                <?php foreach (['draft', 'published'] as $ps): ?>
                    <option value="<?= e($ps) ?>" <?= ($p['publish_status'] ?? 'draft') === $ps ? 'selected' : '' ?>><?= e(ucfirst($ps)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>" <?= ($p['visibility'] ?? 'public') === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="repository_url">Repository URL</label>
            <input id="repository_url" name="repository_url" type="url"
                   value="<?= e((string) ($p['repository_url'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="demo_url">Demo URL</label>
            <input id="demo_url" name="demo_url" type="url"
                   value="<?= e((string) ($p['demo_url'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="documentation_url">Documentation URL</label>
            <input id="documentation_url" name="documentation_url" type="url"
                   value="<?= e((string) ($p['documentation_url'] ?? '')) ?>">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input id="start_date" name="start_date" type="date" value="<?= e((string) ($p['start_date'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input id="end_date" name="end_date" type="date" value="<?= e((string) ($p['end_date'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input id="display_order" name="display_order" type="number" value="<?= (int) ($p['display_order'] ?? 0) ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="technologies">Technologies (comma-separated)</label>
        <input id="technologies" name="technologies" type="text"
               value="<?= e(implode(', ', $technologies)) ?>"
               placeholder="Python, Docker, Wireshark">
    </div>

    <div class="form-group">
        <label>Related Skills</label>
        <div class="portfolio-skills-select">
            <?php foreach ($allSkills as $sk): ?>
                <label class="portfolio-section-check">
                    <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                        <?= in_array((int) $sk['id'], $skillIds, true) ? 'checked' : '' ?>>
                    <?= e((string) $sk['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Project' ?></button>
</form>

<?php if ($isEdit): ?>
<section class="card portfolio-project-actions">
    <h2>Project Actions</h2>
    <div class="hero-actions">
        <?php if (($p['publish_status'] ?? '') !== 'published'): ?>
            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/publish')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="/projects/edit/<?= (int) $p['id'] ?>">
                <button class="btn btn-primary" type="submit">Publish</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/archive')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="/projects/edit/<?= (int) $p['id'] ?>">
                <button class="btn" type="submit">Archive</button>
            </form>
            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/feature')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="/projects/edit/<?= (int) $p['id'] ?>">
                <input type="hidden" name="featured" value="<?= !empty($p['featured']) ? '0' : '1' ?>">
                <button class="btn" type="submit"><?= !empty($p['featured']) ? 'Unfeature' : 'Feature' ?></button>
            </form>
            <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/verification')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn" type="submit">Request Verification</button>
            </form>
        <?php endif; ?>
        <?php if (($p['publish_status'] ?? '') === 'published'): ?>
            <a class="btn" href="<?= e(url('/projects/' . \App\Core\Auth::user()?->username . '/' . $p['slug'])) ?>">View Public Page</a>
        <?php endif; ?>
    </div>
</section>

<section class="card">
    <h2>Project Images</h2>
    <?php if ($images !== []): ?>
        <div class="project-image-grid project-image-admin">
            <?php foreach ($images as $img): ?>
                <figure class="project-image-item">
                    <img src="<?= e(url('/projects/images/' . (int) $img['id'])) ?>" alt="<?= e((string) $img['original_name']) ?>" loading="lazy">
                    <figcaption>
                        <?= e((string) $img['original_name']) ?>
                        <form method="post" action="<?= e(url('/projects/images/' . (int) $img['id'] . '/delete')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="redirect" value="/projects/edit/<?= (int) $p['id'] ?>">
                            <button class="btn btn-sm" type="submit">Delete</button>
                        </form>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/projects/' . (int) $p['id'] . '/images')) ?>" enctype="multipart/form-data" class="portfolio-upload-form">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="image">Upload Image</label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif" required>
        </div>
        <div class="form-group">
            <label for="caption">Caption</label>
            <input id="caption" name="caption" type="text" maxlength="200">
        </div>
        <button class="btn btn-primary btn-sm" type="submit">Upload</button>
    </form>
</section>
<?php endif; ?>
