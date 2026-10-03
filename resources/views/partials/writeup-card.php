<?php
/**
 * @var array<string, mixed> $writeup
 */
$username = (string) ($writeup['username'] ?? '');
$slug = (string) ($writeup['slug'] ?? '');
?>
<article class="writeup-card card knowledge-card">
    <header class="writeup-card-head">
        <h3>
            <a href="<?= e(url('/writeups/' . $username . '/' . $slug)) ?>"><?= e((string) $writeup['title']) ?></a>
        </h3>
        <?php if (!empty($writeup['featured'])): ?>
            <span class="pill featured">★ Featured</span>
        <?php endif; ?>
    </header>
    <div class="article-meta muted">
        <?php if (!empty($writeup['category_name'])): ?>
            <a href="<?= e(url('/writeups?category=' . $writeup['category_slug'])) ?>"><?= e((string) $writeup['category_name']) ?></a>
            ·
        <?php endif; ?>
        <a href="<?= e(url('/portfolio/' . $username)) ?>">@<?= e($username) ?></a>
        · <span class="pill"><?= e((string) ($writeup['difficulty'] ?? 'beginner')) ?></span>
        <?php if (!empty($writeup['reading_time'])): ?>
            · <?= (int) $writeup['reading_time'] ?> min read
        <?php endif; ?>
    </div>
    <?php if (!empty($writeup['short_description'])): ?>
        <p class="writeup-card-excerpt muted"><?= e((string) $writeup['short_description']) ?></p>
    <?php endif; ?>
    <div class="writeup-card-stats muted small">
        <?= (int) ($writeup['view_count'] ?? 0) ?> views
        · <?= (int) ($writeup['helpful_count'] ?? 0) ?> helpful
        <?php if (!empty($writeup['published_at'])): ?>
            · <?= e(time_ago((string) $writeup['published_at'])) ?>
        <?php endif; ?>
    </div>
    <a class="btn btn-primary writeup-card-link" href="<?= e(url('/writeups/' . $username . '/' . $slug)) ?>">Read Writeup</a>
</article>
