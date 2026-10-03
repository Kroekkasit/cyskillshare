<?php
/**
 * @var array<string,mixed> $group
 * @var list<array<string,mixed>> $skills
 * @var list<array<string,mixed>> $members
 * @var list<array<string,mixed>> $goals
 * @var list<array<string,mixed>> $activities
 * @var list<array<string,mixed>> $resources
 * @var string|null $viewer_role
 * @var array|null $pending_join
 * @var array<string,mixed> $stats
 * @var bool $can_manage
 * @var list<array<string,mixed>> $pendingRequests
 * @var array|null $pendingInvitation
 * @var string $basePath
 */
use App\Core\Auth;
$groupId = (int) $group['id'];
?>
<section class="page-header">
    <div>
        <h1><?= e((string) $group['name']) ?></h1>
        <p class="muted">
            <?= e(ucfirst((string) $group['group_type'])) ?> group
            · @<?= e((string) ($group['owner_username'] ?? '')) ?>
            · <?= (int) ($stats['member_count'] ?? 0) ?> members
        </p>
    </div>
    <div class="hero-actions">
        <?php if (Auth::check()): ?>
            <?php if ($viewer_role): ?>
                <form method="post" action="<?= e(url('/groups/' . $groupId . '/leave')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn" type="submit">Leave</button>
                </form>
            <?php elseif ($pending_join): ?>
                <span class="pill">Join request pending</span>
            <?php elseif ($pendingInvitation): ?>
                <form method="post" action="<?= e(url('/groups/invitations/' . (int) $pendingInvitation['id'] . '/respond')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="decision" value="accepted">
                    <button class="btn btn-primary" type="submit">Accept invite</button>
                </form>
                <form method="post" action="<?= e(url('/groups/invitations/' . (int) $pendingInvitation['id'] . '/respond')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="decision" value="declined">
                    <button class="btn" type="submit">Decline</button>
                </form>
            <?php else: ?>
                <form method="post" action="<?= e(url('/groups/' . $groupId . '/join')) ?>">
                    <?= csrf_field() ?>
                    <?php if (($group['join_policy'] ?? '') === 'approval'): ?>
                        <div class="form-group">
                            <label for="message">Message (optional)</label>
                            <input id="message" name="message" maxlength="500" placeholder="Why do you want to join?">
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-primary" type="submit">Join</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login to join</a>
        <?php endif; ?>
        <button type="button" class="btn" data-report data-type="collab_group" data-id="<?= $groupId ?>">Report</button>
    </div>
</section>

<div class="card">
    <h2>About</h2>
    <p><?= nl2br(e((string) $group['description'])) ?></p>
    <?php if ($skills !== []): ?>
        <div class="tag-row">
            <?php foreach ($skills as $sk): ?>
                <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="stats-grid collab-stats">
    <div class="stat-card"><strong><?= (int) ($stats['challenges_solved'] ?? 0) ?></strong><span>Challenges solved</span></div>
    <div class="stat-card"><strong><?= (int) ($stats['labs_completed'] ?? 0) ?></strong><span>Labs completed</span></div>
    <div class="stat-card"><strong><?= (int) ($stats['writeups'] ?? 0) ?></strong><span>Writeups</span></div>
</div>

<div class="card">
    <h2>Members</h2>
    <ul class="member-list">
        <?php foreach ($members as $m): ?>
            <li>
                <a href="<?= e(url('/profile/' . $m['username'])) ?>">@<?= e((string) $m['username']) ?></a>
                <span class="pill"><?= e(str_replace('_', ' ', (string) $m['role'])) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if ($can_manage): ?>
<div class="card">
    <h2>Manage group</h2>
    <form method="post" action="<?= e(url('/groups/' . $groupId . '/edit')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="name">Name</label>
            <input id="name" name="name" required value="<?= e((string) $group['name']) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?= e((string) $group['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select id="visibility" name="visibility">
                <?php foreach (['public', 'community', 'private'] as $v): ?>
                    <option value="<?= e($v) ?>"<?= ($group['visibility'] ?? '') === $v ? ' selected' : '' ?>><?= e(ucfirst($v)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="join_policy">Join policy</label>
            <select id="join_policy" name="join_policy">
                <?php foreach (['open', 'approval', 'invite_only'] as $jp): ?>
                    <option value="<?= e($jp) ?>"<?= ($group['join_policy'] ?? '') === $jp ? ' selected' : '' ?>><?= e(str_replace('_', ' ', $jp)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Save changes</button>
    </form>

    <h3>Invite member</h3>
    <form method="post" action="<?= e(url('/groups/' . $groupId . '/invite')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" required placeholder="username">
        </div>
        <button class="btn" type="submit">Send invite</button>
    </form>

    <?php if ($pendingRequests !== []): ?>
    <h3>Pending join requests</h3>
    <ul class="result-list">
        <?php foreach ($pendingRequests as $req): ?>
            <li>
                <strong>@<?= e((string) $req['username']) ?></strong>
                <?php if (!empty($req['message'])): ?>
                    <p class="muted small"><?= e((string) $req['message']) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= e(url('/groups/' . $groupId . '/join-requests/' . (int) $req['id'])) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="decision" value="approved">
                    <button class="btn btn-sm btn-primary" type="submit">Approve</button>
                </form>
                <form method="post" action="<?= e(url('/groups/' . $groupId . '/join-requests/' . (int) $req['id'])) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="decision" value="rejected">
                    <button class="btn btn-sm" type="submit">Decline</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($goals !== []): ?>
<div class="card">
    <h2>Goals</h2>
    <ul class="result-list">
        <?php foreach ($goals as $goal): ?>
            <li>
                <strong><?= e((string) $goal['title']) ?></strong>
                <span class="pill"><?= e((string) $goal['status']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
