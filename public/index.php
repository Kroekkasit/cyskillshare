<?php

declare(strict_types=1);

/**
 * Front controller — all public requests enter here.
 */

$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->run();
