<?php
/** @var string $content */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'CySkillShare') ?></title>
    <?php \App\Core\View::partial('components/theme-boot'); ?>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-top">
            <a class="brand-lockup" href="<?= e(url('/')) ?>">
                <span class="brand-mark">Cy</span>
                <span class="brand-text">
                    <strong>CySkillShare</strong>
                    <span>KKU · CoC</span>
                </span>
            </a>
            <?php \App\Core\View::partial('components/theme-toggle'); ?>
        </div>
        <?php \App\Core\View::partial('components/flash-message'); ?>
        <?= $content ?>
    </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
