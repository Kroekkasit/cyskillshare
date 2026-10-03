<?php
/** @var string $message */
?>
<section class="hero">
    <h1>404</h1>
    <p class="subtitle"><?= e($message ?? 'Page not found.') ?></p>
    <p><a href="<?= e(url('/')) ?>">Back home</a></p>
</section>
