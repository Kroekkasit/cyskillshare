<?php
/**
 * @var list<\App\Models\Channel> $channels
 * @var string $preselect
 * @var list<\App\Models\Tag> $knownTags
 */
$preselectId = null;
foreach ($channels as $ch) {
    if ($ch->slug === $preselect) {
        $preselectId = $ch->id;
        break;
    }
}
?>
<section class="page-header">
    <div>
        <h1>New Discussion</h1>
        <p class="muted">Share a question, writeup idea, or technical challenge with the community.</p>
    </div>
</section>

<div class="card">
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/community/new')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="channel_id">Channel</label>
            <select id="channel_id" name="channel_id" required>
                <?php foreach ($channels as $ch): ?>
                    <option value="<?= (int) $ch->id ?>"<?= $preselectId === $ch->id || (string) old('channel_id') === (string) $ch->id ? ' selected' : '' ?>>
                        <?= e($ch->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" required minlength="5" maxlength="200"
                   value="<?= e((string) old('title')) ?>">
        </div>
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="12" required minlength="10" maxlength="20000"
                      placeholder="Supports basic Markdown: **bold**, `code`, ```code blocks```, lists, and links."><?= e((string) old('content')) ?></textarea>
            <p class="muted small">Markdown help: paragraphs, lists (- item), `inline code`, fenced code blocks, [links](https://…)</p>
        </div>
        <div class="form-group">
            <label for="tags">Tags</label>
            <input id="tags" name="tags" type="text" maxlength="200"
                   placeholder="php, sql-injection, xss"
                   value="<?= e((string) old('tags')) ?>">
            <p class="muted small">Comma-separated. Letters, numbers, hyphens. Max 8 tags.</p>
            <?php if ($knownTags !== []): ?>
                <p class="muted small">Examples:
                    <?php foreach (array_slice($knownTags, 0, 8) as $t): ?>
                        <code><?= e($t->slug) ?></code>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>
        </div>
        <button class="btn btn-primary" type="submit">Post Discussion</button>
    </form>
</div>
