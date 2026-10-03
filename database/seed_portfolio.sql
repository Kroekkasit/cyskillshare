-- CySkillShare Phase 5: Portfolio seed data
-- DEVELOPMENT ONLY — do not use in production.

SET NAMES utf8mb4;
USE `cyskillshare`;

-- ---------------------------------------------------------------------------
-- Portfolios (only when matching users exist)
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `portfolios` (
  `user_id`, `is_enabled`, `visibility`, `headline`, `about`,
  `university`, `program`, `graduation_year`, `github_url`,
  `show_challenge_stats`, `show_skill_evidence`, `show_community_stats`, `sections_json`
)
SELECT
  u.id, 1, 'public', 'Cybersecurity Student',
  'Third-year Computer Science student at Khon Kaen University with a focus on web application security, secure coding, and hands-on lab work. I document projects from coursework and self-directed practice to demonstrate practical security skills.',
  'Khon Kaen University', 'Computer Science', 2027,
  'https://github.com/example/student1',
  1, 1, 1,
  JSON_OBJECT(
    'about', true,
    'skills', true,
    'projects', true,
    'challenges', true,
    'writeups', true,
    'community', true,
    'education', true,
    'experience', true,
    'certifications', true,
    'links', true
  )
FROM `users` u WHERE u.username = 'student1';

INSERT IGNORE INTO `portfolios` (`user_id`, `is_enabled`, `visibility`, `headline`)
SELECT u.id, 1, 'community', 'Cybersecurity Learner'
FROM `users` u WHERE u.username = 'student2';

INSERT IGNORE INTO `portfolios` (`user_id`, `is_enabled`, `visibility`, `headline`)
SELECT u.id, 1, 'private', 'Private Portfolio'
FROM `users` u WHERE u.username = 'student3';

INSERT IGNORE INTO `portfolios` (`user_id`, `is_enabled`, `visibility`, `headline`)
SELECT u.id, 1, 'public', 'Peer Mentor — Cybersecurity'
FROM `users` u WHERE u.username = 'mentor1';

-- ---------------------------------------------------------------------------
-- Portfolio education (student1)
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `portfolio_education` (
  `user_id`, `institution`, `program`, `field`, `start_year`, `end_year`, `display_order`
)
SELECT u.id, 'Khon Kaen University', 'Computer Science', 'Cybersecurity', 2023, 2027, 10
FROM `users` u WHERE u.username = 'student1';

-- ---------------------------------------------------------------------------
-- Projects (student1)
-- ---------------------------------------------------------------------------

-- A) Secure File Upload Lab — published, public, completed, featured
INSERT IGNORE INTO `projects` (
  `user_id`, `title`, `slug`, `short_description`, `description`,
  `project_type`, `status`, `publish_status`, `visibility`,
  `repository_url`, `start_date`, `end_date`, `featured`, `display_order`
)
SELECT
  u.id,
  'Secure File Upload Lab',
  'secure-file-upload-lab',
  'A deliberately vulnerable PHP upload lab hardened step-by-step to teach secure file handling, MIME validation, and access control.',
  '## Overview\n\nThis home-lab web application simulates a student file-sharing portal built with **PHP**, **Apache**, and **MySQL**. The initial version contained classic upload flaws: missing extension checks, predictable storage paths, and weak session handling.\n\n## What I built\n\n- Baseline vulnerable upload endpoint for classroom demos\n- Hardened version with allowlists, content sniffing, and randomized storage\n- `.htaccess` rules to block script execution in upload directories\n- Prepared statements for all database queries\n\n## Key takeaways\n\nUpload handlers are a high-risk boundary. Defense requires layered controls: validation, storage isolation, and least-privilege access — not a single regex on the filename.',
  'web_security', 'completed', 'published', 'public',
  'https://github.com/example/student1/secure-file-upload-lab',
  '2025-09-01', '2025-11-15', 1, 10
FROM `users` u WHERE u.username = 'student1';

-- B) Network Scanner — published, public, completed, featured
INSERT IGNORE INTO `projects` (
  `user_id`, `title`, `slug`, `short_description`, `description`,
  `project_type`, `status`, `publish_status`, `visibility`,
  `repository_url`, `start_date`, `end_date`, `featured`, `display_order`
)
SELECT
  u.id,
  'Network Scanner',
  'mini-network-scanner',
  'A Python/Scapy-based host discovery and port-scanning tool for learning network reconnaissance fundamentals.',
  '## Overview\n\nA command-line scanner inspired by coursework on **network reconnaissance**. It performs ARP-based host discovery on local subnets and TCP connect scans against a configurable port list.\n\n## Features\n\n- Live host discovery with Scapy\n- Sequential and threaded connect scans\n- CSV export of open ports and service guesses\n- Safe defaults: rate limiting and explicit target confirmation\n\n## Learning goals\n\nUnderstanding scan types, timing, and noise trade-offs prepares you for both offensive assessments and defensive detection engineering.',
  'network_security', 'completed', 'published', 'public',
  'https://github.com/example/student1/mini-network-scanner',
  '2025-06-01', '2025-08-20', 1, 20
FROM `users` u WHERE u.username = 'student1';

-- C) Malware Analysis Toolkit — published, public, completed, not featured
INSERT IGNORE INTO `projects` (
  `user_id`, `title`, `slug`, `short_description`, `description`,
  `project_type`, `status`, `publish_status`, `visibility`,
  `repository_url`, `start_date`, `end_date`, `featured`, `display_order`
)
SELECT
  u.id,
  'Malware Analysis Toolkit',
  'malware-analysis-toolkit',
  'A Python helper suite for static triage, string extraction, and Ghidra workflow automation in an isolated Linux lab.',
  '## Overview\n\nCollection of scripts that streamline repetitive steps when triaging suspicious binaries in a **Linux** malware lab.\n\n## Components\n\n- PE/ELF header summarizer\n- YARA rule runner with match reporting\n- Ghidra headless export wrapper for function lists and strings\n- Safe-copy utility that preserves timestamps and hashes\n\n## Notes\n\nAll samples are analyzed in an air-gapped VM. This project documents workflow automation — not a substitute for full dynamic analysis.',
  'malware_analysis', 'completed', 'published', 'public',
  'https://github.com/example/student1/malware-analysis-toolkit',
  '2025-01-10', '2025-04-30', 0, 30
FROM `users` u WHERE u.username = 'student1';

-- D) Draft private project — must NOT be public
INSERT IGNORE INTO `projects` (
  `user_id`, `title`, `slug`, `short_description`, `description`,
  `project_type`, `status`, `publish_status`, `visibility`,
  `featured`, `display_order`
)
SELECT
  u.id,
  'Unfinished Security Tool',
  'unfinished-tool',
  'Work-in-progress automation script — not ready for review.',
  'Early prototype for log parsing and alert enrichment. **Draft only** — documentation and tests are incomplete.',
  'automation', 'in_progress', 'draft', 'private',
  0, 99
FROM `users` u WHERE u.username = 'student1';

-- ---------------------------------------------------------------------------
-- Project technologies
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'PHP'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Apache'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'MySQL'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Linux'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Python'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'mini-network-scanner';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Scapy'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'mini-network-scanner';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Linux'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'mini-network-scanner';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Python'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'malware-analysis-toolkit';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Ghidra'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'malware-analysis-toolkit';

INSERT IGNORE INTO `project_technologies` (`project_id`, `technology`)
SELECT p.id, 'Linux'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
WHERE p.slug = 'malware-analysis-toolkit';

-- ---------------------------------------------------------------------------
-- Project skills
-- ---------------------------------------------------------------------------

-- A) Secure File Upload Lab
INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'web-security'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'secure-coding'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'secondary', 0.75
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'access-control'
WHERE p.slug = 'secure-file-upload-lab';

-- B) Network Scanner
INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'networking'
WHERE p.slug = 'mini-network-scanner';

INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'network-recon'
WHERE p.slug = 'mini-network-scanner';

INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'secondary', 0.80
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'network-security'
WHERE p.slug = 'mini-network-scanner';

-- C) Malware Analysis Toolkit
INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'malware-analysis'
WHERE p.slug = 'malware-analysis-toolkit';

INSERT IGNORE INTO `project_skills` (`project_id`, `skill_id`, `importance`, `weight`)
SELECT p.id, s.id, 'primary', 1.00
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `skills` s ON s.slug = 'reverse-engineering'
WHERE p.slug = 'malware-analysis-toolkit';

-- ---------------------------------------------------------------------------
-- Project challenges (related arena challenges)
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `project_challenges` (`project_id`, `challenge_id`, `relationship_type`)
SELECT p.id, c.id, 'related_to'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `challenges` c ON c.slug = 'sql-injection-basics'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_challenges` (`project_id`, `challenge_id`, `relationship_type`)
SELECT p.id, c.id, 'related_to'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `challenges` c ON c.slug = 'cross-site-scripting-fundamentals'
WHERE p.slug = 'secure-file-upload-lab';

INSERT IGNORE INTO `project_challenges` (`project_id`, `challenge_id`, `relationship_type`)
SELECT p.id, c.id, 'related_to'
FROM `projects` p
JOIN `users` u ON u.id = p.user_id AND u.username = 'student1'
JOIN `challenges` c ON c.slug = 'nmap-fundamentals'
WHERE p.slug = 'mini-network-scanner';

-- ---------------------------------------------------------------------------
-- Portfolio featured skills (student1)
-- ---------------------------------------------------------------------------

INSERT IGNORE INTO `portfolio_featured_skills` (`user_id`, `skill_id`, `display_order`)
SELECT u.id, s.id, 10
FROM `users` u
JOIN `skills` s ON s.slug = 'web-security'
WHERE u.username = 'student1';

INSERT IGNORE INTO `portfolio_featured_skills` (`user_id`, `skill_id`, `display_order`)
SELECT u.id, s.id, 20
FROM `users` u
JOIN `skills` s ON s.slug = 'linux'
WHERE u.username = 'student1';

INSERT IGNORE INTO `portfolio_featured_skills` (`user_id`, `skill_id`, `display_order`)
SELECT u.id, s.id, 30
FROM `users` u
JOIN `skills` s ON s.slug = 'networking'
WHERE u.username = 'student1';

INSERT IGNORE INTO `portfolio_featured_skills` (`user_id`, `skill_id`, `display_order`)
SELECT u.id, s.id, 40
FROM `users` u
JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE u.username = 'student1';
