<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'CySkillShare'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim((string) env('APP_URL', 'http://localhost'), '/'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Bangkok'),

    'session' => [
        'name' => env('SESSION_NAME', 'cyskillshare_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'secure' => filter_var(env('SESSION_SECURE', false), FILTER_VALIDATE_BOOLEAN),
        'same_site' => env('SESSION_SAME_SITE', 'Lax'),
        'http_only' => true,
    ],
];
