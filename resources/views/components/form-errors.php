<?php

use App\Core\Session;

/** @var array<string, list<string>>|list<string>|null $errors */
$errors = Session::getFlash('errors', []);
if (!is_array($errors) || $errors === []) {
    return;
}

$flat = [];
foreach ($errors as $key => $value) {
    if (is_array($value)) {
        foreach ($value as $msg) {
            $flat[] = (string) $msg;
        }
    } else {
        $flat[] = (string) $value;
    }
}
?>
<?php if ($flat !== []): ?>
    <ul class="errors">
        <?php foreach ($flat as $message): ?>
            <li><?= e($message) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
