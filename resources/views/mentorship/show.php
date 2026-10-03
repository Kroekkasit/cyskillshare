<?php
/**
 * @var array<string,mixed> $mentorship
 * @var array<string,mixed>|null $mentor
 * @var array<string,mixed>|null $mentor_user
 * @var array<string,mixed>|null $mentee
 * @var list<array<string,mixed>> $goals
 * @var list<array<string,mixed>> $sessions
 * @var list<array<string,mixed>> $skills
 * @var bool $isMentor
 */
$id = (int) $mentorship['id'];
$status = (string) $mentorship['status'];
?>
<section class="page-header">
    <div>
        <h1>Mentorship Dashboard</h1>
        <p class="muted">
            @<?= e((string) ($mentor_user['username'] ?? '')) ?>
            ↔ @<?= e((string) ($mentee['username'] ?? '')) ?>
            · <span class="pill"><?= e(ucfirst($status)) ?></span>
        </p>
    </div>
    <a class="btn" href="<?= e(url('/mentorship')) ?>">All mentorships</a>
</section>

<?php if ($status === 'pending'): ?>
<div class="card">
    <?php if ($isMentor): ?>
        <p>Respond to this mentorship request:</p>
        <form method="post" action="<?= e(url('/mentorship/' . $id . '/respond')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <input type="hidden" name="decision" value="accepted">
            <button class="btn btn-primary" type="submit">Accept</button>
        </form>
        <form method="post" action="<?= e(url('/mentorship/' . $id . '/respond')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <input type="hidden" name="decision" value="declined">
            <button class="btn" type="submit">Decline</button>
        </form>
    <?php else: ?>
        <p class="muted">Waiting for mentor response.</p>
        <form method="post" action="<?= e(url('/mentorship/' . $id . '/respond')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="decision" value="cancelled">
            <button class="btn" type="submit">Cancel request</button>
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($mentorship['learning_goal'])): ?>
<div class="card">
    <h2>Learning goal</h2>
    <p><?= nl2br(e((string) $mentorship['learning_goal'])) ?></p>
</div>
<?php endif; ?>

<?php if ($skills !== []): ?>
<div class="card">
    <h2>Focus skills</h2>
    <div class="tag-row">
        <?php foreach ($skills as $sk): ?>
            <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="mentorship-dashboard-grid">
    <div class="card">
        <h2>Goals</h2>
        <?php if ($goals === []): ?>
            <p class="muted">No goals yet.</p>
        <?php else: ?>
            <ul class="result-list">
                <?php foreach ($goals as $g): ?>
                    <li>
                        <strong><?= e((string) $g['title']) ?></strong>
                        <span class="pill"><?= e((string) $g['status']) ?></span>
                        <?php if (!empty($g['skill_name'])): ?>
                            <span class="muted"> · <?= e((string) $g['skill_name']) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (in_array($status, ['active', 'accepted'], true)): ?>
        <form method="post" action="<?= e(url('/mentorship/' . $id . '/goals')) ?>" class="mentorship-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="goal_title">New goal</label>
                <input id="goal_title" name="title" required maxlength="200">
            </div>
            <div class="form-group">
                <label for="goal_description">Description</label>
                <textarea id="goal_description" name="description" rows="2"></textarea>
            </div>
            <button class="btn btn-sm" type="submit">Add goal</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Sessions</h2>
        <?php if ($sessions === []): ?>
            <p class="muted">No sessions scheduled.</p>
        <?php else: ?>
            <ul class="result-list session-list">
                <?php foreach ($sessions as $s): ?>
                    <li>
                        <strong><?= e((string) $s['title']) ?></strong>
                        <span class="muted"><?= e((string) $s['scheduled_at']) ?></span>
                        <?php if (!empty($s['meeting_link'])): ?>
                            <br><a href="<?= e((string) $s['meeting_link']) ?>" rel="noopener noreferrer" target="_blank">Meeting link</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (in_array($status, ['active', 'accepted'], true)): ?>
        <form method="post" action="<?= e(url('/mentorship/' . $id . '/sessions')) ?>" class="mentorship-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="session_title">Title</label>
                <input id="session_title" name="title" required maxlength="200">
            </div>
            <div class="form-group">
                <label for="scheduled_at">When</label>
                <input type="datetime-local" id="scheduled_at" name="scheduled_at" required>
            </div>
            <div class="form-group">
                <label for="duration_minutes">Duration (minutes)</label>
                <input type="number" id="duration_minutes" name="duration_minutes" min="15" max="180" value="45">
            </div>
            <div class="form-group">
                <label for="meeting_link">Meeting link (HTTPS)</label>
                <input type="url" id="meeting_link" name="meeting_link" placeholder="https://…">
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="2"></textarea>
            </div>
            <button class="btn btn-sm" type="submit">Schedule session</button>
        </form>
        <?php endif; ?>
    </div>
</div>
