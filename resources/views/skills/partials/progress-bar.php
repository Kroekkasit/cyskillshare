<?php
/**
 * @var int $progress 0–100
 * @var string $label
 * @var string|null $class
 */
$progress = max(0, min(100, (int) ($progress ?? 0)));
$class = $class ?? 'skill-progress';
?>
<div class="<?= e($class) ?>">
    <?php if ($label !== ''): ?>
    <div class="skill-progress-label">
        <span><?= e($label) ?></span>
        <span class="muted"><?= $progress ?>%</span>
    </div>
    <?php endif; ?>
    <div class="skill-progress-track" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= e($label !== '' ? $label : 'Progress') ?>">
        <div class="skill-progress-fill" style="width: <?= $progress ?>%"></div>
    </div>
</div>
