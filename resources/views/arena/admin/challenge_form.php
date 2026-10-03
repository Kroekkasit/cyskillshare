<?php
/**
 * @var \App\Models\Challenge|null $challenge
 * @var list<\App\Models\ChallengeCategory> $categories
 * @var list<\App\Models\ChallengeHint> $hints
 * @var list<\App\Models\ChallengeFile> $files
 * @var int $solveCount
 * @var list<array{id:int,name:string,slug:string}> $tags
 * @var string|null $tagString
 */
$isEdit = $challenge !== null;
$tagString = $tagString ?? old('tags', $isEdit ? implode(', ', array_map(static fn(array $t): string => $t['name'], $tags)) : '');
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Challenge' : 'New Challenge' ?></h1>
        <p class="muted"><?= $isEdit ? 'Update challenge content, scoring, and publishing settings.' : 'Create a new Cyber Arena challenge.' ?></p>
    </div>
    <a class="btn" href="<?= e(url('/arena/admin/challenges')) ?>">← Back to List</a>
</section>

<?php \App\Core\View::partial('components/form-errors'); ?>

<form class="card admin-form" method="post"
      action="<?= e(url($isEdit ? '/arena/admin/challenges/' . $challenge->id : '/arena/admin/challenges')) ?>">
    <?= csrf_field() ?>

    <fieldset>
        <legend><h2>Basic</h2></legend>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required maxlength="200"
                   value="<?= e(old('title', $challenge?->title ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat->id ?>"
                        <?= (int) old('category_id', $challenge?->category_id ?? 0) === $cat->id ? 'selected' : '' ?>>
                        <?= e($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty" required>
                <?php foreach (['easy', 'medium', 'hard', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>"
                        <?= old('difficulty', $challenge?->difficulty ?? 'easy') === $d ? 'selected' : '' ?>>
                        <?= e(ucfirst($d)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Content</h2></legend>
        <div class="form-group">
            <label for="description">Description (Markdown)</label>
            <textarea id="description" name="description" rows="12" required maxlength="50000"><?= e(old('description', $challenge?->description ?? '')) ?></textarea>
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Scoring</h2></legend>
        <div class="form-group">
            <label for="points">Points</label>
            <input type="number" id="points" name="points" required min="1" max="10000"
                   value="<?= (int) old('points', $challenge?->points ?? 100) ?>">
        </div>
        <?php if ($isEdit && $solveCount > 0): ?>
            <div class="flash flash-error">
                This challenge has <?= (int) $solveCount ?> solve(s). Changing points or flag requires confirmation.
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="confirm_scoring" value="1">
                    I confirm changing flag/points despite existing solves
                </label>
            </div>
        <?php endif; ?>
    </fieldset>

    <fieldset>
        <legend><h2>Flag</h2></legend>
        <div class="form-group">
            <label for="flag">Flag<?= $isEdit ? ' (leave blank to keep current)' : '' ?></label>
            <input type="text" id="flag" name="flag" maxlength="500" autocomplete="off"
                   <?= $isEdit ? '' : 'required' ?>
                   placeholder="CySkillShare{...}">
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="case_sensitive" value="1"
                    <?= old('case_sensitive', $challenge?->case_sensitive ?? true) ? 'checked' : '' ?>>
                Case-sensitive flag matching
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Tags</h2></legend>
        <div class="form-group">
            <label for="tags">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" value="<?= e($tagString) ?>" placeholder="web, xss, beginner">
        </div>
    </fieldset>

    <fieldset>
        <legend><h2>Publishing</h2></legend>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                    <?php if (!$isEdit && $s === 'archived') {
                        continue;
                    } ?>
                    <option value="<?= e($s) ?>"
                        <?= old('status', $challenge?->status ?? 'draft') === $s ? 'selected' : '' ?>>
                        <?= e(ucfirst($s)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="featured" value="1"
                    <?= old('featured', $challenge?->is_featured ?? false) ? 'checked' : '' ?>>
                Featured challenge
            </label>
        </div>
    </fieldset>

    <div class="hero-actions">
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Challenge' ?></button>
        <?php if ($isEdit): ?>
            <?php if ($challenge->status !== 'published'): ?>
                <button class="btn" formaction="<?= e(url('/arena/admin/challenges/' . $challenge->id . '/publish')) ?>" formmethod="post">Publish</button>
            <?php endif; ?>
            <?php if ($challenge->status !== 'archived'): ?>
                <button class="btn" formaction="<?= e(url('/arena/admin/challenges/' . $challenge->id . '/archive')) ?>" formmethod="post"
                        onclick="return confirm('Archive this challenge?')">Archive</button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</form>

<?php if ($isEdit): ?>
<section class="arena-section">
    <h2>Hints</h2>
    <?php if ($hints === []): ?>
        <p class="muted">No hints yet.</p>
    <?php else: ?>
        <?php foreach ($hints as $hint): ?>
            <form class="card hint-card admin-hint-form" method="post"
                  action="<?= e(url('/arena/admin/hints/' . $hint->id)) ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Order</label>
                    <input type="number" name="hint_order" min="1" value="<?= (int) $hint->hint_order ?>">
                </div>
                <div class="form-group">
                    <label>Penalty</label>
                    <input type="number" name="point_penalty" min="0" value="<?= (int) $hint->point_penalty ?>">
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="3" required><?= e($hint->content) ?></textarea>
                </div>
                <div class="hero-actions">
                    <button class="btn btn-primary" type="submit">Update Hint</button>
                    <button class="btn" formaction="<?= e(url('/arena/admin/hints/' . $hint->id . '/delete')) ?>"
                            formmethod="post" onclick="return confirm('Delete this hint?')">Delete</button>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>

    <form class="card" method="post" action="<?= e(url('/arena/admin/challenges/' . $challenge->id . '/hints')) ?>">
        <?= csrf_field() ?>
        <h3>Add Hint</h3>
        <div class="form-group">
            <label for="hint_order">Order</label>
            <input type="number" id="hint_order" name="hint_order" min="1" value="<?= count($hints) + 1 ?>">
        </div>
        <div class="form-group">
            <label for="point_penalty">Point Penalty</label>
            <input type="number" id="point_penalty" name="point_penalty" min="0" value="10">
        </div>
        <div class="form-group">
            <label for="hint_content">Content</label>
            <textarea id="hint_content" name="content" rows="3" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Add Hint</button>
    </form>
</section>

<section class="arena-section">
    <h2>Files</h2>
    <?php if ($files === []): ?>
        <p class="muted">No files uploaded.</p>
    <?php else: ?>
        <ul class="file-list card">
            <?php foreach ($files as $file): ?>
                <li>
                    <?= e($file->original_name) ?>
                    <span class="muted">(<?= number_format($file->file_size / 1024, 1) ?> KB)</span>
                    <form method="post" action="<?= e(url('/arena/admin/files/' . $file->id . '/delete')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn" type="submit" onclick="return confirm('Delete this file?')">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="card" method="post" enctype="multipart/form-data"
          action="<?= e(url('/arena/admin/challenges/' . $challenge->id . '/files')) ?>">
        <?= csrf_field() ?>
        <h3>Upload File</h3>
        <div class="form-group">
            <label for="file">Challenge attachment</label>
            <input type="file" id="file" name="file" required>
        </div>
        <button class="btn btn-primary" type="submit">Upload</button>
    </form>
</section>
<?php endif; ?>
