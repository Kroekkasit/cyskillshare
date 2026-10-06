<?php
/**
 * @var array<string, mixed> $system
 */
\App\Core\View::partial('admin/partials/nav');
?>
<section class="page-header">
    <div>
        <h1>System Status</h1>
        <p class="muted">Runtime configuration for Database &amp; Web Security review. Secrets are not displayed.</p>
    </div>
</section>

<div class="card">
    <h2>Application</h2>
    <dl class="admin-dl">
        <dt>Name</dt><dd><?= e((string) $system['app_name']) ?></dd>
        <dt>Environment</dt><dd><span class="pill"><?= e((string) $system['app_env']) ?></span></dd>
        <dt>Debug</dt>
        <dd>
            <?php if (!empty($system['app_debug'])): ?>
                <span class="pill status-banned">ON (hide in production)</span>
            <?php else: ?>
                <span class="pill status-active">OFF</span>
            <?php endif; ?>
        </dd>
        <dt>URL</dt><dd><?= e((string) $system['app_url']) ?></dd>
        <dt>Timezone</dt><dd><?= e((string) $system['timezone']) ?></dd>
        <dt>PHP</dt><dd><?= e((string) $system['php_version']) ?></dd>
    </dl>
</div>

<div class="card">
    <h2>Database (least privilege)</h2>
    <dl class="admin-dl">
        <dt>Host</dt><dd><?= e((string) $system['db_host']) ?></dd>
        <dt>Database</dt><dd><?= e((string) $system['db_name']) ?></dd>
        <dt>App user</dt><dd><code><?= e((string) $system['db_user']) ?></code> <?= strtolower((string) $system['db_user']) === 'root' ? '<span class="pill status-banned">ROOT — not allowed</span>' : '<span class="pill status-active">non-root</span>' ?></dd>
        <dt>Connection</dt>
        <dd>
            <?php if (!empty($system['db_ok'])): ?>
                <span class="pill status-active">OK</span>
            <?php else: ?>
                <span class="pill status-banned">FAILED</span>
            <?php endif; ?>
        </dd>
    </dl>
</div>

<div class="card">
    <h2>Session security</h2>
    <dl class="admin-dl">
        <dt>Cookie name</dt><dd><code><?= e((string) $system['session_name']) ?></code></dd>
        <dt>Idle lifetime</dt><dd><?= (int) $system['session_lifetime'] ?> seconds</dd>
        <dt>HttpOnly</dt><dd><?= !empty($system['session_http_only']) ? 'yes' : 'no' ?></dd>
        <dt>SameSite</dt><dd><?= e((string) $system['session_same_site']) ?></dd>
        <dt>Secure flag</dt><dd><?= !empty($system['session_secure']) ? 'yes' : 'no (auto-on for HTTPS)' ?></dd>
    </dl>
</div>
