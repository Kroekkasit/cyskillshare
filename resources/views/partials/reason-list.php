<?php
/**
 * @var list<string> $reasons
 */
if (empty($reasons)) {
    return;
}
?>
<ul class="reason-chips" aria-label="Why recommended">
    <?php foreach ($reasons as $reason): ?>
        <li class="reason-chip"><?= e((string) $reason) ?></li>
    <?php endforeach; ?>
</ul>
