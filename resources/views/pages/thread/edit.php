<?php
/**
 * @var \App\Models\Thread $thread
 * @var list<\App\Models\Channel> $channels
 * @var string $tagString
 */
?>
<section class="page-header">
    <div>
        <h1>Edit Discussion</h1>
        <p class="muted">Update your thread. Changes are logged with an edited timestamp.</p>
    </div>
</section>

<div class="card">
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/thread/' . $thread->id . '/edit')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="channel_id">Channel</label>
            <select id="channel_id" name="channel_id" required>
                <?php foreach ($channels as $ch): ?>
                    <option value="<?= (int) $ch->id ?>"<?= (int) old('channel_id', $thread->channel_id) === $ch->id ? ' selected' : '' ?>>
                        <?= e($ch->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input id="title" name="title" required minlength="5" maxlength="200"
                   value="<?= e((string) old('title', $thread->title)) ?>">
        </div>
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="12" required minlength="10"><?= e((string) old('content', $thread->content)) ?></textarea>
        </div>
        <div class="form-group">
            <label for="tags">Tags</label>
            <input id="tags" name="tags" value="<?= e((string) old('tags', $tagString)) ?>">
        </div>
        <button class="btn btn-primary" type="submit">Save changes</button>
        <a class="btn" href="<?= e(url('/thread/' . $thread->id)) ?>">Cancel</a>
    </form>
</div>
