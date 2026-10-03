<?php
/**
 * @var array<string, mixed> $stats
 * @var array<string, mixed>|null $continueLearning
 * @var list<array<string, mixed>> $recommended
 * @var list<array<string, mixed>> $featured
 * @var list<\App\Models\ChallengeCategory> $categories
 */
use App\Core\Auth;
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="arena-hero">
    <div class="arena-hero-content">
        <h1>Cyber Arena</h1>
        <p class="muted">Practice cybersecurity skills through hands-on challenges. Web, network, crypto, forensics, and more.</p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="<?= e(url('/arena/challenges')) ?>">Browse Challenges</a>
            <?php if (Auth::check()): ?>
                <a class="btn" href="<?= e(url('/arena/progress')) ?>">My Progress</a>
            <?php else: ?>
                <a class="btn" href="<?= e(url('/login')) ?>">Login to Compete</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="arena-stats card" aria-label="Arena statistics">
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($stats['challenges'] ?? 0) ?></span>
        <span class="arena-stat-label">Challenges</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($stats['solved'] ?? 0) ?></span>
        <span class="arena-stat-label">Solved</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($stats['points'] ?? 0) ?></span>
        <span class="arena-stat-label">Arena Points</span>
    </div>
    <div class="arena-stat">
        <span class="arena-stat-value"><?= (int) ($stats['streak'] ?? 0) ?></span>
        <span class="arena-stat-label">Day Streak</span>
    </div>
</section>

<?php if ($continueLearning !== null): ?>
<section class="card arena-section">
    <h2>Continue Learning</h2>
    <p>
        <strong><?= e($continueLearning['title']) ?></strong>
        <span class="muted"> · <?= e($continueLearning['category_name'] ?? '') ?> · <?= e($continueLearning['progress'] ?? '') ?></span>
    </p>
    <a class="btn btn-primary" href="<?= e(url('/arena/challenges/' . (int) $continueLearning['id'])) ?>">Continue</a>
</section>
<?php endif; ?>

<?php if ($recommended !== []): ?>
<section class="arena-section">
    <h2>Recommended for You</h2>
    <div class="challenge-grid">
        <?php foreach ($recommended as $item): ?>
            <?php \App\Core\View::partial('arena/partials/challenge-card', ['challenge' => $item]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($featured !== []): ?>
<section class="arena-section">
    <h2>Featured Challenges</h2>
    <div class="challenge-grid">
        <?php foreach ($featured as $item): ?>
            <?php \App\Core\View::partial('arena/partials/challenge-card', ['challenge' => $item]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="arena-section">
    <h2>Categories</h2>
    <?php if ($categories === []): ?>
        <div class="empty-state card">
            <p class="muted">No categories available yet.</p>
        </div>
    <?php else: ?>
        <div class="challenge-grid">
            <?php foreach ($categories as $cat): ?>
                <a class="card challenge-card category-card" href="<?= e(url('/arena/categories/' . $cat->slug)) ?>">
                    <h3><?= e($cat->name) ?></h3>
                    <?php if ($cat->description): ?>
                        <p class="muted"><?= e(mb_strimwidth($cat->description, 0, 120, '…')) ?></p>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
