<?php
/**
 * @var array<string, mixed> $lab
 * @var array<string, mixed> $instance
 * @var list<array<string, mixed>> $tasks
 * @var array<int, list<array<string, mixed>>> $hintsByTask
 * @var array<int, list<array<string, mixed>>> $revealedByTask
 * @var array<string, string> $publicEnv
 * @var array{required_total:int,required_completed:int,optional_completed:int,score_sum:int,hints_used:int} $stats
 * @var int $pollSeconds
 */
$slug = (string) $lab['slug'];
$instanceId = (int) $instance['id'];
$isReady = ($instance['status'] ?? '') === 'running' && ($instance['provision_state'] ?? '') === 'ready';
$isProvisioning = in_array($instance['provision_state'] ?? '', ['queued', 'provisioning'], true);
$gatewayUrl = url('/labs/gateway/' . $instanceId);
$statusUrl = url('/labs/' . $slug . '/instance/' . $instanceId . '/status');
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/labs')) ?>">Cyber Labs</a>
    <span>/</span>
    <a href="<?= e(url('/labs/' . $slug)) ?>"><?= e((string) $lab['title']) ?></a>
    <span>/</span>
    <span>Session</span>
</nav>

<header class="lab-instance-header card">
    <div class="lab-instance-header-main">
        <h1><?= e((string) $lab['title']) ?></h1>
        <div class="lab-instance-meta">
            <span class="pill" id="lab-provision-badge"><?= e(ucfirst((string) ($instance['provision_state'] ?? 'unknown'))) ?></span>
            <span class="pill"><?= (int) $stats['required_completed'] ?>/<?= (int) $stats['required_total'] ?> required</span>
            <span class="pill"><?= (int) $stats['score_sum'] ?> pts</span>
        </div>
    </div>
    <div class="lab-instance-toolbar">
        <div class="lab-timer"
             data-lab-timer
             data-seconds-remaining="<?= (int) \App\Services\LabProgressService::secondsRemaining($instanceId) ?>"
             data-status-url="<?= e($statusUrl) ?>"
             data-poll-seconds="<?= (int) $pollSeconds ?>">
            <span class="lab-timer-label">Time remaining</span>
            <span class="lab-timer-value" data-timer-display>--:--</span>
        </div>
        <div class="hero-actions">
            <?php if ($isReady): ?>
                <a class="btn" href="<?= e($gatewayUrl) ?>" target="_blank" rel="noopener">Open Environment</a>
            <?php endif; ?>
            <form method="post" action="<?= e(url('/labs/' . $slug . '/instance/' . $instanceId . '/stop')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn" type="submit">Stop</button>
            </form>
            <form method="post" action="<?= e(url('/labs/' . $slug . '/instance/' . $instanceId . '/reset')) ?>" class="inline-form"
                  onsubmit="return confirm('Reset will reprovision the environment. Continue?')">
                <?= csrf_field() ?>
                <input type="hidden" name="confirm" value="yes">
                <button class="btn" type="submit">Reset</button>
            </form>
        </div>
    </div>
</header>

<?php if ($isProvisioning): ?>
<div class="card lab-provisioning-panel" data-lab-provisioning data-status-url="<?= e($statusUrl) ?>" data-poll-seconds="<?= (int) $pollSeconds ?>">
    <h2>Provisioning environment</h2>
    <p class="muted">Your lab is being prepared. This page will update automatically.</p>
    <div class="lab-provision-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="30">
        <div class="lab-provision-bar" data-provision-bar></div>
    </div>
    <p class="muted small" data-provision-status>State: <?= e((string) ($instance['provision_state'] ?? '')) ?></p>
</div>
<?php endif; ?>

<div class="lab-instance-layout">
    <section class="card lab-tasks-panel" aria-labelledby="tasks-heading">
        <h2 id="tasks-heading">Tasks</h2>
        <ul class="lab-task-list">
            <?php foreach ($tasks as $task): ?>
                <?php
                $taskId = (int) $task['task_id'];
                $status = (string) ($task['status'] ?? 'locked');
                $icon = match ($status) {
                    'completed' => '✓',
                    'locked' => '🔒',
                    'in_progress' => '◐',
                    default => '○',
                };
                $hints = $hintsByTask[$taskId] ?? [];
                $revealed = $revealedByTask[$taskId] ?? [];
                ?>
                <li class="lab-task-item lab-task-<?= e($status) ?>" id="task-<?= $taskId ?>">
                    <div class="lab-task-head">
                        <span class="lab-task-icon" aria-hidden="true"><?= $icon ?></span>
                        <strong><?= e((string) $task['title']) ?></strong>
                        <?php if ((int) ($task['required'] ?? 0) === 1): ?>
                            <span class="pill">Required</span>
                        <?php endif; ?>
                        <span class="muted"><?= (int) ($task['points'] ?? 0) ?> pts</span>
                    </div>
                    <?php if ($status !== 'locked'): ?>
                        <div class="content-body lab-task-desc">
                            <?= nl2br(e((string) ($task['description'] ?? ''))) ?>
                        </div>
                        <?php if ($status !== 'completed' && $isReady): ?>
                            <form class="lab-task-submit" method="post"
                                  action="<?= e(url('/labs/' . $slug . '/instance/' . $instanceId . '/tasks/' . $taskId . '/submit')) ?>">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label class="visually-hidden" for="answer-<?= $taskId ?>">Your answer</label>
                                    <input id="answer-<?= $taskId ?>" name="answer" type="text" required maxlength="2000"
                                           placeholder="Enter your answer…">
                                </div>
                                <button class="btn btn-primary btn-sm" type="submit">Submit</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($hints !== [] && $status !== 'locked'): ?>
                            <div class="lab-hints">
                                <span class="muted small">Hints</span>
                                <?php foreach ($hints as $hint): ?>
                                    <?php if (!empty($hint['revealed'])): ?>
                                        <?php
                                        $content = '';
                                        foreach ($revealed as $rh) {
                                            if ((int) $rh['id'] === (int) $hint['id']) {
                                                $content = (string) $rh['content'];
                                                break;
                                            }
                                        }
                                        ?>
                                        <div class="lab-hint revealed">
                                            <strong><?= e((string) $hint['title']) ?></strong>
                                            <?php if ($content !== ''): ?>
                                                <p><?= nl2br(e($content)) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <form method="post" class="inline-form"
                                              action="<?= e(url('/labs/' . $slug . '/instance/' . $instanceId . '/tasks/' . $taskId . '/hints/' . (int) $hint['id'])) ?>">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm" type="submit">
                                                Reveal: <?= e((string) $hint['title']) ?> (−<?= (int) $hint['penalty'] ?> pts)
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="muted small">Complete prerequisite tasks to unlock.</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card lab-env-panel" aria-labelledby="env-heading">
        <h2 id="env-heading">Environment</h2>
        <?php if ($isReady): ?>
            <dl class="lab-env-facts">
                <?php foreach ($publicEnv as $key => $val): ?>
                    <div>
                        <dt><?= e(str_replace('_', ' ', ucfirst($key))) ?></dt>
                        <dd><code><?= e($val) ?></code></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
            <div class="lab-gateway-frame-wrap">
                <iframe class="lab-gateway-frame" src="<?= e($gatewayUrl) ?>" title="Simulated lab target" loading="lazy"></iframe>
            </div>
            <p class="muted small">
                <a href="<?= e($gatewayUrl) ?>" target="_blank" rel="noopener">Open environment in new tab</a>
            </p>
        <?php elseif ($isProvisioning): ?>
            <p class="muted">Environment will appear here when provisioning completes.</p>
        <?php else: ?>
            <p class="muted">Environment unavailable (status: <?= e((string) ($instance['status'] ?? '')) ?>).</p>
        <?php endif; ?>
    </section>
</div>

<?php \App\Core\View::partial('labs/partials/timer'); ?>
