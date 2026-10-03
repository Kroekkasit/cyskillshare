<?php
/**
 * @var array<string, mixed> $lab
 * @var array<string, mixed> $instance
 * @var array<string, mixed>|null $completion
 * @var array{required_total:int,required_completed:int,optional_completed:int,score_sum:int,hints_used:int} $stats
 * @var list<array<string, mixed>> $tasks
 */
$slug = (string) $lab['slug'];
$score = (int) ($completion['score'] ?? $instance['score'] ?? $stats['score_sum']);
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/labs')) ?>">Cyber Labs</a>
    <span>/</span>
    <a href="<?= e(url('/labs/' . $slug)) ?>"><?= e((string) $lab['title']) ?></a>
    <span>/</span>
    <span>Complete</span>
</nav>

<section class="card lab-complete-panel">
    <header class="lab-complete-head">
        <h1>Lab Complete!</h1>
        <p class="subtitle">Required tasks finished. Great work.</p>
    </header>

    <div class="lab-complete-stats arena-stats">
        <div class="arena-stat">
            <span class="arena-stat-value"><?= $score ?></span>
            <span class="arena-stat-label">Final Score</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $stats['required_completed'] ?>/<?= (int) $stats['required_total'] ?></span>
            <span class="arena-stat-label">Required Tasks</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $stats['optional_completed'] ?></span>
            <span class="arena-stat-label">Optional Done</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $stats['hints_used'] ?></span>
            <span class="arena-stat-label">Hints Used</span>
        </div>
    </div>

    <section class="lab-section">
        <h2>Task Summary</h2>
        <ul class="lab-task-overview">
            <?php foreach ($tasks as $task): ?>
                <?php $done = ($task['status'] ?? '') === 'completed'; ?>
                <li class="<?= $done ? 'met' : 'unmet' ?>">
                    <?= $done ? '✓' : '○' ?>
                    <?= e((string) $task['title']) ?>
                    <span class="muted"><?= (int) ($task['best_score'] ?? 0) ?> pts</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <div class="hero-actions lab-complete-actions">
        <a class="btn btn-primary" href="<?= e(url('/writeups/create?lab=' . rawurlencode($slug))) ?>">
            Write a Writeup
        </a>
        <a class="btn" href="<?= e(url('/labs/' . $slug)) ?>">Back to Lab</a>
        <a class="btn" href="<?= e(url('/labs/history')) ?>">Lab History</a>
    </div>
</section>
