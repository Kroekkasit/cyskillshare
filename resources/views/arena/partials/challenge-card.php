<?php
/**
 * @var array<string, mixed> $challenge
 */
$solved = !empty($challenge['solved']);
$difficulty = (string) ($challenge['difficulty'] ?? 'easy');
?>
<article class="challenge-card card">
    <header class="challenge-card-head">
        <h3><a href="<?= e(url('/arena/challenges/' . (int) $challenge['id'])) ?>"><?= e($challenge['title']) ?></a></h3>
        <?php if ($solved): ?>
            <span class="pill solved" aria-label="Solved">✓</span>
        <?php endif; ?>
        <?php if (!empty($challenge['is_featured'])): ?>
            <span class="pill featured">★ Featured</span>
        <?php endif; ?>
    </header>
    <div class="challenge-card-meta">
        <span class="difficulty-badge difficulty-<?= e($difficulty) ?>"><?= e(ucfirst($difficulty)) ?></span>
        <span class="muted"><?= (int) ($challenge['points'] ?? 0) ?> pts</span>
        <?php if (!empty($challenge['category_name'])): ?>
            <span class="muted"><?= e($challenge['category_name']) ?></span>
        <?php endif; ?>
        <?php if (isset($challenge['solve_count'])): ?>
            <span class="muted"><?= (int) $challenge['solve_count'] ?> solves</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($challenge['excerpt'])): ?>
        <p class="muted challenge-card-excerpt"><?= e($challenge['excerpt']) ?></p>
    <?php endif; ?>
    <?php if (!empty($challenge['tags'])): ?>
        <div class="tag-row">
            <?php foreach ($challenge['tags'] as $tag): ?>
                <?php \App\Core\View::partial('components/tag', ['tag' => $tag]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a class="btn btn-primary challenge-card-link" href="<?= e(url('/arena/challenges/' . (int) $challenge['id'])) ?>">
        <?= $solved ? 'Review' : 'Start Challenge' ?>
    </a>
</article>
