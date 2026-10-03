<?php
/**
 * @var string $title
 * @var array<string, mixed> $lab
 * @var array<string, mixed> $instance
 * @var array<string, string> $publicEnv
 * @var array<string, mixed> $target
 */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="lab-gateway-body">
<div class="lab-gateway-shell">
    <header class="lab-gateway-header">
        <strong><?= e((string) ($target['title'] ?? 'Lab Target')) ?></strong>
        <span class="muted">Simulated · <?= e((string) ($publicEnv['instance'] ?? '')) ?></span>
    </header>

    <main class="lab-gateway-main">
        <?php $kind = (string) ($target['kind'] ?? 'generic'); ?>

        <?php if ($kind === 'web'): ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) $target['title']) ?></h1>
                <?php if (!empty($target['notes'])): ?>
                    <p class="muted"><?= e((string) $target['notes']) ?></p>
                <?php endif; ?>
                <form class="lab-gateway-form" onsubmit="return false;">
                    <?php foreach (($target['form_fields'] ?? []) as $field): ?>
                        <div class="form-group">
                            <label for="gw-<?= e($field) ?>"><?= e(ucfirst($field)) ?></label>
                            <input id="gw-<?= e($field) ?>" type="text" autocomplete="off">
                        </div>
                    <?php endforeach; ?>
                    <button class="btn btn-primary" type="button" data-show-success>Show success panel</button>
                </form>
                <?php if (!empty($target['log_lines'])): ?>
                    <pre class="lab-gateway-log"><?php foreach ($target['log_lines'] as $line): ?><?= e((string) $line) . "\n" ?><?php endforeach; ?></pre>
                <?php endif; ?>
            </section>

        <?php elseif ($kind === 'pcap'): ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) $target['title']) ?></h1>
                <table class="leaderboard-table">
                    <thead><tr><th>Source</th><th>Dest</th><th>Proto</th><th>Note</th></tr></thead>
                    <tbody>
                        <?php foreach (($target['flows'] ?? []) as $flow): ?>
                            <tr>
                                <td><code><?= e((string) ($flow['src'] ?? '')) ?></code></td>
                                <td><code><?= e((string) ($flow['dst'] ?? '')) ?></code></td>
                                <td><?= e((string) ($flow['proto'] ?? '')) ?></td>
                                <td><?= e((string) ($flow['note'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!empty($target['ioc'])): ?>
                    <p>IOC: <code><?= e((string) $target['ioc']) ?></code></p>
                <?php endif; ?>
                <button class="btn btn-primary" type="button" data-show-success>Reveal flag</button>
            </section>

        <?php elseif ($kind === 'forensics'): ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) $target['title']) ?></h1>
                <dl class="lab-env-facts">
                    <?php foreach (($target['artifacts'] ?? []) as $k => $v): ?>
                        <div><dt><?= e(str_replace('_', ' ', ucfirst($k))) ?></dt><dd><code><?= e((string) $v) ?></code></dd></div>
                    <?php endforeach; ?>
                </dl>
                <button class="btn btn-primary" type="button" data-show-success>Investigation complete</button>
            </section>

        <?php elseif ($kind === 'ir'): ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) $target['title']) ?></h1>
                <ul class="result-list">
                    <?php foreach (($target['findings'] ?? []) as $k => $v): ?>
                        <li><strong><?= e(str_replace('_', ' ', ucfirst($k))) ?>:</strong> <code><?= e((string) $v) ?></code></li>
                    <?php endforeach; ?>
                </ul>
                <button class="btn btn-primary" type="button" data-show-success>Confirm remediation</button>
            </section>

        <?php elseif ($kind === 'linux'): ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) $target['title']) ?></h1>
                <pre class="lab-gateway-terminal">$ ps aux
<?php foreach (($target['ps'] ?? []) as $proc): ?><?= e((string) $proc) . "\n" ?><?php endforeach; ?>
$ tail auth.log
<?= e((string) ($target['auth'] ?? '')) ?>

$ ls -la <?= e((string) ($target['perms'] ?? '')) ?>
</pre>
                <button class="btn btn-primary" type="button" data-show-success>Run check</button>
            </section>

        <?php else: ?>
            <section class="lab-gateway-panel">
                <h1><?= e((string) ($target['title'] ?? 'Lab Environment')) ?></h1>
                <?php if (!empty($target['target_ip'])): ?>
                    <p>Target: <code><?= e((string) $target['target_ip']) ?></code></p>
                <?php endif; ?>
                <button class="btn btn-primary" type="button" data-show-success>Complete check</button>
            </section>
        <?php endif; ?>

        <aside class="lab-gateway-success" id="lab-success-panel" hidden>
            <h2>Success</h2>
            <p>Objective achieved. Flag:</p>
            <code class="lab-flag"><?= e((string) ($target['success_flag'] ?? '')) ?></code>
        </aside>
    </main>
</div>
<script>
document.querySelectorAll('[data-show-success]').forEach((btn) => {
  btn.addEventListener('click', () => {
    const panel = document.getElementById('lab-success-panel');
    if (panel) panel.hidden = false;
  });
});
</script>
</body>
</html>
