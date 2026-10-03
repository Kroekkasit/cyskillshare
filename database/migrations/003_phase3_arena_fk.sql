-- CySkillShare Phase 3: add threads → challenges FK after both tables exist.
-- Safe for fresh installs (docker-entrypoint) after schema + arena_schema.

SET NAMES utf8mb4;

SET @fk_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'threads'
    AND CONSTRAINT_NAME = 'threads_challenge_id_fk'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);

SET @sql := IF(
  @fk_exists = 0,
  'ALTER TABLE `threads` ADD CONSTRAINT `threads_challenge_id_fk` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE SET NULL',
  'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
