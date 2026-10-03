-- CySkillShare Phase 8: Mentorship, Study Groups & Collaboration
-- Uses collab_groups (not bare `groups`) to avoid MySQL/GROUP BY confusion.
-- Portfolio `projects` remain showcase-only; collaboration project teams use group_type=project.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Discovery privacy on users
SET @col := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'show_in_discovery'
);
SET @sql := IF(@col = 0,
  'ALTER TABLE `users` ADD COLUMN `show_in_discovery` TINYINT(1) NOT NULL DEFAULT 1 AFTER `skills_visibility`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS `user_blocks` (
  `blocker_id` BIGINT UNSIGNED NOT NULL,
  `blocked_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blocker_id`, `blocked_id`),
  KEY `user_blocks_blocked_id_index` (`blocked_id`),
  CONSTRAINT `user_blocks_blocker_id_fk`
    FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_blocks_blocked_id_fk`
    FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_groups` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(160) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `description` MEDIUMTEXT NOT NULL,
  `group_type` ENUM('study','ctf','project','course','general') NOT NULL DEFAULT 'study',
  `owner_id` BIGINT UNSIGNED NOT NULL,
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'community',
  `join_policy` ENUM('open','approval','invite_only') NOT NULL DEFAULT 'approval',
  `max_members` INT UNSIGNED NOT NULL DEFAULT 20,
  `status` ENUM('active','archived','suspended') NOT NULL DEFAULT 'active',
  `specializations` JSON NULL,
  `channel_id` INT UNSIGNED NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collab_groups_slug_unique` (`slug`),
  KEY `collab_groups_owner_id_index` (`owner_id`),
  KEY `collab_groups_type_index` (`group_type`),
  KEY `collab_groups_status_index` (`status`),
  KEY `collab_groups_visibility_index` (`visibility`),
  FULLTEXT KEY `collab_groups_ft_search` (`name`, `description`),
  CONSTRAINT `collab_groups_owner_id_fk`
    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_groups_channel_id_fk`
    FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_members` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role` ENUM('owner','admin','moderator','mentor','captain','co_captain','member') NOT NULL DEFAULT 'member',
  `status` ENUM('active','invited','left','removed') NOT NULL DEFAULT 'active',
  `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_active_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collab_group_members_unique` (`group_id`, `user_id`),
  KEY `collab_group_members_user_id_index` (`user_id`),
  CONSTRAINT `collab_group_members_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_members_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_skills` (
  `group_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`, `skill_id`),
  KEY `collab_group_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `collab_group_skills_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_goals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `target_date` DATE NULL,
  `status` ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collab_group_goals_group_id_index` (`group_id`),
  CONSTRAINT `collab_group_goals_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_goals_created_by_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_activities` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `activity_type` ENUM('study','challenge','lab','discussion','project','ctf','review') NOT NULL DEFAULT 'study',
  `related_type` VARCHAR(40) NULL,
  `related_id` BIGINT UNSIGNED NULL,
  `scheduled_at` TIMESTAMP NULL DEFAULT NULL,
  `duration_minutes` INT UNSIGNED NULL,
  `meeting_link` VARCHAR(500) NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `collab_group_activities_group_id_index` (`group_id`),
  KEY `collab_group_activities_scheduled_at_index` (`scheduled_at`),
  CONSTRAINT `collab_group_activities_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_activities_created_by_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_resources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `resource_type` ENUM('link','writeup','lab','challenge','knowledge','note','other') NOT NULL DEFAULT 'link',
  `url` VARCHAR(500) NULL,
  `related_type` VARCHAR(40) NULL,
  `related_id` BIGINT UNSIGNED NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `collab_group_resources_group_id_index` (`group_id`),
  CONSTRAINT `collab_group_resources_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_resources_created_by_fk`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_invitations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `inviter_id` BIGINT UNSIGNED NOT NULL,
  `invitee_id` BIGINT UNSIGNED NOT NULL,
  `token` CHAR(64) NOT NULL,
  `status` ENUM('pending','accepted','declined','expired','cancelled') NOT NULL DEFAULT 'pending',
  `expires_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collab_group_invitations_token_unique` (`token`),
  KEY `collab_group_invitations_invitee_index` (`invitee_id`, `status`),
  CONSTRAINT `collab_group_invitations_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_invitations_inviter_id_fk`
    FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_invitations_invitee_id_fk`
    FOREIGN KEY (`invitee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_join_requests` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `message` TEXT NULL,
  `status` ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `collab_group_join_requests_group_user_index` (`group_id`, `user_id`),
  KEY `collab_group_join_requests_user_id_index` (`user_id`),
  KEY `collab_group_join_requests_status_index` (`status`),
  CONSTRAINT `collab_group_join_requests_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_join_requests_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_join_requests_reviewed_by_fk`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(120) NOT NULL,
  `description` TEXT NULL,
  `required_count` INT UNSIGNED NOT NULL DEFAULT 1,
  `filled_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `collab_group_roles_group_id_index` (`group_id`),
  CONSTRAINT `collab_group_roles_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `collab_group_applications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `message` TEXT NULL,
  `status` ENUM('pending','accepted','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `collab_group_applications_group_id_index` (`group_id`),
  KEY `collab_group_applications_user_id_index` (`user_id`),
  CONSTRAINT `collab_group_applications_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_applications_role_id_fk`
    FOREIGN KEY (`role_id`) REFERENCES `collab_group_roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `collab_group_applications_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collab_group_applications_reviewed_by_fk`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mentors
CREATE TABLE IF NOT EXISTS `mentors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `bio` TEXT NULL,
  `accepting_requests` TINYINT(1) NOT NULL DEFAULT 1,
  `max_mentees` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `preferred_frequency` VARCHAR(80) NULL,
  `preferred_session_length` VARCHAR(40) NULL,
  `languages` VARCHAR(120) NULL,
  `communication_style` VARCHAR(120) NULL,
  `verification_status` ENUM('unverified','verified','suspended') NOT NULL DEFAULT 'unverified',
  `verified_by` BIGINT UNSIGNED NULL,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `verification_note` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mentors_user_id_unique` (`user_id`),
  KEY `mentors_accepting_index` (`accepting_requests`),
  KEY `mentors_verification_index` (`verification_status`),
  CONSTRAINT `mentors_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentors_verified_by_fk`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentor_skills` (
  `mentor_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `preferred_level` TINYINT UNSIGNED NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mentor_id`, `skill_id`),
  KEY `mentor_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `mentor_skills_mentor_id_fk`
    FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentor_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mentor_id` BIGINT UNSIGNED NOT NULL,
  `mentee_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('pending','accepted','declined','cancelled','active','completed') NOT NULL DEFAULT 'pending',
  `learning_goal` TEXT NULL,
  `message` TEXT NULL,
  `preferred_frequency` VARCHAR(80) NULL,
  `preferred_session_length` VARCHAR(40) NULL,
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `ended_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mentorships_mentor_id_index` (`mentor_id`),
  KEY `mentorships_mentee_id_index` (`mentee_id`),
  KEY `mentorships_status_index` (`status`),
  CONSTRAINT `mentorships_mentor_id_fk`
    FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentorships_mentee_id_fk`
    FOREIGN KEY (`mentee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorship_skills` (
  `mentorship_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`mentorship_id`, `skill_id`),
  CONSTRAINT `mentorship_skills_mentorship_id_fk`
    FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentorship_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorship_goals` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mentorship_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `target_level` TINYINT UNSIGNED NULL,
  `status` ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mentorship_goals_mentorship_id_index` (`mentorship_id`),
  CONSTRAINT `mentorship_goals_mentorship_id_fk`
    FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentorship_goals_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorship_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mentorship_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `notes` TEXT NULL,
  `scheduled_at` TIMESTAMP NOT NULL,
  `duration_minutes` INT UNSIGNED NOT NULL DEFAULT 45,
  `meeting_link` VARCHAR(500) NULL,
  `status` ENUM('scheduled','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mentorship_sessions_mentorship_id_index` (`mentorship_id`),
  KEY `mentorship_sessions_scheduled_at_index` (`scheduled_at`),
  CONSTRAINT `mentorship_sessions_mentorship_id_fk`
    FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `mentorship_feedback` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `from_user_id` BIGINT UNSIGNED NOT NULL,
  `to_user_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `feedback` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mentorship_feedback_unique` (`session_id`, `from_user_id`),
  CONSTRAINT `mentorship_feedback_session_id_fk`
    FOREIGN KEY (`session_id`) REFERENCES `mentorship_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentorship_feedback_from_user_id_fk`
    FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentorship_feedback_to_user_id_fk`
    FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `recruitment_posts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `creator_id` BIGINT UNSIGNED NOT NULL,
  `group_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `looking_for` ENUM('study','ctf','project','mentor','general') NOT NULL DEFAULT 'general',
  `status` ENUM('open','filled','closed') NOT NULL DEFAULT 'open',
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recruitment_posts_creator_id_index` (`creator_id`),
  KEY `recruitment_posts_status_index` (`status`),
  KEY `recruitment_posts_group_id_index` (`group_id`),
  CONSTRAINT `recruitment_posts_creator_id_fk`
    FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recruitment_posts_group_id_fk`
    FOREIGN KEY (`group_id`) REFERENCES `collab_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `recruitment_skills` (
  `recruitment_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `required` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`recruitment_id`, `skill_id`),
  KEY `recruitment_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `recruitment_skills_recruitment_id_fk`
    FOREIGN KEY (`recruitment_id`) REFERENCES `recruitment_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recruitment_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `recruitment_applications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recruitment_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `message` TEXT NULL,
  `status` ENUM('pending','accepted','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recruitment_applications_unique` (`recruitment_id`, `user_id`),
  KEY `recruitment_applications_user_id_index` (`user_id`),
  CONSTRAINT `recruitment_applications_recruitment_id_fk`
    FOREIGN KEY (`recruitment_id`) REFERENCES `recruitment_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recruitment_applications_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recruitment_applications_reviewed_by_fk`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
