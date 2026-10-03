-- CySkillShare Phase 5: Portfolio & Project Showcase
-- Load after skills schema. Extends project_skills stub from Phase 4.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `portfolios` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `headline` VARCHAR(200) NULL,
  `about` MEDIUMTEXT NULL,
  `university` VARCHAR(150) NULL,
  `program` VARCHAR(150) NULL,
  `graduation_year` SMALLINT UNSIGNED NULL,
  `location` VARCHAR(120) NULL,
  `show_location` TINYINT(1) NOT NULL DEFAULT 0,
  `show_email` TINYINT(1) NOT NULL DEFAULT 0,
  `github_url` VARCHAR(255) NULL,
  `linkedin_url` VARCHAR(255) NULL,
  `website_url` VARCHAR(255) NULL,
  `resume_url` VARCHAR(255) NULL,
  `theme` VARCHAR(50) NOT NULL DEFAULT 'dark_cyber_academy',
  `featured_project_limit` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `sections_json` JSON NULL,
  `show_challenge_stats` TINYINT(1) NOT NULL DEFAULT 1,
  `show_skill_evidence` TINYINT(1) NOT NULL DEFAULT 1,
  `show_community_stats` TINYINT(1) NOT NULL DEFAULT 1,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolios_user_id_unique` (`user_id`),
  KEY `portfolios_visibility_index` (`visibility`),
  CONSTRAINT `portfolios_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `short_description` VARCHAR(500) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `project_type` ENUM(
    'security_tool','web_security','network_security','digital_forensics',
    'malware_analysis','reverse_engineering','ctf','automation','research',
    'academic','open_source','home_lab','other'
  ) NOT NULL DEFAULT 'other',
  `status` ENUM('planning','in_progress','completed','archived') NOT NULL DEFAULT 'planning',
  `publish_status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `repository_url` VARCHAR(255) NULL,
  `demo_url` VARCHAR(255) NULL,
  `documentation_url` VARCHAR(255) NULL,
  `thumbnail` VARCHAR(255) NULL,
  `start_date` DATE NULL,
  `end_date` DATE NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `display_order` INT NOT NULL DEFAULT 0,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_user_slug_unique` (`user_id`, `slug`),
  KEY `projects_visibility_index` (`visibility`),
  KEY `projects_featured_index` (`featured`),
  KEY `projects_publish_status_index` (`publish_status`),
  KEY `projects_status_index` (`status`),
  KEY `projects_created_at_index` (`created_at`),
  CONSTRAINT `projects_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhance Phase-4 stub project_skills
SET @col := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'project_skills' AND COLUMN_NAME = 'importance'
);
SET @sql := IF(@col = 0,
  'ALTER TABLE `project_skills` ADD COLUMN `importance` ENUM(''primary'',''secondary'',''supporting'') NOT NULL DEFAULT ''secondary'' AFTER `skill_id`, ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `project_technologies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `technology` VARCHAR(80) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_technologies_unique` (`project_id`, `technology`),
  KEY `project_technologies_project_id_index` (`project_id`),
  CONSTRAINT `project_technologies_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_images` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `storage_path` VARCHAR(500) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(120) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL DEFAULT 0,
  `caption` VARCHAR(255) NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_images_project_id_index` (`project_id`),
  CONSTRAINT `project_images_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_verifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `verified_by` BIGINT UNSIGNED NULL,
  `requested_by` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verification_note` VARCHAR(500) NULL,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `project_verifications_project_id_index` (`project_id`),
  KEY `project_verifications_status_index` (`status`),
  CONSTRAINT `project_verifications_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_verifications_verified_by_fk`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_verifications_requested_by_fk`
    FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_challenges` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `relationship_type` ENUM('inspired_by','built_after','related_to') NOT NULL DEFAULT 'related_to',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_challenges_unique` (`project_id`, `challenge_id`),
  KEY `project_challenges_challenge_id_index` (`challenge_id`),
  CONSTRAINT `project_challenges_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_challenges_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_writeups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_writeups_unique` (`project_id`, `writeup_id`),
  KEY `project_writeups_writeup_id_index` (`writeup_id`),
  CONSTRAINT `project_writeups_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_writeups_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_labs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_labs_unique` (`project_id`, `lab_id`),
  KEY `project_labs_lab_id_index` (`lab_id`),
  CONSTRAINT `project_labs_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `project_reactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `reaction_type` ENUM('helpful','interesting','impressive') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_reactions_unique` (`project_id`, `user_id`, `reaction_type`),
  KEY `project_reactions_user_id_index` (`user_id`),
  CONSTRAINT `project_reactions_project_id_fk`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_reactions_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_education` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `institution` VARCHAR(200) NOT NULL,
  `program` VARCHAR(200) NULL,
  `field` VARCHAR(200) NULL,
  `start_year` SMALLINT UNSIGNED NULL,
  `end_year` SMALLINT UNSIGNED NULL,
  `description` TEXT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `portfolio_education_user_id_index` (`user_id`),
  CONSTRAINT `portfolio_education_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_experience` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `organization` VARCHAR(200) NOT NULL,
  `role` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `start_date` DATE NULL,
  `end_date` DATE NULL,
  `is_current` TINYINT(1) NOT NULL DEFAULT 0,
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `display_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `portfolio_experience_user_id_index` (`user_id`),
  CONSTRAINT `portfolio_experience_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_certifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `issuer` VARCHAR(200) NULL,
  `credential_id` VARCHAR(120) NULL,
  `credential_url` VARCHAR(255) NULL,
  `issued_date` DATE NULL,
  `expiration_date` DATE NULL,
  `description` TEXT NULL,
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `portfolio_certifications_user_id_index` (`user_id`),
  CONSTRAINT `portfolio_certifications_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_featured_skills` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `skill_id`),
  KEY `portfolio_featured_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `portfolio_featured_skills_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `portfolio_featured_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_analytics_daily` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `metric` VARCHAR(50) NOT NULL,
  `metric_date` DATE NOT NULL,
  `count` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolio_analytics_unique` (`user_id`, `metric`, `metric_date`),
  CONSTRAINT `portfolio_analytics_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
