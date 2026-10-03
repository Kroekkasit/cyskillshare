<?php
/** @var string $message */
?>
<section class="hero">
    <h1>403 Forbidden</h1>
    <p class="subtitle"><?= e($message ?? 'Access denied.') ?></p>
    <p><a href="<?= e(url('/')) ?>">Back home</a></p>
</section>
