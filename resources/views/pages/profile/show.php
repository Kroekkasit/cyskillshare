<?php
/**
 * @var \App\Models\User $profile
 * @var list<string> $roles
 * @var bool $isOwner
 * @var int $discussionCount
 * @var int $replyCount
 * @var int $bestAnswerCount
 * @var list<array<string, mixed>> $recentDiscussions
 * @var bool $canViewSkills
 * @var list<array<string, mixed>> $topSkills
 * @var string $skillsVisibility
 */
use App\Services\SkillTreeService;
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

<?php if ($canViewSkills && $topSkills !== []): ?>
<div class="card skill-profile-section">
    <div class="skill-profile-head">
        <h2>Top Skills</h2>
        <a class="btn btn-sm" href="<?= e(url('/skills')) ?>">View skill tree</a>
    </div>
    <ul class="skill-profile-list">
        <?php foreach ($topSkills as $us): ?>
            <li class="skill-profile-item">
                <a href="<?= e(url('/skills/' . $us['slug'])) ?>">
                    <span class="skill-level-symbol"><?= e(SkillTreeService::levelSymbol((int) $us['current_level'])) ?></span>
                    <?= e($us['name']) ?>
                </a>
                <span class="muted"><?= e($us['level_name'] ?? 'Not started') ?></span>
                <?php \App\Core\View::partial('skills/partials/progress-bar', [
                    'progress' => (int) $us['progress_score'],
                    'label' => '',
                    'class' => 'skill-progress skill-progress-compact',
                ]); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php elseif (!$canViewSkills && !$isOwner): ?>
<div class="card">
    <p class="muted">Skills are private on this profile.</p>
</div>
<?php endif; ?>

<?php if ($isOwner): ?>
<div class="card">
    <h2>Skills visibility</h2>
    <p class="muted">Control who can see your skill progress on this profile.</p>
    <form method="post" action="<?= e(url('/skills/privacy')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= e(url('/profile/' . $profile->username)) ?>">
        <div class="form-group">
            <label for="visibility">Who can see your skills</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public' => 'Everyone', 'community' => 'Logged-in members', 'private' => 'Only me'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $skillsVisibility === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Save visibility</button>
    </form>
</div>
<?php endif; ?>

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
