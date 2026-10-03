<?php
/**
 * @var \App\Models\User $profile
 * @var list<string> $roles
 * @var bool $isOwner
 * @var int $discussionCount
 * @var int $replyCount
 * @var int $bestAnswerCount
 * @var list<array<string, mixed>> $recentDiscussions
 */
?>
<section class="profile-hero card">
    <div class="avatar lg"><?= e(strtoupper(substr($profile->username, 0, 1))) ?></div>
    <div>
        <h1>@<?= e($profile->username) ?></h1>
        <p class="subtitle"><?= e($profile->full_name ?? $profile->primaryRoleLabel()) ?></p>
        <p class="muted">
            <?= e($profile->program ?? 'College of Computing') ?>
            <?php if ($profile->year_level): ?> · Year <?= (int) $profile->year_level ?><?php endif; ?>
            · Khon Kaen University
        </p>
        <div class="tag-row">
            <?php foreach ($roles as $role): ?>
                <span class="pill"><?= e(ucfirst($role)) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="stats-grid">
    <div class="stat-card"><strong><?= (int) $discussionCount ?></strong><span>Discussions</span></div>
    <div class="stat-card"><strong><?= (int) $replyCount ?></strong><span>Replies</span></div>
    <div class="stat-card"><strong><?= (int) $bestAnswerCount ?></strong><span>Best Answers</span></div>
</div>

<div class="card">
    <h2>Bio</h2>
    <?php if ($isOwner): ?>
        <?php \App\Core\View::partial('components/form-errors'); ?>
        <form method="post" action="<?= e(url('/profile/' . $profile->username)) ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="full_name">Full name</label>
                <input id="full_name" name="full_name" value="<?= e((string) old('full_name', $profile->full_name ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4" maxlength="2000"><?= e((string) old('bio', $profile->bio ?? '')) ?></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Save profile</button>
        </form>
    <?php else: ?>
        <p><?= nl2br(e($profile->bio ?? 'No bio yet.')) ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Recent Discussions</h2>
    <?php if ($recentDiscussions === []): ?>
        <p class="muted">No discussions yet.</p>
    <?php else: ?>
        <ul class="result-list">
            <?php foreach ($recentDiscussions as $t): ?>
                <li>
                    <a href="<?= e(url('/thread/' . $t['id'])) ?>"><?= e((string) $t['title']) ?></a>
                    <div class="muted small"><?= e((string) $t['channel_name']) ?> · <?= e(time_ago((string) $t['created_at'])) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
