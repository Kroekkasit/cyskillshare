-- CySkillShare Phase 8 seed — DEVELOPMENT ONLY
SET NAMES utf8mb4;
USE `cyskillshare`;

UPDATE `users` SET `show_in_discovery` = 1 WHERE `show_in_discovery` IS NULL OR `show_in_discovery` = 0;
UPDATE `users` SET `show_in_discovery` = 1;


INSERT IGNORE INTO `mentors` (`user_id`, `bio`, `accepting_requests`, `max_mentees`,
  `preferred_frequency`, `preferred_session_length`, `languages`, `communication_style`,
  `verification_status`, `verified_by`, `verified_at`)
SELECT u.id, 'I help students with Web Security, Linux, and CTF fundamentals. Weekly 45-minute sessions.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call',
  'verified',
  (SELECT id FROM users WHERE username = 'admin' LIMIT 1),
  NOW()
FROM `users` u WHERE u.username = 'mentor1';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'mentor1'
JOIN `skills` s ON s.slug = 'web-security';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'mentor1'
JOIN `skills` s ON s.slug = 'sql-injection';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'mentor1'
JOIN `skills` s ON s.slug = 'linux';


INSERT IGNORE INTO `mentors` (`user_id`, `bio`, `accepting_requests`, `max_mentees`,
  `preferred_frequency`, `preferred_session_length`, `languages`, `communication_style`,
  `verification_status`, `verified_by`, `verified_at`)
SELECT u.id, 'Instructor mentor focusing on digital forensics and incident response.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call',
  'verified',
  (SELECT id FROM users WHERE username = 'admin' LIMIT 1),
  NOW()
FROM `users` u WHERE u.username = 'instructor1';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'instructor1'
JOIN `skills` s ON s.slug = 'digital-forensics';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'instructor1'
JOIN `skills` s ON s.slug = 'incident-response';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'instructor1'
JOIN `skills` s ON s.slug = 'network-security';


INSERT IGNORE INTO `mentors` (`user_id`, `bio`, `accepting_requests`, `max_mentees`,
  `preferred_frequency`, `preferred_session_length`, `languages`, `communication_style`,
  `verification_status`, `verified_by`, `verified_at`)
SELECT u.id, 'Peer mentor for beginners learning web basics and Linux.', 1, 3, 'weekly', '45 minutes', 'English, Thai', 'Async notes + weekly call',
  'unverified',
  NULL,
  NULL
FROM `users` u WHERE u.username = 'student1';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'web-security';


INSERT IGNORE INTO `mentor_skills` (`mentor_id`, `skill_id`)
SELECT m.id, s.id FROM `mentors` m
JOIN `users` u ON u.id = m.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'linux';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'Web Security Study Group', 'web-security-study-group', 'Weekly practice on SQLi, XSS, and access control. Beginner-friendly.', 'study', u.id, 'community', 'open', 20, 'active', CAST('["web"]' AS JSON)
FROM `users` u WHERE u.username = 'student1';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student1'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'web-security'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'sql-injection'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'xss'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'Linux & Networking Study Group', 'linux-networking-study-group', 'Hands-on Linux permissions, processes, and network fundamentals.', 'study', u.id, 'community', 'approval', 20, 'active', NULL
FROM `users` u WHERE u.username = 'student2';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student2'
WHERE g.slug = 'linux-networking-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'linux'
WHERE g.slug = 'linux-networking-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'network-security'
WHERE g.slug = 'linux-networking-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'networking'
WHERE g.slug = 'linux-networking-study-group';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'Digital Forensics Club', 'digital-forensics-club', 'Artifact analysis practice and case discussions.', 'study', u.id, 'community', 'approval', 20, 'active', CAST('["forensics"]' AS JSON)
FROM `users` u WHERE u.username = 'mentor1';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'mentor1'
WHERE g.slug = 'digital-forensics-club';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE g.slug = 'digital-forensics-club';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'incident-response'
WHERE g.slug = 'digital-forensics-club';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'Malware Analysis Study Group', 'malware-analysis-study-group', 'Static analysis notes and safe lab discussions (no live malware hosting).', 'study', u.id, 'community', 'invite_only', 20, 'active', CAST('["malware"]' AS JSON)
FROM `users` u WHERE u.username = 'instructor1';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'instructor1'
WHERE g.slug = 'malware-analysis-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'malware-analysis'
WHERE g.slug = 'malware-analysis-study-group';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'reverse-engineering'
WHERE g.slug = 'malware-analysis-study-group';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'OSINT Beginners', 'osint-beginners', 'Open-source intelligence fundamentals and ethical practice.', 'study', u.id, 'community', 'open', 20, 'active', CAST('["osint"]' AS JSON)
FROM `users` u WHERE u.username = 'student3';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student3'
WHERE g.slug = 'osint-beginners';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'osint'
WHERE g.slug = 'osint-beginners';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`, `specializations`)
SELECT 'Packet Pirates', 'packet-pirates', 'University CTF practice team. Looking for crypto and reverse help.', 'ctf', u.id, 'community', 'approval', 20, 'active', CAST('["web", "network", "forensics"]' AS JSON)
FROM `users` u WHERE u.username = 'student1';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'owner', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student1'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'web-security'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'network-security'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'member', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student2'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'member', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student3'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'co_captain', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'mentor1'
WHERE g.slug = 'packet-pirates';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'member', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student2'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'mentor', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'mentor1'
WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'member', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student1'
WHERE g.slug = 'digital-forensics-club';


INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, u.id, 'member', 'active'
FROM `collab_groups` g
JOIN `users` u ON u.username = 'student3'
WHERE g.slug = 'digital-forensics-club';


INSERT IGNORE INTO `collab_group_goals` (`group_id`, `title`, `description`, `status`, `progress`, `created_by`)
SELECT g.id, 'Complete Web Security Lab Series', 'Finish published web labs together this month.', 'active', 40, g.owner_id
FROM `collab_groups` g WHERE g.slug = 'web-security-study-group';

INSERT IGNORE INTO `collab_group_goals` (`group_id`, `title`, `description`, `status`, `progress`, `created_by`)
SELECT g.id, 'Prepare for university CTF', 'Practice Arena challenges across Web and Forensics.', 'active', 55, g.owner_id
FROM `collab_groups` g WHERE g.slug = 'packet-pirates';

INSERT IGNORE INTO `collab_group_activities`
  (`group_id`, `title`, `description`, `activity_type`, `related_type`, `scheduled_at`, `duration_minutes`, `created_by`, `status`)
SELECT g.id, 'SQL Injection Study Session', 'Walk through parameterized queries and lab tasks.',
  'lab', 'lab', DATE_ADD(NOW(), INTERVAL 3 DAY), 60, g.owner_id, 'scheduled'
FROM `collab_groups` g WHERE g.slug = 'web-security-study-group';

INSERT IGNORE INTO `collab_group_resources` (`group_id`, `title`, `description`, `resource_type`, `related_type`, `created_by`)
SELECT g.id, 'SQL Injection writeup', 'Related community writeup', 'writeup', 'writeup', g.owner_id
FROM `collab_groups` g WHERE g.slug = 'web-security-study-group';


INSERT IGNORE INTO `collab_groups`
  (`name`, `slug`, `description`, `group_type`, `owner_id`, `visibility`, `join_policy`, `max_members`, `status`)
SELECT 'Open Source SOC Dashboard', 'open-source-soc-dashboard',
  'Build a lightweight SOC dashboard for campus lab telemetry. Looking for backend, frontend, and threat intel contributors.',
  'project', u.id, 'community', 'approval', 12, 'active'
FROM `users` u WHERE u.username = 'student2';

INSERT IGNORE INTO `collab_group_members` (`group_id`, `user_id`, `role`, `status`)
SELECT g.id, g.owner_id, 'owner', 'active' FROM `collab_groups` g WHERE g.slug = 'open-source-soc-dashboard';

INSERT IGNORE INTO `collab_group_skills` (`group_id`, `skill_id`, `weight`)
SELECT g.id, s.id, 1.00 FROM `collab_groups` g
JOIN `skills` s ON s.slug IN ('security-monitoring','network-security','web-security')
WHERE g.slug = 'open-source-soc-dashboard';

INSERT IGNORE INTO `collab_group_roles` (`group_id`, `title`, `description`, `required_count`, `filled_count`)
SELECT g.id, 'Backend Developer', 'APIs and data pipelines', 1, 0 FROM `collab_groups` g WHERE g.slug = 'open-source-soc-dashboard';
INSERT IGNORE INTO `collab_group_roles` (`group_id`, `title`, `description`, `required_count`, `filled_count`)
SELECT g.id, 'Security Analyst', 'Detection rules and dashboards', 1, 0 FROM `collab_groups` g WHERE g.slug = 'open-source-soc-dashboard';
INSERT IGNORE INTO `collab_group_roles` (`group_id`, `title`, `description`, `required_count`, `filled_count`)
SELECT g.id, 'Frontend Developer', 'Dashboard UI', 1, 0 FROM `collab_groups` g WHERE g.slug = 'open-source-soc-dashboard';


INSERT IGNORE INTO `recruitment_posts` (`creator_id`, `group_id`, `title`, `description`, `looking_for`, `status`, `expires_at`)
SELECT u.id, g.id,
  'Looking for Web Security teammate for university CTF',
  'Packet Pirates needs someone comfortable with SQLi/XSS for weekend practice.',
  'ctf', 'open', DATE_ADD(NOW(), INTERVAL 30 DAY)
FROM `users` u
JOIN `collab_groups` g ON g.slug = 'packet-pirates'
WHERE u.username = 'student1';

INSERT IGNORE INTO `recruitment_skills` (`recruitment_id`, `skill_id`, `required`)
SELECT r.id, s.id, 1
FROM `recruitment_posts` r
JOIN `skills` s ON s.slug IN ('web-security','sql-injection')
WHERE r.title LIKE 'Looking for Web Security teammate%';


INSERT IGNORE INTO `mentorships`
  (`mentor_id`, `mentee_id`, `status`, `learning_goal`, `message`, `preferred_frequency`, `preferred_session_length`, `started_at`)
SELECT m.id, mentee.id, 'active',
  'Reach Intermediate Web Security and solve Medium Arena challenges.',
  'I completed beginner labs and want structured weekly guidance.',
  'weekly', '45 minutes', NOW()
FROM `mentors` m
JOIN `users` mu ON mu.id = m.user_id AND mu.username = 'mentor1'
JOIN `users` mentee ON mentee.username = 'student3';

INSERT IGNORE INTO `mentorship_skills` (`mentorship_id`, `skill_id`)
SELECT ms.id, s.id FROM `mentorships` ms
JOIN `mentors` m ON m.id = ms.mentor_id
JOIN `users` mu ON mu.id = m.user_id AND mu.username = 'mentor1'
JOIN `users` mentee ON mentee.id = ms.mentee_id AND mentee.username = 'student3'
JOIN `skills` s ON s.slug IN ('web-security','sql-injection');

INSERT IGNORE INTO `mentorship_goals`
  (`mentorship_id`, `skill_id`, `title`, `description`, `target_level`, `status`, `progress`)
SELECT ms.id, s.id, 'Reach Intermediate Web Security',
  'Complete labs, medium challenges, and a writeup.',
  3, 'active', 35
FROM `mentorships` ms
JOIN `mentors` m ON m.id = ms.mentor_id
JOIN `users` mu ON mu.id = m.user_id AND mu.username = 'mentor1'
JOIN `users` mentee ON mentee.id = ms.mentee_id AND mentee.username = 'student3'
JOIN `skills` s ON s.slug = 'web-security';

INSERT IGNORE INTO `mentorship_sessions`
  (`mentorship_id`, `title`, `notes`, `scheduled_at`, `duration_minutes`, `status`)
SELECT ms.id, 'Web Security kickoff', 'Review SQLi basics and choose next lab.',
  DATE_ADD(NOW(), INTERVAL 2 DAY), 45, 'scheduled'
FROM `mentorships` ms
JOIN `mentors` m ON m.id = ms.mentor_id
JOIN `users` mu ON mu.id = m.user_id AND mu.username = 'mentor1'
JOIN `users` mentee ON mentee.id = ms.mentee_id AND mentee.username = 'student3'
LIMIT 1;

