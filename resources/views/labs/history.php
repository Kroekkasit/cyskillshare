<?php
/**
 * @var list<array<string, mixed>> $completions
 * @var list<array<string, mixed>> $recentInstances
 */
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/labs')) ?>">Cyber Labs</a>
    <span>/</span>
    <span>My History</span>
</nav>

<section class="page-header">
    <div>
        <h1>Lab History</h1>
        <p class="muted">Your completed labs and recent sessions.</p>
    </div>
</section>

<section class="card">
    <h2>Completed Labs</h2>
    <?php if ($completions === []): ?>
        <p class="muted">No completed labs yet. <a href="<?= e(url('/labs')) ?>">Browse labs</a>.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Lab</th>
                    <th scope="col">Score</th>
                    <th scope="col">Tasks</th>
                    <th scope="col">Hints</th>
                    <th scope="col">Completed</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($completions as $c): ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/labs/' . $c['slug'])) ?>"><?= e((string) $c['title']) ?></a>
                            <div class="muted small"><?= e((string) ($c['category_name'] ?? '')) ?></div>
                        </td>
                        <td><?= (int) ($c['score'] ?? 0) ?></td>
                        <td><?= (int) ($c['required_completed'] ?? 0) ?>/<?= (int) ($c['required_total'] ?? 0) ?></td>
                        <td><?= (int) ($c['hints_used'] ?? 0) ?></td>
                        <td><?= e(time_ago((string) $c['completed_at'])) ?></td>
                        <td>
                            <?php if (!empty($c['instance_id'])): ?>
                                <a class="btn btn-sm" href="<?= e(url('/labs/' . $c['slug'] . '/instance/' . (int) $c['instance_id'] . '/complete')) ?>">Summary</a>
                            <?php endif; ?>
                            <a class="btn btn-sm" href="<?= e(url('/writeups/create?lab=' . rawurlencode((string) $c['slug']))) ?>">Writeup</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Recent Sessions</h2>
    <?php if ($recentInstances === []): ?>
        <p class="muted">No lab sessions yet.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Lab</th>
                    <th scope="col">Status</th>
                    <th scope="col">Started</th>
                    <th scope="col">Score</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentInstances as $inst): ?>
                    <tr>
                        <td><a href="<?= e(url('/labs/' . $inst['slug'])) ?>"><?= e((string) $inst['title']) ?></a></td>
                        <td>
                            <span class="pill"><?= e(ucfirst((string) ($inst['status'] ?? ''))) ?></span>
                            <?php if (!empty($inst['provision_state']) && $inst['provision_state'] !== 'ready'): ?>
                                <span class="muted small"><?= e((string) $inst['provision_state']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(time_ago((string) ($inst['started_at'] ?? ''))) ?></td>
                        <td><?= $inst['score'] !== null ? (int) $inst['score'] : '—' ?></td>
                        <td>
                            <?php if (in_array($inst['status'] ?? '', ['queued', 'provisioning', 'running', 'paused'], true)): ?>
                                <a class="btn btn-sm" href="<?= e(url('/labs/' . $inst['slug'] . '/instance/' . (int) $inst['id'])) ?>">Open</a>
                            <?php elseif (($inst['status'] ?? '') === 'completed'): ?>
                                <a class="btn btn-sm" href="<?= e(url('/labs/' . $inst['slug'] . '/instance/' . (int) $inst['id'] . '/complete')) ?>">Summary</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
