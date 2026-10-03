<?php
/**
 * @var array<string, mixed> $lab
 * @var bool|null $completed
 */
$completed = !empty($completed);
$difficulty = (string) ($lab['difficulty'] ?? 'beginner');
?>
<article class="lab-card card">
    <header class="lab-card-head">
        <h3><a href="<?= e(url('/labs/' . $lab['slug'])) ?>"><?= e((string) $lab['title']) ?></a></h3>
        <?php if ($completed): ?>
            <span class="pill solved" aria-label="Completed">✓</span>
        <?php endif; ?>
        <?php if (!empty($lab['featured'])): ?>
            <span class="pill featured">★ Featured</span>
        <?php endif; ?>
    </header>
    <div class="lab-card-meta">
        <span class="difficulty-badge difficulty-<?= e($difficulty) ?>"><?= e(ucfirst($difficulty)) ?></span>
        <?php if (!empty($lab['estimated_minutes'])): ?>
            <span class="muted"><?= (int) $lab['estimated_minutes'] ?> min</span>
        <?php endif; ?>
        <?php if (!empty($lab['category_name'])): ?>
            <span class="muted"><?= e((string) $lab['category_name']) ?></span>
        <?php endif; ?>
        <?php if (isset($lab['start_count'])): ?>
            <span class="muted"><?= (int) $lab['start_count'] ?> starts</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($lab['short_description'])): ?>
        <p class="muted lab-card-excerpt"><?= e((string) $lab['short_description']) ?></p>
    <?php endif; ?>
    <a class="btn btn-primary lab-card-link" href="<?= e(url('/labs/' . $lab['slug'])) ?>">
        <?= $completed ? 'Review Lab' : 'View Lab' ?>
    </a>
</article>
