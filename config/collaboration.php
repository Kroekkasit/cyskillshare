<?php

declare(strict_types=1);

/**
 * Mentorship, study groups, CTF teams & collaboration settings.
 */
return [
    'manager_roles' => ['instructor', 'moderator', 'admin'],
    'mentor_verifier_roles' => ['instructor', 'admin'],

    'max_open_groups_per_user' => (int) env('COLLAB_MAX_GROUPS_OWNED', 10),
    'max_group_members' => (int) env('COLLAB_MAX_GROUP_MEMBERS', 50),
    'default_max_mentees' => (int) env('COLLAB_DEFAULT_MAX_MENTEES', 3),
    'invitation_ttl_hours' => (int) env('COLLAB_INVITE_TTL_HOURS', 72),

    'mentorship_request_max_per_day' => (int) env('COLLAB_MENTOR_REQ_PER_DAY', 5),
    'group_invite_max_per_hour' => (int) env('COLLAB_INVITE_PER_HOUR', 20),
    'join_request_max_per_hour' => (int) env('COLLAB_JOIN_REQ_PER_HOUR', 10),
    'recruitment_max_per_day' => (int) env('COLLAB_RECRUIT_PER_DAY', 5),
    'people_search_max_per_minute' => (int) env('COLLAB_PEOPLE_SEARCH_PER_MIN', 30),

    // Minimum demonstrated skill level (user_skills.current_level) to enable mentor mode
    'mentor_min_skill_level' => (int) env('COLLAB_MENTOR_MIN_LEVEL', 2),
];
