<?php
/**
 * @var array{rows: list<array<string,mixed>>, total: int, page: int, per_page: int, pages: int} $result
 * @var list<\App\Models\Role> $roles
 * @var array{q: string, status: string, role: string} $filters
 */
\App\Core\View::partial('admin/partials/nav');

$queryBase = array_filter([
    'q' => $filters['q'] !== '' ? $filters['q'] : null,
    'status' => $filters['status'] !== '' ? $filters['status'] : null,
    'role' => $filters['role'] !== '' ? $filters['role'] : null,
], static fn($v) => $v !== null);
?>
<section class="page-header">
    <div>
        <h1>Users &amp; Roles</h1>
        <p class="muted">Manage accounts, status, and RBAC roles (<code>roles</code> / <code>user_roles</code>).</p>
    </div>
</section>

<div class="card">
    <form method="get" action="<?= e(url('/admin/users')) ?>" class="admin-filter-form">
        <label class="grow">
            <span>Search</span>
            <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="username, email, name, student ID">
        </label>
        <label>
            <span>Status</span>
            <select name="status">
                <option value="">All</option>
                <?php foreach (['active', 'suspended', 'banned'] as $st): ?>
                    <option value="<?= e($st) ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Role</span>
            <select name="role">
                <option value="">All</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= e($role->name) ?>" <?= $filters['role'] === $role->name ? 'selected' : '' ?>><?= e($role->name) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn" href="<?= e(url('/admin/users')) ?>">Reset</a>
    </form>
</div>

<div class="card table-card">
    <p class="muted small"><?= (int) $result['total'] ?> user<?= $result['total'] === 1 ? '' : 's' ?></p>
    <?php if ($result['rows'] === []): ?>
        <p class="muted">No users found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Last login</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['rows'] as $u): ?>
                    <tr>
                        <td><?= (int) $u['id'] ?></td>
                        <td>
                            <a href="<?= e(url('/admin/users/' . (int) $u['id'])) ?>"><strong>@<?= e((string) $u['username']) ?></strong></a>
                            <?php if (!empty($u['full_name'])): ?>
                                <div class="muted small"><?= e((string) $u['full_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= e((string) $u['email']) ?></td>
                        <td><?= e((string) ($u['roles'] ?? '—')) ?></td>
                        <td><span class="pill status-<?= e((string) $u['status']) ?>"><?= e((string) $u['status']) ?></span></td>
                        <td><?= !empty($u['last_login_at']) ? e(time_ago((string) $u['last_login_at'])) : '—' ?></td>
                        <td><a class="btn btn-sm" href="<?= e(url('/admin/users/' . (int) $u['id'])) ?>">Manage</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($result['pages'] > 1): ?>
        <div class="admin-pagination">
            <?php if ($result['page'] > 1): ?>
                <a class="btn btn-sm" href="<?= e(url('/admin/users?' . http_build_query($queryBase + ['page' => $result['page'] - 1]))) ?>">← Prev</a>
            <?php endif; ?>
            <?php if ($result['page'] < $result['pages']): ?>
                <a class="btn btn-sm" href="<?= e(url('/admin/users?' . http_build_query($queryBase + ['page' => $result['page'] + 1]))) ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
