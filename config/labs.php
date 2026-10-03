<?php

declare(strict_types=1);

/**
 * Cyber Labs configuration.
 * Orchestrator credentials must never be exposed to browsers.
 */
return [
    'max_active_instances_per_user' => (int) env('LABS_MAX_ACTIVE_PER_USER', 2),
    'max_total_active_instances' => (int) env('LABS_MAX_TOTAL_ACTIVE', 100),
    'default_lifetime_minutes' => (int) env('LABS_DEFAULT_LIFETIME_MINUTES', 60),
    'max_lifetime_minutes' => (int) env('LABS_MAX_LIFETIME_MINUTES', 180),
    'provision_poll_seconds' => (int) env('LABS_PROVISION_POLL_SECONDS', 2),
    'simulated_ready_after_seconds' => (int) env('LABS_SIMULATED_READY_AFTER', 3),

    'start_max_per_hour' => (int) env('LABS_START_MAX_PER_HOUR', 10),
    'reset_max_per_hour' => (int) env('LABS_RESET_MAX_PER_HOUR', 6),
    'submit_max_per_minute' => (int) env('LABS_SUBMIT_MAX_PER_MINUTE', 12),
    'hint_max_per_minute' => (int) env('LABS_HINT_MAX_PER_MINUTE', 20),

    'manager_roles' => ['instructor', 'moderator', 'admin'],
    'infra_roles' => ['admin'], // template / resource limit infrastructure settings

    // Orchestrator driver: "simulated" (default) | future "http"
    'orchestrator' => (string) env('LABS_ORCHESTRATOR', 'simulated'),
    'orchestrator_base_url' => (string) env('LABS_ORCHESTRATOR_URL', ''),
    'orchestrator_token' => (string) env('LABS_ORCHESTRATOR_TOKEN', ''),

    'default_resources' => [
        'cpu' => 1.0,
        'memory_mb' => 512,
        'disk_mb' => 1024,
        'max_processes' => 128,
    ],
];
