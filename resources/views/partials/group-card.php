<?php
/**
 * @var array<string, mixed> $group
 * @var string $basePath
 */
$basePath = $basePath ?? '/groups';
$slug = (string) ($group['slug'] ?? '');
$type = (string) ($group['group_type'] ?? 'general');
$typeLabel = match ($type) {
    'ctf' => 'CTF Team',
    'project' => 'Project Team',
    'study' => 'Study Group',
    default => ucfirst($type),
};
?>
<article class="group-card card collab-card">
    <header class="group-card-head">
        <h3>
            <a href="<?= e(url($basePath . '/' . $slug)) ?>"><?= e((string) ($group['name'] ?? '')) ?></a>
        </h3>
        <span class="pill group-type-pill"><?= e($typeLabel) ?></span>
    </header>
    <div class="group-card-meta muted">
        <span>@<?= e((string) ($group['owner_username'] ?? '')) ?></span>
        <span><?= (int) ($group['member_count'] ?? 0) ?> members</span>
        <?php if (!empty($group['featured'])): ?>
            <span class="pill featured">★ Featured</span>
        <?php endif; ?>
    </div>
    <?php if (!empty($group['description'])): ?>
        <p class="group-card-excerpt muted"><?= e(mb_strimwidth((string) $group['description'], 0, 160, '…')) ?></p>
    <?php endif; ?>
    <?php if (!empty($group['reasons']) && is_array($group['reasons'])): ?>
        <?php \App\Core\View::partial('partials/reason-list', ['reasons' => $group['reasons']]); ?>
    <?php endif; ?>
    <a class="btn btn-primary group-card-link" href="<?= e(url($basePath . '/' . $slug)) ?>">View</a>
</article>
