<?php

declare(strict_types=1);

/**
 * Cyber Arena configuration.
 * Semester dates are configurable — do not hardcode elsewhere.
 */
return [
    'flag_submit_max_per_minute' => (int) env('ARENA_FLAG_MAX_PER_MINUTE', 8),
    'hint_reveal_max_per_minute' => (int) env('ARENA_HINT_MAX_PER_MINUTE', 20),

    'challenge_files' => [
        'max_bytes' => (int) env('ARENA_FILE_MAX_BYTES', 10 * 1024 * 1024),
        // Allowed MIME types for staff-managed challenge attachments
        'allowed_mimes' => [
            'text/plain',
            'text/csv',
            'application/zip',
            'application/x-zip-compressed',
            'application/gzip',
            'application/x-gzip',
            'application/octet-stream',
            'application/json',
            'image/png',
            'image/jpeg',
            'image/gif',
            'image/webp',
            'application/pdf',
        ],
        // Allowed extensions (original name check; storage name is randomized)
        'allowed_extensions' => [
            'txt', 'csv', 'zip', 'gz', 'pcap', 'pcapng', 'png', 'jpg', 'jpeg',
            'gif', 'webp', 'pdf', 'bin', 'raw', 'log', 'json', 'xml', 'hex',
        ],
        'blocked_extensions' => [
            'php', 'phtml', 'php3', 'php4', 'php5', 'phar', 'htaccess', 'htpasswd',
            'sh', 'bash', 'cgi', 'exe', 'dll', 'so', 'bat', 'cmd', 'ps1', 'js', 'jsp',
        ],
    ],

    // Leaderboard “This Semester” window (Asia/Bangkok assumed via app timezone)
    'semester' => [
        'start' => (string) env('ARENA_SEMESTER_START', '2026-06-01'),
        'end' => (string) env('ARENA_SEMESTER_END', '2026-10-31'),
    ],

    // Channel slug used for challenge discussions
    'discussion_channel_slug' => 'ctf-general',

    // Tie-break for leaderboards: points DESC, solved_count DESC, earliest last_solve ASC
    'leaderboard_tie_break' => 'points_desc_solves_desc_earliest_solve_asc',
];
