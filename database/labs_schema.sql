-- CySkillShare Phase 7: Cyber Labs
-- Requires: users, skills, lab_skills stub, writeup_labs stub, project_labs stub

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `lab_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `icon` VARCHAR(40) NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_templates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(140) NOT NULL,
  `description` TEXT NULL,
  `runtime_type` VARCHAR(60) NOT NULL DEFAULT 'simulated_web',
  `configuration` JSON NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_templates_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `labs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `short_description` VARCHAR(500) NULL,
  `description` MEDIUMTEXT NOT NULL,
  `learning_objectives` MEDIUMTEXT NULL,
  `category_id` INT UNSIGNED NULL,
  `template_id` INT UNSIGNED NULL,
  `difficulty` ENUM('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner',
  `estimated_minutes` INT UNSIGNED NOT NULL DEFAULT 45,
  `status` ENUM('draft','review','published','archived') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `author_id` BIGINT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `thumbnail` VARCHAR(255) NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_pause` TINYINT(1) NOT NULL DEFAULT 0,
  `reset_task_progress` TINYINT(1) NOT NULL DEFAULT 1,
  `prerequisite_mode` ENUM('recommended','required') NOT NULL DEFAULT 'recommended',
  `lifetime_minutes` INT UNSIGNED NOT NULL DEFAULT 60,
  `cpu_limit` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  `memory_mb` INT UNSIGNED NOT NULL DEFAULT 512,
  `disk_mb` INT UNSIGNED NOT NULL DEFAULT 1024,
  `allow_internet` TINYINT(1) NOT NULL DEFAULT 0,
  `max_points` INT UNSIGNED NOT NULL DEFAULT 100,
  `environment_type` VARCHAR(60) NOT NULL DEFAULT 'browser',
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `start_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `completion_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `labs_slug_unique` (`slug`),
  KEY `labs_category_id_index` (`category_id`),
  KEY `labs_status_index` (`status`),
  KEY `labs_visibility_index` (`visibility`),
  KEY `labs_featured_index` (`featured`),
  KEY `labs_author_id_index` (`author_id`),
  FULLTEXT KEY `labs_ft_search` (`title`, `short_description`, `description`),
  CONSTRAINT `labs_category_id_fk`
    FOREIGN KEY (`category_id`) REFERENCES `lab_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `labs_template_id_fk`
    FOREIGN KEY (`template_id`) REFERENCES `lab_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `labs_author_id_fk`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attach FK / created_at to existing stub lab_skills (skills_schema)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lab_skills' AND COLUMN_NAME = 'created_at'
);
SET @sql := IF(@col_exists = 0,
  'ALTER TABLE `lab_skills` ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lab_skills' AND CONSTRAINT_NAME = 'lab_skills_lab_id_fk'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE `lab_skills`
     ADD CONSTRAINT `lab_skills_lab_id_fk`
       FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `lab_prerequisites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `minimum_level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_prerequisites_unique` (`lab_id`, `skill_id`),
  KEY `lab_prerequisites_skill_id_index` (`skill_id`),
  CONSTRAINT `lab_prerequisites_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_prerequisites_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_services` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `service_type` VARCHAR(60) NOT NULL,
  `template` VARCHAR(120) NULL,
  `internal_port` INT UNSIGNED NULL,
  `display_port` INT UNSIGNED NULL,
  `protocol` VARCHAR(20) NOT NULL DEFAULT 'http',
  `environment_config` JSON NULL,
  `is_student_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `display_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lab_services_lab_id_index` (`lab_id`),
  CONSTRAINT `lab_services_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `task_type` ENUM(
    'question','flag','command_output','multiple_choice','file_analysis',
    'log_analysis','configuration','investigation','report','manual_verification'
  ) NOT NULL DEFAULT 'question',
  `display_order` INT NOT NULL DEFAULT 0,
  `required` TINYINT(1) NOT NULL DEFAULT 1,
  `points` INT UNSIGNED NOT NULL DEFAULT 10,
  `options_json` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_tasks_lab_slug_unique` (`lab_id`, `slug`),
  KEY `lab_tasks_lab_order_index` (`lab_id`, `display_order`),
  CONSTRAINT `lab_tasks_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_task_dependencies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `depends_on_task_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_task_dependencies_unique` (`task_id`, `depends_on_task_id`),
  KEY `lab_task_dependencies_depends_index` (`depends_on_task_id`),
  CONSTRAINT `lab_task_dependencies_task_id_fk`
    FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_task_dependencies_depends_fk`
    FOREIGN KEY (`depends_on_task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Staff-only validation secrets — never select into student-facing queries
CREATE TABLE IF NOT EXISTS `lab_task_validations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `validation_type` ENUM('exact','regex','flag','instance_secret','multiple_choice','manual') NOT NULL,
  `validation_config` JSON NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_task_validations_task_unique` (`task_id`),
  CONSTRAINT `lab_task_validations_task_id_fk`
    FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_task_hints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(160) NOT NULL,
  `content` TEXT NOT NULL,
  `hint_level` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `penalty` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lab_task_hints_task_id_index` (`task_id`),
  CONSTRAINT `lab_task_hints_task_id_fk`
    FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_instances` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM(
    'queued','provisioning','running','paused','stopping','stopped','expired','failed','destroyed','completed'
  ) NOT NULL DEFAULT 'queued',
  `provision_state` ENUM('queued','provisioning','ready','failed') NOT NULL DEFAULT 'queued',
  `instance_identifier` VARCHAR(64) NOT NULL,
  `runtime_secrets` JSON NULL,
  `access_token` VARCHAR(64) NOT NULL,
  `orchestrator_ref` VARCHAR(120) NULL,
  `failure_category` VARCHAR(80) NULL,
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `ready_at` TIMESTAMP NULL DEFAULT NULL,
  `last_activity_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `paused_at` TIMESTAMP NULL DEFAULT NULL,
  `stopped_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `score` INT UNSIGNED NULL,
  `hints_used` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_instances_identifier_unique` (`instance_identifier`),
  KEY `lab_instances_user_id_index` (`user_id`),
  KEY `lab_instances_lab_id_index` (`lab_id`),
  KEY `lab_instances_status_index` (`status`),
  KEY `lab_instances_expires_at_index` (`expires_at`),
  CONSTRAINT `lab_instances_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_instances_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_progress` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` BIGINT UNSIGNED NOT NULL,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('locked','available','in_progress','completed') NOT NULL DEFAULT 'locked',
  `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
  `best_score` INT UNSIGNED NOT NULL DEFAULT 0,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_progress_unique` (`instance_id`, `task_id`),
  KEY `lab_progress_task_id_index` (`task_id`),
  CONSTRAINT `lab_progress_instance_id_fk`
    FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_progress_task_id_fk`
    FOREIGN KEY (`task_id`) REFERENCES `lab_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_attempts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `progress_id` BIGINT UNSIGNED NOT NULL,
  `answer_hash` CHAR(64) NULL,
  `result` ENUM('correct','incorrect','partial','manual_review') NOT NULL,
  `score` INT UNSIGNED NOT NULL DEFAULT 0,
  `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lab_attempts_progress_id_index` (`progress_id`),
  KEY `lab_attempts_submitted_at_index` (`submitted_at`),
  CONSTRAINT `lab_attempts_progress_id_fk`
    FOREIGN KEY (`progress_id`) REFERENCES `lab_progress` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_hint_usage` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance_id` BIGINT UNSIGNED NOT NULL,
  `hint_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `penalty_applied` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_hint_usage_unique` (`instance_id`, `hint_id`),
  KEY `lab_hint_usage_user_id_index` (`user_id`),
  CONSTRAINT `lab_hint_usage_instance_id_fk`
    FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_hint_usage_hint_id_fk`
    FOREIGN KEY (`hint_id`) REFERENCES `lab_task_hints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_hint_usage_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_completions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `instance_id` BIGINT UNSIGNED NOT NULL,
  `score` INT UNSIGNED NULL,
  `required_completed` INT UNSIGNED NOT NULL DEFAULT 0,
  `required_total` INT UNSIGNED NOT NULL DEFAULT 0,
  `optional_completed` INT UNSIGNED NOT NULL DEFAULT 0,
  `hints_used` INT UNSIGNED NOT NULL DEFAULT 0,
  `show_on_portfolio` TINYINT(1) NOT NULL DEFAULT 1,
  `completed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_completions_user_lab_unique` (`user_id`, `lab_id`),
  KEY `lab_completions_lab_id_index` (`lab_id`),
  KEY `lab_completions_instance_id_index` (`instance_id`),
  CONSTRAINT `lab_completions_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_completions_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_completions_instance_id_fk`
    FOREIGN KEY (`instance_id`) REFERENCES `lab_instances` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_feedback` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `difficulty_rating` ENUM('too_easy','appropriate','too_hard') NOT NULL,
  `clarity_rating` ENUM('poor','okay','good') NOT NULL,
  `feedback` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lab_feedback_unique` (`lab_id`, `user_id`),
  KEY `lab_feedback_user_id_index` (`user_id`),
  CONSTRAINT `lab_feedback_lab_id_fk`
    FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lab_feedback_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attach FKs to Phase 5/6 stub junction tables
SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'writeup_labs' AND CONSTRAINT_NAME = 'writeup_labs_lab_id_fk'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE `writeup_labs`
     ADD CONSTRAINT `writeup_labs_lab_id_fk`
       FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'project_labs' AND CONSTRAINT_NAME = 'project_labs_lab_id_fk'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE `project_labs`
     ADD CONSTRAINT `project_labs_lab_id_fk`
       FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;
