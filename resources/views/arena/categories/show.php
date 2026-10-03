<?php
/**
 * @var \App\Models\ChallengeCategory $category
 * @var int $total
 * @var int $solved
 * @var array<string, int> $difficulty
 * @var list<array<string, mixed>> $challenges
 */
use App\Core\Auth;
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/arena')) ?>">Cyber Arena</a>
    <span>/</span>
    <a href="<?= e(url('/arena/challenges')) ?>">Challenges</a>
    <span>/</span>
    <span><?= e($category->name) ?></span>
</nav>

<section class="page-header">
    <div>
        <h1><?= e($category->name) ?></h1>
        <?php if ($category->description): ?>
            <p class="muted"><?= e($category->description) ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="arena-stats card" aria-label="Category statistics">
    <div class="arena-stat">
        <span class="arena-stat-value"><?= $total ?></span>
        <span class="arena-stat-label">Challenges</span>
    </div>
    <?php if (Auth::check()): ?>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= $solved ?></span>
        <span class="arena-stat-label">Your Solves</span>
    </div>
    <?php endif; ?>
    <?php foreach (['easy', 'medium', 'hard', 'expert'] as $d): ?>
        <?php if (($difficulty[$d] ?? 0) > 0): ?>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) $difficulty[$d] ?></span>
            <span class="arena-stat-label"><?= e(ucfirst($d)) ?></span>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>

<?php if ($challenges === []): ?>
    <div class="empty-state card">
        <h2>No challenges in this category</h2>
        <p class="muted">Check back later for new content.</p>
    </div>
<?php else: ?>
    <div class="challenge-grid">
        <?php foreach ($challenges as $item): ?>
            <?php \App\Core\View::partial('arena/partials/challenge-card', ['challenge' => $item]); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
