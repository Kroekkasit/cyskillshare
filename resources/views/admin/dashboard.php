<?php
/**
 * @var array<string, int> $stats
 * @var list<array{title: string, description: string, href: string, roles: list<string>}> $tools
 * @var bool $isAdmin
 */
\App\Core\View::partial('admin/partials/nav');
?>
<section class="page-header">
    <div>
        <h1>Admin Console</h1>
        <p class="muted">Platform management, security audit trail, and staff tools.</p>
    </div>
</section>

<?php if ($isAdmin && $stats !== []): ?>
<section class="admin-stat-grid">
    <div class="admin-stat">
        <span class="admin-stat-label">Users</span>
        <strong><?= (int) $stats['users'] ?></strong>
        <span class="muted small"><?= (int) $stats['users_active'] ?> active · <?= (int) $stats['users_suspended'] ?> suspended · <?= (int) $stats['users_banned'] ?> banned</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-label">Logins (24h)</span>
        <strong><?= (int) $stats['logins_24h'] ?></strong>
        <span class="muted small"><?= (int) $stats['failed_logins_24h'] ?> failed · <?= (int) $stats['csrf_rejected_24h'] ?> CSRF rejects</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-label">Activity logs</span>
        <strong><?= (int) $stats['activity_logs'] ?></strong>
        <span class="muted small"><a href="<?= e(url('/admin/activity')) ?>">View audit trail</a></span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-label">Community</span>
        <strong><?= (int) $stats['threads'] ?></strong>
        <span class="muted small"><?= (int) $stats['replies'] ?> replies · <?= (int) $stats['pending_reports'] ?> pending reports</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-label">Arena / Labs</span>
        <strong><?= (int) $stats['challenges'] ?></strong>
        <span class="muted small"><?= (int) $stats['labs'] ?> published labs · <?= (int) $stats['pending_evidence'] ?> pending evidence</span>
    </div>
</section>
<?php endif; ?>

<section class="admin-tool-grid">
    <?php foreach ($tools as $tool): ?>
        <a class="admin-tool-card" href="<?= e(url($tool['href'])) ?>">
            <h2><?= e($tool['title']) ?></h2>
            <p class="muted"><?= e($tool['description']) ?></p>
        </a>
    <?php endforeach; ?>
</section>

<?php if ($tools === []): ?>
<div class="card empty-state">
    <p class="muted">No admin tools are available for your roles.</p>
</div>
<?php endif; ?>
