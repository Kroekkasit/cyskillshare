<?php
/** @var string $content */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Resume') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="print-layout">
<?= $content ?>
<script>
document.querySelector('[data-print]')?.addEventListener('click', function () { window.print(); });
</script>
</body>
</html>
