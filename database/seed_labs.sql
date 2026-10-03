-- CySkillShare Phase 7: Cyber Labs seed
-- DEVELOPMENT ONLY
SET NAMES utf8mb4;
USE `cyskillshare`;

INSERT IGNORE INTO `lab_categories` (`slug`, `name`, `description`, `display_order`) VALUES
  ('web-security', 'Web Security', 'Hands-on web application security labs', 10),
  ('network-security', 'Network Security', 'Traffic analysis and network investigation', 20),
  ('digital-forensics', 'Digital Forensics', 'Artifact analysis without executing untrusted code', 30),
  ('malware-analysis', 'Malware Analysis', 'Safe static-analysis oriented labs', 40),
  ('reverse-engineering', 'Reverse Engineering', 'Binary analysis practice', 50),
  ('osint', 'OSINT', 'Open-source intelligence exercises', 60),
  ('linux', 'Linux', 'Linux security investigation labs', 70),
  ('windows', 'Windows', 'Windows security labs', 80),
  ('cloud-security', 'Cloud Security', 'Cloud security practice', 90),
  ('incident-response', 'Incident Response', 'IR investigation labs', 100),
  ('secure-coding', 'Secure Coding', 'Secure development labs', 110),
  ('cryptography', 'Cryptography', 'Applied crypto labs', 120);

INSERT IGNORE INTO `lab_templates` (`slug`, `name`, `description`, `runtime_type`, `configuration`) VALUES
  ('single_web_app', 'Single Web App', 'One simulated web target via lab gateway', 'simulated_web',
   JSON_OBJECT('gateway','browser','internet',false,'max_containers',1)),
  ('forensics_workspace', 'Forensics Workspace', 'Read-only artifact workspace (simulated)', 'simulated_forensics',
   JSON_OBJECT('gateway','browser','internet',false)),
  ('network_analysis', 'Network Analysis', 'PCAP / log investigation workspace', 'simulated_network',
   JSON_OBJECT('gateway','browser','internet',false)),
  ('incident_response', 'Incident Response', 'IR investigation with logs and timeline tasks', 'simulated_ir',
   JSON_OBJECT('gateway','browser','internet',false)),
  ('linux_privilege_lab', 'Linux Investigation', 'Linux host investigation (simulated)', 'simulated_linux',
   JSON_OBJECT('gateway','browser','internet',false));


INSERT IGNORE INTO `labs` (
  `title`, `slug`, `short_description`, `description`, `learning_objectives`,
  `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`,
  `author_id`, `featured`, `lifetime_minutes`, `environment_type`, `published_at`, `max_points`
)
SELECT
  'Vulnerable Web Application', 'vulnerable-web-application', 'Investigate a simulated vulnerable web app: recon, identify SQLi, analyze logs, and recommend remediation.', '## Scenario

You are assessing a small campus club web application. The environment is **simulated** inside the CySkillShare lab gateway — it does not touch university production systems.

## Goals

Work through reconnaissance, identify a vulnerable endpoint, confirm safe exploitation in the lab, analyze access logs, and recommend remediation.

## Environment

Use the **Lab Environment** panel after starting the lab. Instance-specific values (target IP, tokens) appear there.', '- Identify suspicious HTTP requests
- Locate a SQL injection vulnerability safely in a lab context
- Analyze web server access logs
- Recommend parameterized-query remediation',
  lc.id, lt.id, 'intermediate', 45, 'published', 'public',
  u.id, 1, 60, 'browser', NOW(), 100
FROM `users` u
JOIN `lab_categories` lc ON lc.slug = 'web-security'
JOIN `lab_templates` lt ON lt.slug = 'single_web_app'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.0 FROM `labs` l JOIN `skills` s ON s.slug = 'web-security'
WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.2 FROM `labs` l JOIN `skills` s ON s.slug = 'sql-injection'
WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 0.8 FROM `labs` l JOIN `skills` s ON s.slug = 'secure-coding'
WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_prerequisites` (`lab_id`, `skill_id`, `minimum_level`)
SELECT l.id, s.id, 1 FROM `labs` l JOIN `skills` s ON s.slug = 'web-security'
WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'Web Target', 'web', 80, 80, 'http', 0, 1
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'Access Logs', 'logs', 8081, 8081, 'http', 1, 1
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify the target IP', 'identify-target-ip', 'Open the Lab Environment. What is the internal target IP assigned to your instance?', 'investigation', 1, 1, 10
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "target_ip"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'identify-target-ip';


INSERT IGNORE INTO `lab_task_hints` (`task_id`, `title`, `content`, `hint_level`, `penalty`)
SELECT t.id, 'Where to look', 'Check the Environment panel header for Target IP.', 1, 3
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'identify-target-ip';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`, `options_json`)
SELECT l.id, 'Find the login endpoint', 'find-login-endpoint', 'Which path handles authentication on the target application?', 'multiple_choice', 2, 1, 10,
  CAST('[{"id": "a", "label": "/admin"}, {"id": "b", "label": "/login"}, {"id": "c", "label": "/api/users"}, {"id": "d", "label": "/dashboard"}]' AS JSON)
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'multiple_choice', CAST('{"correct_option_ids": ["b"]}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'find-login-endpoint';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'vulnerable-web-application'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'identify-target-ip'
WHERE t.slug = 'find-login-endpoint';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify the injectable parameter', 'spot-sqli-param', 'Which request parameter is vulnerable to SQL injection on the login form?', 'question', 3, 1, 15
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'exact', CAST('{"value_hash": "16f78a7d6317f102bbd95fc9a4f3ff2e3249287690b8bdad6b7810f82b34ace3", "case_sensitive": false}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'spot-sqli-param';


INSERT IGNORE INTO `lab_task_hints` (`task_id`, `title`, `content`, `hint_level`, `penalty`)
SELECT t.id, 'Form fields', 'Look at the login form field names in the simulated target.', 1, 5
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'spot-sqli-param';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'vulnerable-web-application'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'find-login-endpoint'
WHERE t.slug = 'spot-sqli-param';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Retrieve the lab flag', 'capture-flag', 'After confirming the vulnerability in the lab environment, submit the lab flag shown on the success panel.', 'flag', 4, 1, 25
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'flag', CAST('{"secret_key": "flag"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'capture-flag';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'vulnerable-web-application'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'spot-sqli-param'
WHERE t.slug = 'capture-flag';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify the attacker IP in logs', 'log-source-ip', 'In the Access Logs view, which source IP generated the suspicious login attempts?', 'log_analysis', 5, 1, 15
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "attacker_ip"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'log-source-ip';


INSERT IGNORE INTO `lab_task_hints` (`task_id`, `title`, `content`, `hint_level`, `penalty`)
SELECT t.id, 'Log filter', 'Look for repeated 401/200 patterns around /login.', 1, 5
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'log-source-ip';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'vulnerable-web-application'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'capture-flag'
WHERE t.slug = 'log-source-ip';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Recommend remediation', 'remediation', 'What is the primary remediation for this class of vulnerability? Answer with the two-word phrase used in the environment notes (lowercase).', 'question', 6, 1, 15
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'exact', CAST('{"value_hash": "fae60eefb2da839840d670d73305614527299eee1270a8500e30eaedf5ad7036", "case_sensitive": false}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'remediation';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'vulnerable-web-application'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'log-source-ip'
WHERE t.slug = 'remediation';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Optional: short incident note', 'optional-report', 'In one sentence, summarize initial access. This task is optional and uses manual review.', 'report', 7, 0, 10
FROM `labs` l WHERE l.slug = 'vulnerable-web-application';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'manual', CAST('{}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'vulnerable-web-application' AND t.slug = 'optional-report';


INSERT IGNORE INTO `labs` (
  `title`, `slug`, `short_description`, `description`, `learning_objectives`,
  `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`,
  `author_id`, `featured`, `lifetime_minutes`, `environment_type`, `published_at`, `max_points`
)
SELECT
  'Suspicious Network Traffic', 'suspicious-network-traffic', 'Analyze a simulated PCAP summary to identify a suspicious host, protocol, and IOC.', '## Scenario

A SOC analyst handed you a short capture summary from an isolated lab network. Use the Lab Environment to inspect the summarized flows (no live university network access).', '- Identify a suspicious host from traffic summaries
- Recognize the protocol used for C2-like communication
- Extract a simple IOC
- Document findings',
  lc.id, lt.id, 'beginner', 35, 'published', 'public',
  u.id, 1, 60, 'browser', NOW(), 100
FROM `users` u
JOIN `lab_categories` lc ON lc.slug = 'network-security'
JOIN `lab_templates` lt ON lt.slug = 'network_analysis'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.0 FROM `labs` l JOIN `skills` s ON s.slug = 'network-security'
WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_prerequisites` (`lab_id`, `skill_id`, `minimum_level`)
SELECT l.id, s.id, 1 FROM `labs` l JOIN `skills` s ON s.slug = 'network-security'
WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'PCAP Summary', 'pcap_view', 80, 80, 'http', 0, 1
FROM `labs` l WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify the suspicious host', 'suspect-host', 'Which internal IP generates unusual outbound connections?', 'investigation', 1, 1, 20
FROM `labs` l WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "suspect_host"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'suspicious-network-traffic' AND t.slug = 'suspect-host';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify the protocol', 'protocol', 'What protocol carries the suspicious payload? (lowercase abbreviation)', 'question', 2, 1, 20
FROM `labs` l WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'exact', CAST('{"value_hash": "dd75a9d6fb309c4399fe425cd5f90ff95eba135d6924fb91766ee5d3726b168a", "case_sensitive": false}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'suspicious-network-traffic' AND t.slug = 'protocol';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'suspicious-network-traffic'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'suspect-host'
WHERE t.slug = 'protocol';


INSERT IGNORE INTO `lab_task_hints` (`task_id`, `title`, `content`, `hint_level`, `penalty`)
SELECT t.id, 'Look at ports', 'Watch for queries that do not look like normal name resolution.', 1, 5
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'suspicious-network-traffic' AND t.slug = 'protocol';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Extract the IOC domain', 'ioc', 'Submit the suspicious domain observed in the capture summary.', 'investigation', 3, 1, 30
FROM `labs` l WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "ioc_domain"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'suspicious-network-traffic' AND t.slug = 'ioc';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'suspicious-network-traffic'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'protocol'
WHERE t.slug = 'ioc';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Submit investigation flag', 'flag', 'Submit the investigation completion flag from the environment.', 'flag', 4, 1, 30
FROM `labs` l WHERE l.slug = 'suspicious-network-traffic';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'flag', CAST('{"secret_key": "flag"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'suspicious-network-traffic' AND t.slug = 'flag';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'suspicious-network-traffic'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'ioc'
WHERE t.slug = 'flag';


INSERT IGNORE INTO `labs` (
  `title`, `slug`, `short_description`, `description`, `learning_objectives`,
  `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`,
  `author_id`, `featured`, `lifetime_minutes`, `environment_type`, `published_at`, `max_points`
)
SELECT
  'Compromised Workstation', 'compromised-workstation', 'Static forensics triage: timeline, persistence, and suspicious file identification using read-only artifacts.', '## Scenario

You receive hashed, read-only artifact summaries from a compromised workstation. Do **not** execute any binaries — analyze the provided metadata only.', '- Build a simple timeline from artifact timestamps
- Identify a persistence mechanism
- Locate a suspicious filename
- Write concise findings',
  lc.id, lt.id, 'intermediate', 50, 'published', 'public',
  u.id, 0, 60, 'browser', NOW(), 100
FROM `users` u
JOIN `lab_categories` lc ON lc.slug = 'digital-forensics'
JOIN `lab_templates` lt ON lt.slug = 'forensics_workspace'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.2 FROM `labs` l JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 0.8 FROM `labs` l JOIN `skills` s ON s.slug = 'incident-response'
WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'Artifact Browser', 'forensics', 80, 80, 'http', 0, 1
FROM `labs` l WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Identify persistence', 'persist-mech', 'Which persistence mechanism was used? Answer exactly as shown in the artifact notes (lowercase with hyphen).', 'file_analysis', 1, 1, 25
FROM `labs` l WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'exact', CAST('{"value_hash": "eba32383c39522370d98102be229e9a944526073ebd7b1e5a925498a5e4607ca", "case_sensitive": false}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'compromised-workstation' AND t.slug = 'persist-mech';


INSERT IGNORE INTO `lab_task_hints` (`task_id`, `title`, `content`, `hint_level`, `penalty`)
SELECT t.id, 'Registry', 'Check autorun-related artifact categories.', 1, 5
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'compromised-workstation' AND t.slug = 'persist-mech';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Find the suspicious filename', 'bad-file', 'What is the suspicious executable filename?', 'investigation', 2, 1, 25
FROM `labs` l WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "malware_name"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'compromised-workstation' AND t.slug = 'bad-file';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'compromised-workstation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'persist-mech'
WHERE t.slug = 'bad-file';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'First-seen hour (UTC)', 'first-seen', 'According to the timeline, in which UTC hour (0-23 as two digits) did the suspicious file first appear?', 'question', 3, 1, 20
FROM `labs` l WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "first_seen_hour"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'compromised-workstation' AND t.slug = 'first-seen';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'compromised-workstation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'bad-file'
WHERE t.slug = 'first-seen';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Forensics flag', 'flag', 'Submit the completion flag from the environment.', 'flag', 4, 1, 30
FROM `labs` l WHERE l.slug = 'compromised-workstation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'flag', CAST('{"secret_key": "flag"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'compromised-workstation' AND t.slug = 'flag';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'compromised-workstation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'first-seen'
WHERE t.slug = 'flag';


INSERT IGNORE INTO `labs` (
  `title`, `slug`, `short_description`, `description`, `learning_objectives`,
  `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`,
  `author_id`, `featured`, `lifetime_minutes`, `environment_type`, `published_at`, `max_points`
)
SELECT
  'Web Server Compromise', 'web-server-compromise', 'Incident response lab: analyze logs, determine initial access, impact, and produce a timeline.', '## Scenario

A club web server shows signs of compromise. Use the simulated IR console to analyze logs and reconstruct the attack path.', '- Identify initial access from logs
- Trace attacker activity
- Determine impact scope
- Produce a short incident timeline',
  lc.id, lt.id, 'advanced', 60, 'published', 'public',
  u.id, 1, 60, 'browser', NOW(), 100
FROM `users` u
JOIN `lab_categories` lc ON lc.slug = 'incident-response'
JOIN `lab_templates` lt ON lt.slug = 'incident_response'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.2 FROM `labs` l JOIN `skills` s ON s.slug = 'incident-response'
WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 0.8 FROM `labs` l JOIN `skills` s ON s.slug = 'web-security'
WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 0.7 FROM `labs` l JOIN `skills` s ON s.slug = 'linux'
WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'IR Console', 'ir_console', 80, 80, 'http', 0, 1
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Initial access vector', 'initial-access', 'What was the initial access vector? (two words, lowercase)', 'log_analysis', 1, 1, 20
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'exact', CAST('{"value_hash": "0fa499aba8c8ffbe5e3a783601595f78853e54d5dcc46fcbfae275b904e29992", "case_sensitive": false}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'web-server-compromise' AND t.slug = 'initial-access';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Locate the webshell path', 'webshell-path', 'Submit the webshell path shown in the IR console.', 'investigation', 2, 1, 20
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "webshell_path"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'web-server-compromise' AND t.slug = 'webshell-path';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'web-server-compromise'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'initial-access'
WHERE t.slug = 'webshell-path';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Compromised account', 'impact-user', 'Which account was compromised?', 'investigation', 3, 1, 20
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "compromised_user"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'web-server-compromise' AND t.slug = 'impact-user';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'web-server-compromise'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'webshell-path'
WHERE t.slug = 'impact-user';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Compromise hour (UTC)', 'timeline-hour', 'In which UTC hour (two digits) did initial access occur?', 'question', 4, 1, 15
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "access_hour"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'web-server-compromise' AND t.slug = 'timeline-hour';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'web-server-compromise'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'impact-user'
WHERE t.slug = 'timeline-hour';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'IR completion flag', 'flag', 'Submit the IR lab flag.', 'flag', 5, 1, 25
FROM `labs` l WHERE l.slug = 'web-server-compromise';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'flag', CAST('{"secret_key": "flag"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'web-server-compromise' AND t.slug = 'flag';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'web-server-compromise'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'timeline-hour'
WHERE t.slug = 'flag';


INSERT IGNORE INTO `labs` (
  `title`, `slug`, `short_description`, `description`, `learning_objectives`,
  `category_id`, `template_id`, `difficulty`, `estimated_minutes`, `status`, `visibility`,
  `author_id`, `featured`, `lifetime_minutes`, `environment_type`, `published_at`, `max_points`
)
SELECT
  'Linux Security Investigation', 'linux-security-investigation', 'Investigate a simulated Linux host: processes, auth logs, permissions, and persistence.', '## Scenario

A Linux jump host in the lab range behaved oddly. Use the simulated host console — there is no access to real campus servers.', '- Review process listings for anomalies
- Correlate authentication logs
- Identify risky file permissions
- Spot a persistence artifact',
  lc.id, lt.id, 'intermediate', 40, 'published', 'public',
  u.id, 0, 60, 'browser', NOW(), 100
FROM `users` u
JOIN `lab_categories` lc ON lc.slug = 'linux'
JOIN `lab_templates` lt ON lt.slug = 'linux_privilege_lab'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 1.2 FROM `labs` l JOIN `skills` s ON s.slug = 'linux'
WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_skills` (`lab_id`, `skill_id`, `weight`)
SELECT l.id, s.id, 0.6 FROM `labs` l JOIN `skills` s ON s.slug = 'incident-response'
WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_services` (`lab_id`, `name`, `service_type`, `internal_port`, `display_port`, `protocol`, `display_order`, `is_student_visible`)
SELECT l.id, 'Host Console', 'linux_console', 80, 80, 'http', 0, 1
FROM `labs` l WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Suspicious process name', 'bad-process', 'Which process name looks malicious in the process list?', 'investigation', 1, 1, 25
FROM `labs` l WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "bad_process"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'linux-security-investigation' AND t.slug = 'bad-process';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Brute-forced account', 'auth-user', 'Which username appears in failed SSH attempts most often?', 'log_analysis', 2, 1, 25
FROM `labs` l WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "ssh_target_user"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'linux-security-investigation' AND t.slug = 'auth-user';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'linux-security-investigation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'bad-process'
WHERE t.slug = 'auth-user';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'World-writable path', 'perm-path', 'Which path is incorrectly world-writable?', 'configuration', 3, 1, 25
FROM `labs` l WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'instance_secret', CAST('{"secret_key": "world_writable"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'linux-security-investigation' AND t.slug = 'perm-path';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'linux-security-investigation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'auth-user'
WHERE t.slug = 'perm-path';


INSERT IGNORE INTO `lab_tasks` (`lab_id`, `title`, `slug`, `description`, `task_type`, `display_order`, `required`, `points`)
SELECT l.id, 'Linux lab flag', 'flag', 'Submit the completion flag.', 'flag', 4, 1, 25
FROM `labs` l WHERE l.slug = 'linux-security-investigation';


INSERT IGNORE INTO `lab_task_validations` (`task_id`, `validation_type`, `validation_config`)
SELECT t.id, 'flag', CAST('{"secret_key": "flag"}' AS JSON)
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id
WHERE l.slug = 'linux-security-investigation' AND t.slug = 'flag';


INSERT IGNORE INTO `lab_task_dependencies` (`task_id`, `depends_on_task_id`)
SELECT t.id, d.id
FROM `lab_tasks` t
JOIN `labs` l ON l.id = t.lab_id AND l.slug = 'linux-security-investigation'
JOIN `lab_tasks` d ON d.lab_id = l.id AND d.slug = 'perm-path'
WHERE t.slug = 'flag';


INSERT IGNORE INTO `writeup_labs` (`writeup_id`, `lab_id`)
SELECT w.id, l.id FROM `writeups` w
JOIN `labs` l ON l.slug = 'vulnerable-web-application'
WHERE w.slug = 'sql-injection-beyond-the-basics';

