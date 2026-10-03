<?php
/**
 * @var \App\Models\ArenaEvent|null $event
 * @var list<array<string, mixed>> $challenges
 * @var list<int> $selectedIds
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>New Event</h1>
        <p class="muted">Create a Cyber Arena event and assign challenges.</p>
    </div>
    <a class="btn" href="<?= e(url('/arena/admin/events')) ?>">← Back to Events</a>
</section>

<?php \App\Core\View::partial('components/form-errors'); ?>

<form class="card admin-form" method="post" action="<?= e(url('/arena/admin/events')) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required maxlength="200"
               value="<?= e(old('name', '')) ?>">
    </div>

    <div class="form-group">
        <label for="description">Description (Markdown)</label>
        <textarea id="description" name="description" rows="8" required maxlength="10000"><?= e(old('description', '')) ?></textarea>
    </div>

    <div class="form-group">
        <label for="event_type">Event Type</label>
        <select id="event_type" name="event_type">
            <?php foreach (['practice', 'ctf', 'competition', 'workshop'] as $t): ?>
                <option value="<?= e($t) ?>"<?= old('event_type', 'practice') === $t ? ' selected' : '' ?>>
                    <?= e(ucfirst($t)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach (['draft', 'upcoming', 'active', 'ended', 'archived'] as $s): ?>
                <option value="<?= e($s) ?>"<?= old('status', 'draft') === $s ? ' selected' : '' ?>>
                    <?= e(ucfirst($s)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="visibility">Visibility</label>
        <select id="visibility" name="visibility">
            <option value="public"<?= old('visibility', 'public') === 'public' ? ' selected' : '' ?>>Public</option>
            <option value="private"<?= old('visibility', 'public') === 'private' ? ' selected' : '' ?>>Private</option>
        </select>
    </div>

    <div class="form-group">
        <label for="start_at">Start</label>
        <input type="datetime-local" id="start_at" name="start_at" required
               value="<?= e(old('start_at', '')) ?>">
    </div>

    <div class="form-group">
        <label for="end_at">End</label>
        <input type="datetime-local" id="end_at" name="end_at" required
               value="<?= e(old('end_at', '')) ?>">
    </div>

    <fieldset>
        <legend><h2>Assign Challenges</h2></legend>
        <?php if ($challenges === []): ?>
            <p class="muted">No challenges available. Create challenges first.</p>
        <?php else: ?>
            <div class="challenge-checklist">
                <?php foreach ($challenges as $ch): ?>
                    <label class="checkbox-row">
                        <input type="checkbox" name="challenge_ids[]" value="<?= (int) $ch['id'] ?>"
                            <?= in_array((int) $ch['id'], $selectedIds, true) ? 'checked' : '' ?>>
                        <?= e($ch['title']) ?>
                        <span class="muted">(<?= e(ucfirst($ch['difficulty'] ?? '')) ?>, <?= (int) ($ch['points'] ?? 0) ?> pts)</span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </fieldset>

    <button class="btn btn-primary" type="submit">Create Event</button>
</form>
