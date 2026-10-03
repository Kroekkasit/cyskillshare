-- CySkillShare Phase 6: Writeup relations + Knowledge Base
-- Requires: users, skills, challenges, writeups, threads, projects, writeups table from arena_schema

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `writeup_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `writeup_tags_slug_unique` (`slug`),
  UNIQUE KEY `writeup_tags_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_tag_map` (
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`writeup_id`, `tag_id`),
  KEY `writeup_tag_map_tag_id_index` (`tag_id`),
  CONSTRAINT `writeup_tag_map_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeup_tag_map_tag_id_fk`
    FOREIGN KEY (`tag_id`) REFERENCES `writeup_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_skills` (
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`writeup_id`, `skill_id`),
  KEY `writeup_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `writeup_skills_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeup_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_challenges` (
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `challenge_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`writeup_id`, `challenge_id`),
  KEY `writeup_challenges_challenge_id_index` (`challenge_id`),
  CONSTRAINT `writeup_challenges_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeup_challenges_challenge_id_fk`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_labs` (
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `lab_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`writeup_id`, `lab_id`),
  CONSTRAINT `writeup_labs_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_threads` (
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `thread_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`writeup_id`, `thread_id`),
  KEY `writeup_threads_thread_id_index` (`thread_id`),
  CONSTRAINT `writeup_threads_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeup_threads_thread_id_fk`
    FOREIGN KEY (`thread_id`) REFERENCES `threads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `writeup_versions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `edited_by` BIGINT UNSIGNED NOT NULL,
  `change_summary` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `writeup_versions_unique` (`writeup_id`, `version`),
  KEY `writeup_versions_edited_by_index` (`edited_by`),
  CONSTRAINT `writeup_versions_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `writeup_versions_edited_by_fk`
    FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_articles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `author_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(220) NOT NULL,
  `summary` VARCHAR(500) NULL,
  `content` MEDIUMTEXT NOT NULL,
  `difficulty` ENUM('beginner','intermediate','advanced','expert') NOT NULL DEFAULT 'beginner',
  `status` ENUM('draft','published','under_review','archived') NOT NULL DEFAULT 'draft',
  `visibility` ENUM('public','community','private') NOT NULL DEFAULT 'public',
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_official` TINYINT(1) NOT NULL DEFAULT 0,
  `version` INT UNSIGNED NOT NULL DEFAULT 1,
  `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `helpful_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_articles_slug_unique` (`slug`),
  KEY `knowledge_articles_author_id_index` (`author_id`),
  KEY `knowledge_articles_category_id_index` (`category_id`),
  KEY `knowledge_articles_status_index` (`status`),
  KEY `knowledge_articles_featured_index` (`featured`),
  FULLTEXT KEY `knowledge_articles_ft_search` (`title`, `summary`, `content`),
  CONSTRAINT `knowledge_articles_author_id_fk`
    FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_articles_category_id_fk`
    FOREIGN KEY (`category_id`) REFERENCES `writeup_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_article_versions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` BIGINT UNSIGNED NOT NULL,
  `version` INT UNSIGNED NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `edited_by` BIGINT UNSIGNED NOT NULL,
  `change_summary` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `knowledge_article_versions_unique` (`article_id`, `version`),
  CONSTRAINT `knowledge_article_versions_article_id_fk`
    FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_article_versions_edited_by_fk`
    FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_article_reviews` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` BIGINT UNSIGNED NOT NULL,
  `reviewer_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('pending','approved','changes_requested','rejected') NOT NULL DEFAULT 'pending',
  `review_note` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `knowledge_article_reviews_article_id_index` (`article_id`),
  KEY `knowledge_article_reviews_status_index` (`status`),
  CONSTRAINT `knowledge_article_reviews_article_id_fk`
    FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_article_reviews_reviewer_id_fk`
    FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_article_sources` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` BIGINT UNSIGNED NOT NULL,
  `source_type` VARCHAR(50) NOT NULL,
  `source_id` BIGINT UNSIGNED NULL,
  `description` VARCHAR(255) NULL,
  `external_title` VARCHAR(200) NULL,
  `external_url` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `knowledge_article_sources_article_id_index` (`article_id`),
  CONSTRAINT `knowledge_article_sources_article_id_fk`
    FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_article_skills` (
  `article_id` BIGINT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  `weight` DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`, `skill_id`),
  KEY `knowledge_article_skills_skill_id_index` (`skill_id`),
  CONSTRAINT `knowledge_article_skills_article_id_fk`
    FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_article_skills_skill_id_fk`
    FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `knowledge_article_contributors` (
  `article_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role` ENUM('author','contributor','reviewer','editor') NOT NULL DEFAULT 'contributor',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`, `user_id`, `role`),
  CONSTRAINT `knowledge_article_contributors_article_id_fk`
    FOREIGN KEY (`article_id`) REFERENCES `knowledge_articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `knowledge_article_contributors_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `content_reactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `content_type` ENUM('writeup','knowledge_article') NOT NULL,
  `content_id` BIGINT UNSIGNED NOT NULL,
  `reaction_type` ENUM('helpful','clear','practical') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_reactions_unique` (`user_id`, `content_type`, `content_id`, `reaction_type`),
  KEY `content_reactions_content_index` (`content_type`, `content_id`),
  CONSTRAINT `content_reactions_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_featured_writeups` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `writeup_id` BIGINT UNSIGNED NOT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`, `writeup_id`),
  CONSTRAINT `portfolio_featured_writeups_user_id_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `portfolio_featured_writeups_writeup_id_fk`
    FOREIGN KEY (`writeup_id`) REFERENCES `writeups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
