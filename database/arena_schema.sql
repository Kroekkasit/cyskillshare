-- CySkillShare Phase 3: Cyber Arena schema fragment
-- CREATE TABLE statements only — combine with schema.sql for fresh installs.
-- Requires existing tables: users, tags, threads (threads.challenge_id via migration or extended schema).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- challenge_categories
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `icon` VARCHAR(50) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenge_categories_slug_unique` (`slug`),
  KEY `challenge_categories_sort_order_index` (`sort_order`),
  KEY `challenge_categories_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenges
-- ---------------------------------------------------------------------------
CREATE TABLE `challenges` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `difficulty` ENUM('easy', 'medium', 'hard', 'expert') NOT NULL,
  `points` INT UNSIGNED NOT NULL DEFAULT 100,
  `author_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
  `flag_type` ENUM('static') NOT NULL DEFAULT 'static',
  `flag_hash` CHAR(64) NOT NULL,
  `case_sensitive` TINYINT(1) NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `first_solved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenges_slug_unique` (`slug`),
  KEY `challenges_category_id_index` (`category_id`),
  KEY `challenges_status_index` (`status`),
  KEY `challenges_difficulty_index` (`difficulty`),
  KEY `challenges_created_at_index` (`created_at`),
  KEY `challenges_is_featured_index` (`is_featured`),
  KEY `challenges_is_active_index` (`is_active`),
  KEY `challenges_author_id_index` (`author_id`),
  CONSTRAINT `challenges_category_id_fk`
    FOREIGN KEY (`category_id`) REFERENCES `challenge_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `challenges_author_id_fk`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_tags
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_tags` (
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`challenge_id`, `tag_id`),
  KEY `challenge_tags_tag_id_index` (`tag_id`),
  CONSTRAINT `challenge_tags_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_tags_tag_id_fk`
    FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_hints
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_hints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `hint_order` INT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `point_penalty` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `challenge_hints_challenge_id_index` (`challenge_id`),
  KEY `challenge_hints_challenge_order_index` (`challenge_id`, `hint_order`),
  CONSTRAINT `challenge_hints_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_hint_usage
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_hint_usage` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `hint_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `penalty_applied` INT UNSIGNED NOT NULL,
  `revealed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenge_hint_usage_user_hint_unique` (`user_id`, `hint_id`),
  KEY `challenge_hint_usage_challenge_id_index` (`challenge_id`),
  KEY `challenge_hint_usage_hint_id_index` (`hint_id`),
  KEY `challenge_hint_usage_user_id_index` (`user_id`),
  CONSTRAINT `challenge_hint_usage_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_hint_usage_hint_id_fk`
    FOREIGN KEY (`hint_id`) REFERENCES `challenge_hints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_hint_usage_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_submissions
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_submissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `submitted_flag_hash` CHAR(64) NOT NULL,
  `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
  `points_awarded` INT NOT NULL DEFAULT 0,
  `attempt_number` INT UNSIGNED NOT NULL DEFAULT 1,
  `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `challenge_submissions_challenge_id_index` (`challenge_id`),
  KEY `challenge_submissions_user_id_index` (`user_id`),
  KEY `challenge_submissions_submitted_at_index` (`submitted_at`),
  KEY `challenge_submissions_user_challenge_index` (`user_id`, `challenge_id`),
  CONSTRAINT `challenge_submissions_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_submissions_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_solves
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_solves` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `points_awarded` INT UNSIGNED NOT NULL,
  `hints_used` INT UNSIGNED NOT NULL DEFAULT 0,
  `solved_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `challenge_solves_challenge_user_unique` (`challenge_id`, `user_id`),
  KEY `challenge_solves_user_id_index` (`user_id`),
  KEY `challenge_solves_solved_at_index` (`solved_at`),
  CONSTRAINT `challenge_solves_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_solves_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- challenge_files
-- ---------------------------------------------------------------------------
CREATE TABLE `challenge_files` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `storage_path` VARCHAR(500) NOT NULL,
  `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `mime_type` VARCHAR(120) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `challenge_files_challenge_id_index` (`challenge_id`),
  CONSTRAINT `challenge_files_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- arena_events
-- ---------------------------------------------------------------------------
CREATE TABLE `arena_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `description` MEDIUMTEXT NULL,
  `event_type` ENUM('practice', 'ctf', 'competition', 'workshop') NOT NULL,
  `status` ENUM('draft', 'upcoming', 'active', 'ended', 'archived') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public', 'private') NOT NULL DEFAULT 'public',
  `start_at` TIMESTAMP NULL DEFAULT NULL,
  `end_at` TIMESTAMP NULL DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `arena_events_slug_unique` (`slug`),
  KEY `arena_events_status_index` (`status`),
  KEY `arena_events_start_at_index` (`start_at`),
  KEY `arena_events_end_at_index` (`end_at`),
  KEY `arena_events_created_by_index` (`created_by`),
  CONSTRAINT `arena_events_created_by_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- arena_event_challenges
-- ---------------------------------------------------------------------------
CREATE TABLE `arena_event_challenges` (
  `event_id` BIGINT UNSIGNED NOT NULL,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`event_id`, `challenge_id`),
  KEY `arena_event_challenges_challenge_id_index` (`challenge_id`),
  CONSTRAINT `arena_event_challenges_event_id_fk`
    FOREIGN KEY (`event_id`) REFERENCES `arena_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `arena_event_challenges_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- arena_point_transactions
-- ---------------------------------------------------------------------------
CREATE TABLE `arena_point_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `challenge_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `event_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `hint_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `points` INT NOT NULL,
  `reason` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `arena_point_transactions_user_id_index` (`user_id`),
  KEY `arena_point_transactions_created_at_index` (`created_at`),
  KEY `arena_point_transactions_challenge_id_index` (`challenge_id`),
  KEY `arena_point_transactions_event_id_index` (`event_id`),
  KEY `arena_point_transactions_hint_id_index` (`hint_id`),
  CONSTRAINT `arena_point_transactions_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `arena_point_transactions_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE SET NULL,
  CONSTRAINT `arena_point_transactions_event_id_fk`
    FOREIGN KEY (`event_id`) REFERENCES `arena_events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `arena_point_transactions_hint_id_fk`
    FOREIGN KEY (`hint_id`) REFERENCES `challenge_hints` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- writeups (full schema — Phase 6; challenge_id optional via writeup_challenges)
-- ---------------------------------------------------------------------------
CREATE TABLE `writeup_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `writeup_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `writeups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `short_description` VARCHAR(500) NULL,
  `content` MEDIUMTEXT NOT NULL,
  `content_format` ENUM('markdown') NOT NULL DEFAULT 'markdown',
  `difficulty` ENUM('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner',
  `status` ENUM('draft','published','archived','under_review') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `cover_image` VARCHAR(255) NULL,
  `reading_time` INT UNSIGNED NOT NULL DEFAULT 1,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `helpful_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `writeups_user_slug_unique` (`user_id`, `slug`),
  KEY `writeups_user_id_index` (`user_id`),
  KEY `writeups_category_id_index` (`category_id`),
  KEY `writeups_status_index` (`status`),
  KEY `writeups_visibility_index` (`visibility`),
  KEY `writeups_featured_index` (`featured`),
  KEY `writeups_published_at_index` (`published_at`),
  KEY `writeups_deleted_at_index` (`deleted_at`),
  FULLTEXT KEY `writeups_ft_search` (`title`, `short_description`, `content`),
  CONSTRAINT `writeups_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeups_category_id_fk`
    FOREIGN KEY (`category_id`) REFERENCES `writeup_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
