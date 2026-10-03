<?php
/**
 * @var array<string, mixed> $project
 * @var string|null $username
 */
$username = $username ?? (string) ($project['username'] ?? '');
$slug = (string) ($project['slug'] ?? '');
$verified = !empty($project['verified']);
$featured = !empty($project['featured']);
?>
<article class="project-card card portfolio-card">
    <header class="project-card-head">
        <h3>
            <a href="<?= e(url('/projects/' . $username . '/' . $slug)) ?>"><?= e((string) $project['title']) ?></a>
        </h3>
        <div class="project-card-badges">
            <?php if ($featured): ?>
                <span class="pill featured">★ Featured</span>
            <?php endif; ?>
            <?php if ($verified): ?>
                <span class="pill verified">✓ Verified</span>
            <?php endif; ?>
        </div>
    </header>
    <div class="project-card-meta muted">
        <span class="pill"><?= e(str_replace('_', ' ', (string) ($project['project_type'] ?? 'other'))) ?></span>
        <?php if ($username !== ''): ?>
            <a href="<?= e(url('/portfolio/' . $username)) ?>">@<?= e($username) ?></a>
        <?php endif; ?>
        <?php if (!empty($project['view_count'])): ?>
            <span><?= (int) $project['view_count'] ?> views</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($project['short_description'])): ?>
        <p class="project-card-excerpt muted"><?= e((string) $project['short_description']) ?></p>
    <?php endif; ?>
    <?php if (!empty($project['technologies'])): ?>
        <div class="tag-row project-tech-row">
            <?php foreach (array_slice($project['technologies'], 0, 6) as $tech): ?>
                <span class="tag-pill"><?= e((string) $tech) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($project['skills'])): ?>
        <div class="tag-row">
            <?php foreach (array_slice($project['skills'], 0, 4) as $sk): ?>
                <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a class="btn btn-primary project-card-link" href="<?= e(url('/projects/' . $username . '/' . $slug)) ?>">View Project</a>
</article>
