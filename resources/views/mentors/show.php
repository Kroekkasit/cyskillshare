<?php
/**
 * @var array<string,mixed> $user
 * @var array<string,mixed> $mentor
 * @var list<array<string,mixed>> $skills
 * @var int $active_mentees
 * @var int $slots_left
 */
use App\Core\Auth;
$mentorId = (int) $mentor['id'];
?>
<section class="page-header">
    <div>
        <h1>@<?= e((string) $user['username']) ?></h1>
        <p class="subtitle">Mentor profile</p>
        <?php if (($mentor['verification_status'] ?? '') === 'verified'): ?>
            <span class="pill verified">✓ Verified mentor</span>
        <?php endif; ?>
    </div>
    <a class="btn" href="<?= e(url('/profile/' . $user['username'])) ?>">Full profile</a>
</section>

<div class="card">
    <?php if (!empty($mentor['bio'])): ?>
        <p><?= nl2br(e((string) $mentor['bio'])) ?></p>
    <?php elseif (!empty($user['bio'])): ?>
        <p><?= nl2br(e((string) $user['bio'])) ?></p>
    <?php else: ?>
        <p class="muted">No bio provided.</p>
    <?php endif; ?>

    <dl class="mentor-meta">
        <?php if (!empty($mentor['preferred_frequency'])): ?>
            <dt>Frequency</dt><dd><?= e((string) $mentor['preferred_frequency']) ?></dd>
        <?php endif; ?>
        <?php if (!empty($mentor['preferred_session_length'])): ?>
            <dt>Session length</dt><dd><?= e((string) $mentor['preferred_session_length']) ?></dd>
        <?php endif; ?>
        <?php if (!empty($mentor['languages'])): ?>
            <dt>Languages</dt><dd><?= e((string) $mentor['languages']) ?></dd>
        <?php endif; ?>
    </dl>

    <p class="muted"><?= (int) $active_mentees ?> active · <?= (int) $slots_left ?> slots available</p>
</div>

<?php if ($skills !== []): ?>
<div class="card">
    <h2>Mentorship skills</h2>
    <div class="tag-row">
        <?php foreach ($skills as $sk): ?>
            <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>">
                <?= e((string) $sk['name']) ?> (L<?= (int) $sk['level'] ?>)
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (Auth::check() && (int) Auth::id() !== (int) $user['id'] && (int) $mentor['accepting_requests'] && $slots_left > 0): ?>
<div class="card">
    <h2>Request mentorship</h2>
    <form method="post" action="<?= e(url('/mentorship/request')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="mentor_id" value="<?= $mentorId ?>">
        <input type="hidden" name="redirect" value="<?= e(url('/mentors/' . $user['username'])) ?>">
        <div class="form-group">
            <label for="learning_goal">Learning goal</label>
            <textarea id="learning_goal" name="learning_goal" rows="3" required maxlength="2000"></textarea>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="3" maxlength="2000"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Send request</button>
    </form>
</div>
<?php elseif (!Auth::check()): ?>
<div class="card"><a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login to request mentorship</a></div>
<?php endif; ?>
