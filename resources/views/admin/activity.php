<?php
/**
 * @var array{rows: list<array<string,mixed>>, total: int, page: int, per_page: int, pages: int} $result
 * @var list<string> $actions
 * @var array{action: string, q: string, user_id: string} $filters
 */
\App\Core\View::partial('admin/partials/nav');

$queryBase = array_filter([
    'action' => $filters['action'] !== '' ? $filters['action'] : null,
    'q' => $filters['q'] !== '' ? $filters['q'] : null,
    'user_id' => $filters['user_id'] !== '' ? $filters['user_id'] : null,
], static fn($v) => $v !== null);
?>
<section class="page-header">
    <div>
        <h1>Activity Logs</h1>
        <p class="muted">Security audit trail stored in MySQL <code>activity_logs</code>. Passwords and tokens are never logged.</p>
    </div>
</section>

<div class="card">
    <form method="get" action="<?= e(url('/admin/activity')) ?>" class="admin-filter-form">
        <label>
            <span>Action</span>
            <select name="action">
                <option value="">All actions</option>
                <?php foreach ($actions as $action): ?>
                    <option value="<?= e($action) ?>" <?= $filters['action'] === $action ? 'selected' : '' ?>><?= e($action) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>User ID</span>
            <input type="number" name="user_id" min="1" value="<?= e($filters['user_id']) ?>" placeholder="e.g. 1">
        </label>
        <label class="grow">
            <span>Search</span>
            <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="username, action, IP…">
        </label>
        <button class="btn btn-primary" type="submit">Filter</button>
        <a class="btn" href="<?= e(url('/admin/activity')) ?>">Reset</a>
    </form>
</div>

<div class="card table-card">
    <p class="muted small"><?= (int) $result['total'] ?> event<?= $result['total'] === 1 ? '' : 's' ?> · page <?= (int) $result['page'] ?> / <?= (int) $result['pages'] ?></p>
    <?php if ($result['rows'] === []): ?>
        <p class="muted">No activity matches these filters.</p>
    <?php else: ?>
        <table class="data-table admin-activity-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>When</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>IP</th>
                    <th>Metadata</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['rows'] as $row): ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td title="<?= e((string) $row['created_at']) ?>"><?= e(time_ago((string) $row['created_at'])) ?></td>
                        <td>
                            <?php if (!empty($row['username'])): ?>
                                <a href="<?= e(url('/admin/users/' . (int) $row['user_id'])) ?>">@<?= e((string) $row['username']) ?></a>
                            <?php elseif ($row['user_id'] !== null): ?>
                                #<?= (int) $row['user_id'] ?>
                            <?php else: ?>
                                <span class="muted">guest</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill"><?= e((string) $row['action']) ?></span></td>
                        <td>
                            <?php if (!empty($row['target_type'])): ?>
                                <?= e((string) $row['target_type']) ?>
                                <?php if ($row['target_id'] !== null): ?>#<?= (int) $row['target_id'] ?><?php endif; ?>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= e((string) ($row['ip_address'] ?? '—')) ?></code></td>
                        <td class="meta-cell">
                            <?php if (!empty($row['metadata_decoded'])): ?>
                                <code><?= e(json_encode($row['metadata_decoded'], JSON_UNESCAPED_UNICODE) ?: '') ?></code>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($result['pages'] > 1): ?>
        <div class="admin-pagination">
            <?php if ($result['page'] > 1): ?>
                <a class="btn btn-sm" href="<?= e(url('/admin/activity?' . http_build_query($queryBase + ['page' => $result['page'] - 1]))) ?>">← Prev</a>
            <?php endif; ?>
            <?php if ($result['page'] < $result['pages']): ?>
                <a class="btn btn-sm" href="<?= e(url('/admin/activity?' . http_build_query($queryBase + ['page' => $result['page'] + 1]))) ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
