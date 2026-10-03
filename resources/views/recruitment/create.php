<?php
/**
 * @var list<array{id:int,name:string,slug:string}> $allSkills
 * @var list<array<string,mixed>> $myGroups
 * @var list<string> $lookingFor
 */
?>
<section class="page-header">
    <h1>Post Recruitment</h1>
    <p class="muted">Tell the community who you are looking for.</p>
</section>

<div class="card">
    <form method="post" action="<?= e(url('/recruitment/create')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" name="title" required maxlength="200" value="<?= e((string) old('title', '')) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= e((string) old('description', '')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="looking_for">Looking for</label>
            <select id="looking_for" name="looking_for">
                <?php foreach ($lookingFor as $lf): ?>
                    <option value="<?= e($lf) ?>"<?= old('looking_for', 'general') === $lf ? ' selected' : '' ?>><?= e(ucfirst($lf)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($myGroups !== []): ?>
        <div class="form-group">
            <label for="group_id">Link to group (optional)</label>
            <select id="group_id" name="group_id">
                <option value="">None</option>
                <?php foreach ($myGroups as $g): ?>
                    <option value="<?= (int) $g['id'] ?>"><?= e((string) $g['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Required skills</label>
            <div class="skill-checkbox-grid">
                <?php foreach ($allSkills as $sk): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>">
                        <?= e($sk['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Publish post</button>
    </form>
</div>
