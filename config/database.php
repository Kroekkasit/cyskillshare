<?php

declare(strict_types=1);

return [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => (int) env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'cyskillshare'),
    // Never default to root — app user must follow least privilege.
    'username' => env('DB_USERNAME', 'cyskillshare'),
    'password' => env('DB_PASSWORD', 'cyskillshare'),
    'socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];
