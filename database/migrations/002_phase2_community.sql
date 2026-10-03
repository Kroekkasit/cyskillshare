-- Phase 2 incremental migration for existing databases.
-- Prefer recreating via schema.sql + seed.sql in development:
--   docker compose down -v && docker compose up -d --build

SET NAMES utf8mb4;
USE `cyskillshare`;

-- Soft delete for threads (ignore error if already applied)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'threads'
    AND COLUMN_NAME = 'deleted_at'
);

SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `threads` ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `updated_at`, ADD INDEX `threads_deleted_at_index` (`deleted_at`), ADD INDEX `threads_pinned_created_index` (`is_pinned`, `created_at`)',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `sort_order`)
SELECT 'Cloud Security', 'cloud-security', 'Cloud security and infrastructure hardening', 'cloud', 85
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'cloud-security');

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#cloud-security', 'cloud-security', 'Cloud security discussions', 'text', 10
FROM categories c
WHERE c.slug = 'cloud-security'
  AND NOT EXISTS (SELECT 1 FROM channels WHERE slug = 'cloud-security');
