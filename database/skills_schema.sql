-- CySkillShare Phase 4: Skill Tree & Evidence System
-- CREATE TABLE fragment for fresh installs (after Arena schema).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `skill_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `icon` VARCHAR(50) NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skill_categories_slug_unique` (`slug`),
  KEY `skill_categories_display_order_index` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `parent_skill_id` INT UNSIGNED NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(140) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `icon` VARCHAR(50) NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_gated` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skills_slug_unique` (`slug`),
  KEY `skills_category_id_index` (`category_id`),
  KEY `skills_parent_skill_id_index` (`parent_skill_id`),
  KEY `skills_display_order_index` (`display_order`),
  CONSTRAINT `skills_category_id_fk`
    FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `skills_parent_skill_id_fk`
    FOREIGN KEY (`parent_skill_id`) REFERENCES `skills` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skill_levels` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` TINYINT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NULL,
  `minimum_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `display_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skill_levels_level_unique` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skill_requirements` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `skill_id` INT UNSIGNED NOT NULL,
  `target_level` TINYINT UNSIGNED NOT NULL,
  `evidence_type` VARCHAR(50) NOT NULL,
  `minimum_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `minimum_difficulty` ENUM('easy','medium','hard','expert') NULL,
  `is_required` TINYINT(1) NOT NULL DEFAULT 1,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `skill_requirements_skill_id_index` (`skill_id`),
  KEY `skill_requirements_target_level_index` (`target_level`),
  CONSTRAINT `skill_requirements_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skill_prerequisites` (
  `skill_id` INT UNSIGNED NOT NULL,
  `prerequisite_skill_id` INT UNSIGNED NOT NULL,
  `minimum_level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`skill_id`, `prerequisite_skill_id`),
  KEY `skill_prerequisites_prereq_index` (`prerequisite_skill_id`),
  CONSTRAINT `skill_prerequisites_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  CONSTRAINT `skill_prerequisites_prereq_fk`
    FOREIGN KEY (`prerequisite_skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `challenge_skills` (
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`challenge_id`, `skill_id`),
  KEY `challenge_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `challenge_skills_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `challenge_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `thread_skills` (
  `thread_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`thread_id`, `skill_id`),
  KEY `thread_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `thread_skills_thread_id_fk`
    FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `thread_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Future portfolio / labs hooks (no UI in this phase)
CREATE TABLE IF NOT EXISTS `project_skills` (
  `project_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`project_id`, `skill_id`),
  KEY `project_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `project_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_skills` (
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`lab_id`, `skill_id`),
  KEY `lab_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `lab_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `skill_evidence` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `evidence_type` VARCHAR(50) NOT NULL,
  `source_type` VARCHAR(50) NOT NULL,
  `source_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `strength` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `status` ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `verified_by` BIGINT UNSIGNED NULL,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `verification_note` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `skill_evidence_unique_source` (`user_id`, `source_type`, `source_id`, `skill_id`),
  KEY `skill_evidence_user_id_index` (`user_id`),
  KEY `skill_evidence_skill_id_index` (`skill_id`),
  KEY `skill_evidence_status_index` (`status`),
  KEY `skill_evidence_evidence_type_index` (`evidence_type`),
  KEY `skill_evidence_created_at_index` (`created_at`),
  CONSTRAINT `skill_evidence_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `skill_evidence_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE,
  CONSTRAINT `skill_evidence_verified_by_fk`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_skills` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `current_level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `progress_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `evidence_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_activity_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_skills_user_skill_unique` (`user_id`, `skill_id`),
  KEY `user_skills_skill_id_index` (`skill_id`),
  KEY `user_skills_current_level_index` (`current_level`),
  CONSTRAINT `user_skills_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Privacy column on users (idempotent)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'skills_visibility'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `users` ADD COLUMN `skills_visibility` ENUM(''public'',''community'',''private'') NOT NULL DEFAULT ''public'' AFTER `bio`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
