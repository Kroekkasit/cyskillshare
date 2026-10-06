-- CySkillShare least-privilege grants for the application DB user.
-- Applied as MySQL root during docker-entrypoint init (or manually).
-- Application connects as `cyskillshare` with DML only — never as root.

SET NAMES utf8mb4;

REVOKE ALL ON `cyskillshare`.* FROM 'cyskillshare'@'%';

GRANT SELECT, INSERT, UPDATE, DELETE
  ON `cyskillshare`.*
  TO 'cyskillshare'@'%';

FLUSH PRIVILEGES;
