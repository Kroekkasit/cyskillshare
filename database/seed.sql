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
  ('Cloud Security', 'cloud-security', 'Cloud security and infrastructure hardening', 'cloud', 85),
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
SELECT c.id, '#cloud-security', 'cloud-security', 'Cloud security discussions', 'text', 10
FROM categories c WHERE c.slug = 'cloud-security';

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
  ('networking', 'networking'),
  ('nmap', 'nmap'),
  ('powershell', 'powershell'),
  ('encryption', 'encryption'),
  ('hashing', 'hashing'),
  ('ctf', 'ctf'),
  ('web-security', 'web-security'),
  ('reverse-engineering', 'reverse-engineering');

-- ---------------------------------------------------------------------------
-- Development users
-- Passwords (DEVELOPMENT ONLY):
--   admin     / Admin@123!
--   student1  / Student@123!
--   student2  / Student@123!
--   student3  / Student@123!
--   mentor1     / Mentor@123!
--   moderator1  / Student@123!
--   instructor1 / Student@123!
-- ---------------------------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `student_id`, `year_level`, `program`, `bio`, `status`) VALUES
  (
    'admin',
    'admin@cyskillshare.local',
    '$2y$12$pYs5fOxUu6w7bk60eA1E8OjCCrhPjNBipVPoT.QkclyN9410DfMzi',
    'System Administrator',
    NULL,
    NULL,
    'College of Computing',
    'Platform admin for CySkillShare development.',
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
    'Interested in web security and PHP.',
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
    'Learning digital forensics and networking.',
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
    'CTF player focusing on reversing.',
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
    'Peer mentor for junior cybersecurity students.',
    'active'
  ),
  (
    'moderator1',
    'moderator1@cyskillshare.local',
    '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q',
    'Mod Pilot',
    NULL,
    NULL,
    'Cybersecurity',
    'Community moderator.',
    'active'
  ),
  (
    'instructor1',
    'instructor1@cyskillshare.local',
    '$2y$12$jYDuehF9Q0rmMafHe4beQuLEoob8l5hon04UTuFILkOYaeIEzXL.q',
    'Dr. Kittipong',
    NULL,
    NULL,
    'Cybersecurity',
    'Instructor at College of Computing, KKU.',
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

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'moderator1' AND r.name IN ('moderator', 'student');

INSERT INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM users u, roles r WHERE u.username = 'instructor1' AND r.name IN ('instructor', 'student');

-- ---------------------------------------------------------------------------
-- Phase 2 community seed content
-- ---------------------------------------------------------------------------

-- Pinned welcome (bilingual — KKU CoC)
INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`, `is_pinned`)
SELECT ch.id, u.id,
  'ยินดีต้อนรับสู่ CySkillShare / Welcome',
  'ยินดีต้อนรับสู่ชุมชนเรียนรู้ไซเบอร์ของวิทยาลัยการคอมพิวเตอร์ มข.\n\nกรุณา:\n- เคารพกันและกัน\n- ห้ามแชร์เฉลยข้อสอบที่ผิดระเบียบวิชาการ\n- คุยเชิงเทคนิคพร้อมหลักฐาน\n- ติดแท็กให้ค้นหาได้ง่าย\n\nถาม → คุย → ช่วย → แก้ → แชร์ความรู้\n\n---\nWelcome to the cybersecurity learning community at College of Computing, KKU.\nAsk → Discuss → Help → Solve → Share Knowledge',
  'open', 1
FROM channels ch, users u
WHERE ch.slug = 'general' AND u.username = 'moderator1' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'How does CSRF actually work?',
  'I''m trying to understand why a malicious website can trigger requests on a site where I am already logged in.\n\n1. Does the browser always send cookies?\n2. Why is SameSite helpful?\n3. When do we still need CSRF tokens?\n\nLooking for a clear explanation with a PHP example.',
  'solved'
FROM channels ch, users u WHERE ch.slug = 'web-security' AND u.username = 'student2' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'Understanding prepared statements in PHP',
  'I''m currently working on a PHP assignment using PDO.\n\nIs using prepared statements enough to prevent SQL injection, or should I also validate the input?\n\n```php\n$stmt = $pdo->prepare(\"SELECT * FROM users WHERE username = ?\");\n$stmt->execute([$username]);\n```\n\nAny best practices from seniors?',
  'solved'
FROM channels ch, users u WHERE ch.slug = 'web-security' AND u.username = 'student1' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'How to read a Wireshark TCP stream?',
  'In Wireshark I can see many packets for an HTTP session. What is the fastest way to follow a TCP stream and extract the request/response body for analysis?',
  'open'
FROM channels ch, users u WHERE ch.slug = 'network-security' AND u.username = 'student2' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'Nmap SYN scan vs TCP connect scan',
  'What is the practical difference between `-sS` and `-sT` when scanning lab machines? When would a connect scan be preferred?',
  'open'
FROM channels ch, users u WHERE ch.slug = 'network-security' AND u.username = 'student3' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'How should I approach my first CTF?',
  'I want to join my first CTF this semester. Which categories should beginners start with, and what tools should I install first on Linux?',
  'open'
FROM channels ch, users u WHERE ch.slug = 'ctf-general' AND u.username = 'student1' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'Basic Linux permissions for cybersecurity',
  'Can someone explain when we use chmod 600 vs 644 for config files containing secrets? Looking for practical guidance for lab reports.',
  'open'
FROM channels ch, users u WHERE ch.slug = 'assignment-help' AND u.username = 'student2' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'How to identify suspicious PowerShell activity?',
  'In a DFIR exercise we have Windows event logs. Which Event IDs or command-line patterns usually indicate suspicious PowerShell usage?',
  'open'
FROM channels ch, users u WHERE ch.slug = 'digital-forensics' AND u.username = 'student3' LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'What is the difference between hashing and encryption?',
  'I still confuse hashing and encryption in cryptography class. Can someone explain with cybersecurity examples (password storage vs TLS)?',
  'solved'
FROM channels ch, users u WHERE ch.slug = 'cryptography' AND u.username = 'student1' LIMIT 1;

-- Tags for threads
INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'How does CSRF actually work?' AND tg.slug IN ('csrf', 'web-security', 'php');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'Understanding prepared statements in PHP' AND tg.slug IN ('php', 'sql-injection', 'web-security');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'How to read a Wireshark TCP stream?' AND tg.slug IN ('wireshark', 'networking');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'Nmap SYN scan vs TCP connect scan' AND tg.slug IN ('nmap', 'networking');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'How should I approach my first CTF?' AND tg.slug IN ('ctf', 'linux');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'Basic Linux permissions for cybersecurity' AND tg.slug IN ('linux');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'How to identify suspicious PowerShell activity?' AND tg.slug IN ('powershell', 'forensics');

INSERT INTO thread_tags (thread_id, tag_id)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'What is the difference between hashing and encryption?' AND tg.slug IN ('hashing', 'encryption');

-- Replies + best answers
INSERT INTO replies (thread_id, user_id, content, is_best_answer)
SELECT t.id, u.id,
  'Yes — browsers send cookies on cross-site form POSTs by default for many legacy sites.\n\n**SameSite=Lax/Strict** reduces many CSRF cases, but APIs and older browsers still need tokens.\n\nA practical pattern in PHP is a per-session CSRF token checked on every state-changing request.',
  1
FROM threads t, users u
WHERE t.title = 'How does CSRF actually work?' AND u.username = 'mentor1' LIMIT 1;

INSERT INTO replies (thread_id, user_id, content, is_best_answer)
SELECT t.id, u.id,
  'Prepared statements are the core control against SQL injection for query structure.\n\nYou should still validate/normalize input for business rules (length, type, allowlists), but validation alone is **not** a substitute for parameterization.\n\nNever concatenate user input into SQL.',
  1
FROM threads t, users u
WHERE t.title = 'Understanding prepared statements in PHP' AND u.username = 'instructor1' LIMIT 1;

INSERT INTO replies (thread_id, user_id, content)
SELECT t.id, u.id,
  'Right-click a packet → Follow → TCP Stream. Use the drop-down to switch client/server and export as raw/ASCII for writeups.'
FROM threads t, users u
WHERE t.title = 'How to read a Wireshark TCP stream?' AND u.username = 'mentor1' LIMIT 1;

INSERT INTO replies (thread_id, user_id, content, is_best_answer)
SELECT t.id, u.id,
  '**Hashing** is one-way (password storage with salt/argon2). **Encryption** is reversible with a key (TLS, disk encryption).\n\nIf you need confidentiality and later recovery of the original value, encrypt. If you only need to verify later, hash.',
  1
FROM threads t, users u
WHERE t.title = 'What is the difference between hashing and encryption?' AND u.username = 'mentor1' LIMIT 1;

-- Votes
INSERT INTO votes (user_id, target_type, target_id, vote_type)
SELECT u.id, 'thread', t.id, 'up'
FROM users u, threads t
WHERE u.username IN ('student1', 'student3', 'mentor1') AND t.title = 'How does CSRF actually work?';

INSERT INTO votes (user_id, target_type, target_id, vote_type)
SELECT u.id, 'thread', t.id, 'up'
FROM users u, threads t
WHERE u.username IN ('student2', 'mentor1', 'moderator1') AND t.title = 'Understanding prepared statements in PHP';

-- Bookmarks
INSERT INTO bookmarks (user_id, target_type, target_id)
SELECT u.id, 'thread', t.id
FROM users u, threads t
WHERE u.username = 'student1' AND t.title IN (
  'How does CSRF actually work?',
  'How should I approach my first CTF?'
);

-- Notifications
INSERT INTO notifications (user_id, type, title, message, reference_type, reference_id, is_read)
SELECT u.id, 'thread_reply', 'mentor1 replied to your discussion.', '“How does CSRF actually work?”', 'thread', t.id, 0
FROM users u, threads t
WHERE u.username = 'student2' AND t.title = 'How does CSRF actually work?' LIMIT 1;

INSERT INTO notifications (user_id, type, title, message, reference_type, reference_id, is_read)
SELECT u.id, 'best_answer', 'Your reply was marked as the best answer.', '“Understanding prepared statements in PHP”', 'thread', t.id, 0
FROM users u, threads t
WHERE u.username = 'instructor1' AND t.title = 'Understanding prepared statements in PHP' LIMIT 1;
