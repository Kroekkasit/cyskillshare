<?php
/** @var list<array<string, mixed>> $channels */
$grouped = [];
foreach ($channels as $ch) {
    $cat = (string) $ch['category_name'];
    $grouped[$cat][] = $ch;
}
?>
<section class="hero-banner">
    <h1>Community</h1>
    <p class="subtitle">Channels for discussion, help, and
        <span class="accent-italic">cybersecurity practice</span>.</p>
    <div class="hero-actions">
        <?php if (\App\Core\Auth::check()): ?>
            <a class="btn btn-primary" href="#create-thread">+ New thread</a>
        <?php else: ?>
            <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login to post</a>
        <?php endif; ?>
    </div>
</section>

<?php if (\App\Core\Auth::check()): ?>
<div class="card" id="create-thread">
    <h2>Create thread</h2>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/thread/create')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="channel_id">Channel</label>
            <select id="channel_id" name="channel_id" required>
                <?php foreach ($channels as $ch): ?>
                    <option value="<?= (int) $ch['id'] ?>"><?= e((string) $ch['name']) ?></option>
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
            <textarea id="content" name="content" rows="5" required minlength="10"><?= e((string) old('content')) ?></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Post thread</button>
    </form>
</div>
<?php endif; ?>

<?php foreach ($grouped as $categoryName => $items): ?>
    <div class="card">
        <h2><?= e($categoryName) ?></h2>
        <div class="channel-grid">
            <?php foreach ($items as $ch): ?>
                <a class="channel-chip" href="<?= e(url('/community/' . $ch['slug'])) ?>">
                    <strong><?= e((string) $ch['name']) ?></strong>
                    <span><?= e((string) ($ch['description'] ?? '')) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
