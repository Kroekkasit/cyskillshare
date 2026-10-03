<?php
/**
 * @var \App\Models\User $profile
 * @var list<string> $roles
 * @var bool $isOwner
 */
?>
<section class="hero">
    <h1>@<?= e($profile->username) ?></h1>
    <p class="subtitle"><?= e($profile->full_name ?? 'CySkillShare member') ?></p>
</section>

<div class="card">
    <p><strong>Program:</strong> <?= e($profile->program ?? '—') ?></p>
    <p><strong>Year:</strong> <?= e((string) ($profile->year_level ?? '—')) ?></p>
    <p><strong>Roles:</strong> <?= e(implode(', ', $roles)) ?></p>
    <p><strong>Status:</strong> <?= e($profile->status) ?></p>
    <p><strong>Bio:</strong></p>
    <p><?= nl2br(e($profile->bio ?? 'No bio yet.')) ?></p>
    <?php if ($isOwner): ?>
        <p class="muted">This is your profile. Editing UI comes in a later phase.</p>
    <?php endif; ?>
</div>
