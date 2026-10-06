<?php
/**
 * @var array<string, mixed> $user
 * @var list<\App\Models\Role> $allRoles
 * @var int $actorId
 */
\App\Core\View::partial('admin/partials/nav');
$userRoles = is_array($user['roles'] ?? null) ? $user['roles'] : [];
?>
<section class="page-header">
    <div>
        <h1>@<?= e((string) $user['username']) ?></h1>
        <p class="muted">
            User #<?= (int) $user['id'] ?>
            · <a href="<?= e(url('/profile/' . $user['username'])) ?>">Public profile</a>
        </p>
    </div>
</section>

<div class="admin-two-col">
    <div class="card">
        <h2>Account</h2>
        <dl class="admin-dl">
            <dt>Email</dt><dd><?= e((string) $user['email']) ?></dd>
            <dt>Full name</dt><dd><?= e((string) ($user['full_name'] ?? '—')) ?></dd>
            <dt>Student ID</dt><dd><?= e((string) ($user['student_id'] ?? '—')) ?></dd>
            <dt>Program</dt><dd><?= e((string) ($user['program'] ?? '—')) ?></dd>
            <dt>Status</dt><dd><span class="pill status-<?= e((string) $user['status']) ?>"><?= e((string) $user['status']) ?></span></dd>
            <dt>Joined</dt><dd><?= e((string) $user['created_at']) ?></dd>
            <dt>Last login</dt><dd><?= e((string) ($user['last_login_at'] ?? '—')) ?></dd>
            <dt>Discussions</dt><dd><?= (int) ($user['discussion_count'] ?? 0) ?></dd>
            <dt>Replies</dt><dd><?= (int) ($user['reply_count'] ?? 0) ?></dd>
        </dl>

        <?php if ((int) $user['id'] !== $actorId): ?>
            <form method="post" action="<?= e(url('/admin/users/' . (int) $user['id'] . '/status')) ?>" class="admin-inline-form">
                <?= csrf_field() ?>
                <label>
                    <span>Change status</span>
                    <select name="status">
                        <?php foreach (['active', 'suspended', 'banned'] as $st): ?>
                            <option value="<?= e($st) ?>" <?= ($user['status'] ?? '') === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="btn btn-primary" type="submit">Update status</button>
            </form>
        <?php else: ?>
            <p class="muted small">You cannot change your own account status.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Roles (RBAC)</h2>
        <ul class="role-chip-list">
            <?php if ($userRoles === []): ?>
                <li class="muted">No roles assigned.</li>
            <?php else: ?>
                <?php foreach ($userRoles as $roleName): ?>
                    <li>
                        <span class="pill"><?= e((string) $roleName) ?></span>
                        <form method="post" action="<?= e(url('/admin/users/' . (int) $user['id'] . '/roles/remove')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="role" value="<?= e((string) $roleName) ?>">
                            <button class="btn btn-sm" type="submit" onclick="return confirm('Remove role <?= e((string) $roleName) ?>?')">Remove</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <form method="post" action="<?= e(url('/admin/users/' . (int) $user['id'] . '/roles')) ?>" class="admin-inline-form">
            <?= csrf_field() ?>
            <label>
                <span>Assign role</span>
                <select name="role" required>
                    <option value="">Select…</option>
                    <?php foreach ($allRoles as $role): ?>
                        <?php if (in_array($role->name, $userRoles, true)) {
                            continue;
                        } ?>
                        <option value="<?= e($role->name) ?>"><?= e($role->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-primary" type="submit">Assign</button>
        </form>
    </div>
</div>

<div class="card table-card">
    <h2>Recent activity</h2>
    <?php $recent = is_array($user['recent_activity'] ?? null) ? $user['recent_activity'] : []; ?>
    <?php if ($recent === []): ?>
        <p class="muted">No activity logged for this user.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $a): ?>
                    <tr>
                        <td><?= e(time_ago((string) $a['created_at'])) ?></td>
                        <td><span class="pill"><?= e((string) $a['action']) ?></span></td>
                        <td>
                            <?= e((string) ($a['target_type'] ?? '—')) ?>
                            <?php if ($a['target_id'] !== null): ?>#<?= (int) $a['target_id'] ?><?php endif; ?>
                        </td>
                        <td><code><?= e((string) ($a['ip_address'] ?? '—')) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="muted small"><a href="<?= e(url('/admin/activity?user_id=' . (int) $user['id'])) ?>">View full audit trail →</a></p>
    <?php endif; ?>
</div>
