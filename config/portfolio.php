<?php

declare(strict_types=1);

return [
    'max_featured_projects' => (int) env('PORTFOLIO_MAX_FEATURED', 3),
    'max_featured_skills' => (int) env('PORTFOLIO_MAX_FEATURED_SKILLS', 6),

    'default_sections' => [
        'about' => true,
        'skills' => true,
        'projects' => true,
        'challenges' => true,
        'writeups' => true,
        'community' => true,
        'education' => true,
        'experience' => true,
        'certifications' => true,
        'links' => true,
    ],

    'reserved_usernames' => [
        'admin', 'api', 'login', 'register', 'settings', 'skills', 'arena',
        'community', 'portfolio', 'projects', 'dashboard', 'moderation',
        'notifications', 'search', 'tag', 'thread', 'u', 'profile', 'assets',
    ],

    'project_images' => [
        'max_bytes' => 5 * 1024 * 1024,
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'blocked_extensions' => ['svg', 'php', 'phtml', 'js', 'html', 'htm'],
    ],

    'allowed_url_schemes' => ['http', 'https'],
];
