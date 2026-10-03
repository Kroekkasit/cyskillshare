<?php
/**
 * @var array{done:list<string>,missing:list<string>} $suggestions
 */
?>
<section class="card quality-checklist">
    <h2>Quality Checklist</h2>
    <?php if ($suggestions['done'] !== []): ?>
        <div class="quality-done">
            <h3 class="small muted">Completed</h3>
            <ul class="skill-checklist">
                <?php foreach ($suggestions['done'] as $item): ?>
                    <li class="met">✓ <?= e($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($suggestions['missing'] !== []): ?>
        <div class="quality-missing">
            <h3 class="small muted">Suggestions</h3>
            <ul class="skill-checklist">
                <?php foreach ($suggestions['missing'] as $item): ?>
                    <li class="unmet">○ <?= e($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>
