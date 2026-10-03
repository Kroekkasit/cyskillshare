-- CySkillShare Phase 1 Seed Data
-- DEVELOPMENT ONLY — do not use these credentials in production.

SET NAMES utf8mb4;
USE `cyskillshare`;

-- ---------------------------------------------------------------------------
-- Roles
-- ---------------------------------------------------------------------------
INSERT INTO `roles` (`name`, `description`) VALUES
  ('student', 'Standard student account'),
  ('mentor', 'Peer mentor'),
  ('instructor', 'Course instructor'),
  ('moderator', 'Community moderator'),
  ('admin', 'Platform administrator');

-- ---------------------------------------------------------------------------
-- Categories
-- ---------------------------------------------------------------------------
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `sort_order`) VALUES
  ('General', 'general', 'General discussion and introductions', 'chat', 10),
  ('Web Security', 'web-security', 'OWASP, XSS, SQLi, CSRF, and web app security', 'globe', 20),
  ('Network Security', 'network-security', 'Firewalls, protocols, packet analysis', 'network', 30),
  ('Digital Forensics', 'digital-forensics', 'Disk, memory, and evidence analysis', 'search', 40),
  ('Malware Analysis', 'malware-analysis', 'Static and dynamic malware analysis', 'bug', 50),
  ('Reverse Engineering', 'reverse-engineering', 'Binary analysis and RE techniques', 'cpu', 60),
  ('Cryptography', 'cryptography', 'Crypto concepts, attacks, and tooling', 'lock', 70),
  ('OSINT', 'osint', 'Open-source intelligence gathering', 'eye', 80),
  ('Academic', 'academic', 'Courses, assignments, and academic help', 'book', 90),
  ('Career', 'career', 'Internships, jobs, and career advice', 'briefcase', 100),
  ('CTF', 'ctf', 'Capture The Flag practice and teams', 'flag', 110);

-- ---------------------------------------------------------------------------
-- Channels
-- ---------------------------------------------------------------------------
INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#general', 'general', 'General community chat', 'text', 10
FROM categories c WHERE c.slug = 'general';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#introductions', 'introductions', 'Introduce yourself to the community', 'text', 20
FROM categories c WHERE c.slug = 'general';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#web-security', 'web-security', 'Web application security discussions', 'text', 10
FROM categories c WHERE c.slug = 'web-security';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#network-security', 'network-security', 'Network security discussions', 'text', 10
FROM categories c WHERE c.slug = 'network-security';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#digital-forensics', 'digital-forensics', 'Digital forensics discussions', 'text', 10
FROM categories c WHERE c.slug = 'digital-forensics';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#malware-analysis', 'malware-analysis', 'Malware analysis discussions', 'text', 10
FROM categories c WHERE c.slug = 'malware-analysis';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#reverse-engineering', 'reverse-engineering', 'Reverse engineering discussions', 'text', 10
FROM categories c WHERE c.slug = 'reverse-engineering';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#cryptography', 'cryptography', 'Cryptography discussions', 'text', 10
FROM categories c WHERE c.slug = 'cryptography';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#osint', 'osint', 'OSINT discussions', 'text', 10
FROM categories c WHERE c.slug = 'osint';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#assignment-help', 'assignment-help', 'Ask for assignment guidance (no cheating)', 'help', 10
FROM categories c WHERE c.slug = 'academic';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#project-help', 'project-help', 'Project collaboration and help', 'help', 20
FROM categories c WHERE c.slug = 'academic';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#ctf-general', 'ctf-general', 'CTF discussion and team finding', 'text', 10
FROM categories c WHERE c.slug = 'ctf';

INSERT INTO `channels` (`category_id`, `name`, `slug`, `description`, `channel_type`, `sort_order`)
SELECT c.id, '#internships', 'internships', 'Internship and career opportunities', 'text', 10
FROM categories c WHERE c.slug = 'career';

-- ---------------------------------------------------------------------------
-- Tags
-- ---------------------------------------------------------------------------
INSERT INTO `tags` (`name`, `slug`) VALUES
  ('xss', 'xss'),
  ('sql-injection', 'sql-injection'),
  ('csrf', 'csrf'),
  ('linux', 'linux'),
  ('wireshark', 'wireshark'),
  ('python', 'python'),
  ('php', 'php'),
  ('docker', 'docker'),
  ('malware', 'malware'),
  ('forensics', 'forensics'),
  ('networking', 'networking');

-- ---------------------------------------------------------------------------
-- Development users
-- Passwords (DEVELOPMENT ONLY):
--   admin     / Admin@123!
--   student1  / Student@123!
--   student2  / Student@123!
--   student3  / Student@123!
--   mentor1   / Mentor@123!
-- ---------------------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `student_id`, `year_level`, `program`, `status`) VALUES
  (
    'admin',
    'admin@cyskillshare.local',
    '$2y$12$pYs5fOxUu6w7bk60eA1E8OjCCrhPjNBipVPoT.QkclyN9410DfMzi',
    'System Administrator',
    NULL,
    NULL,
    'College of Computing',
    'active'
  ),
  (
    'student1',
    'student1@kkumail.com',
    '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q',
    'Somchai Cyber',
    '653040001-1',
    3,
    'Cybersecurity',
    'active'
  ),
  (
    'student2',
    'student2@kkumail.com',
    '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q',
    'Suda Secure',
    '653040002-2',
    2,
    'Cybersecurity',
    'active'
  ),
  (
    'student3',
    'student3@kkumail.com',
    '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q',
    'Nattapong Net',
    '653040003-3',
    4,
    'Computer Science',
    'active'
  ),
  (
    'mentor1',
    'mentor1@cyskillshare.local',
    '$2y$12$qqJ2B4.sHtBGy23YMuA6gOojg6oAPZd.iQ1MXbcYACL3utNGjQHTm',
    'Arisa Mentor',
    NULL,
    NULL,
    'Cybersecurity',
    'active'
  );

-- Role assignments
INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'admin' AND r.name = 'admin';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'admin' AND r.name = 'student';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'student1' AND r.name = 'student';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'student2' AND r.name = 'student';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'student3' AND r.name = 'student';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'mentor1' AND r.name = 'mentor';

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'mentor1' AND r.name = 'student';

-- Sample thread (optional foundation content)
INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'Welcome to CySkillShare',
  'This is a development foundation thread. Feel free to explore channels and reply once forums are fully built in later phases.',
  'open'
FROM channels ch, users u
WHERE ch.slug = 'introductions' AND u.username = 'admin'
LIMIT 1;
