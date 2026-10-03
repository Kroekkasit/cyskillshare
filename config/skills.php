<?php

declare(strict_types=1);

/**
 * Skill Tree & Evidence configuration.
 * Levels are evidence-driven — these weights are NOT a global XP system.
 */
return [
    'difficulty_strength' => [
        'easy' => 1,
        'medium' => 2,
        'hard' => 3,
        'expert' => 4,
    ],

    // Internal evidence strength scale (1–5)
    'strength' => [
        'weak' => 1,
        'basic' => 2,
        'moderate' => 3,
        'strong' => 4,
        'verified' => 5,
    ],

    'evidence_types' => [
        'challenge_solved',
        'challenge_hard_solved',
        'writeup',
        'community_best_answer',
        'community_helpful_answer',
        'project',
        'lab',
        'event_participation',
        'instructor_verification',
        'manual',
    ],

    'source_types' => [
        'challenge',
        'writeup',
        'thread',
        'reply',
        'project',
        'lab',
        'event',
        'manual',
    ],

    // Roles allowed to verify pending evidence
    'verifier_roles' => ['instructor', 'mentor', 'admin'],

    // Roles allowed to manage skill tree definitions
    'manager_roles' => ['instructor', 'admin'],

    // Roles allowed to run global recalculation
    'recalculate_roles' => ['admin'],
];
