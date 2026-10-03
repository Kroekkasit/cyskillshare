<?php
/** @var list<array<string,mixed>> $items */
use App\Core\Auth;
$userId = Auth::id();
?>
<section class="page-header">
    <div>
        <h1>My Mentorships</h1>
        <p class="muted">Track active and pending mentorship relationships.</p>
    </div>
    <a class="btn" href="<?= e(url('/mentors')) ?>">Find mentors</a>
</section>

<?php if ($items === []): ?>
<div class="card">
    <p class="muted">You have no mentorships yet.</p>
    <a class="btn btn-primary" href="<?= e(url('/mentors')) ?>">Browse mentors</a>
</div>
<?php else: ?>
<div class="mentorship-dashboard">
    <?php foreach ($items as $ms): ?>
        <?php
        $isMentee = (int) $ms['mentee_id'] === $userId;
        $partner = $isMentee ? ($ms['mentor_username'] ?? '') : ($ms['mentee_username'] ?? '');
        $roleLabel = $isMentee ? 'Mentor' : 'Mentee';
        ?>
        <article class="card mentorship-card">
            <header>
                <h3>
                    <a href="<?= e(url('/mentorship/' . (int) $ms['id'])) ?>">
                        <?= e($roleLabel) ?>: @<?= e((string) $partner) ?>
                    </a>
                </h3>
                <span class="pill status-<?= e((string) $ms['status']) ?>"><?= e(ucfirst((string) $ms['status'])) ?></span>
            </header>
            <?php if (!empty($ms['learning_goal'])): ?>
                <p class="muted"><?= e(mb_strimwidth((string) $ms['learning_goal'], 0, 100, '…')) ?></p>
            <?php endif; ?>
            <a class="btn btn-sm" href="<?= e(url('/mentorship/' . (int) $ms['id'])) ?>">Open dashboard</a>
        </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
