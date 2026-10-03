<?php
/** @var string $message */
?>
<section class="hero">
    <h1>500</h1>
    <p class="subtitle"><?= e($message ?? 'Server error.') ?></p>
    <p><a href="<?= e(url('/')) ?>">Back home</a></p>
</section>
