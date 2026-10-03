<?php

use App\Core\Session;

$success = Session::getFlash('success');
$error = Session::getFlash('error');
$loggedOut = isset($_GET['logged_out']);
?>
<?php if ($success): ?>
    <div class="flash flash-success"><?= e((string) $success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="flash flash-error"><?= e((string) $error) ?></div>
<?php endif; ?>
<?php if ($loggedOut): ?>
    <div class="flash flash-success">You have been logged out.</div>
<?php endif; ?>
