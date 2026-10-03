<?php
declare(strict_types=1);

// Router script for PHP built-in development server.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false; // serve static asset as-is
}

require __DIR__ . '/index.php';
