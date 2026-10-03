-- CySkillShare Phase 4: Skill Tree seed data
-- DEVELOPMENT ONLY — do not use in production.

SET NAMES utf8mb4;
USE `cyskillshare`;

-- ---------------------------------------------------------------------------
-- 1. Skill levels (0–5)
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `skill_levels` (`level`, `name`, `description`, `minimum_score`, `display_order`) VALUES
  (0, 'Not Started',   'No demonstrated evidence yet',                0,  0),
  (1, 'Beginner',      'Foundational exposure with guided practice', 10, 10),
  (2, 'Developing',    'Growing competence on routine tasks',        25, 20),
  (3, 'Intermediate',  'Independent work on moderate problems',        45, 30),
  (4, 'Advanced',      'Strong performance on complex scenarios',      70, 40),
  (5, 'Demonstrated',  'Portfolio-ready mastery with peer recognition', 90, 50);

-- ---------------------------------------------------------------------------
-- 2. Skill categories
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `skill_categories` (`name`, `slug`, `description`, `display_order`, `is_active`) VALUES
  ('Foundations',          'foundations',          'Core computing and security concepts every practitioner needs', 10, 1),
  ('Offensive Security',   'offensive-security',   'Ethical hacking, exploitation, and penetration testing skills', 20, 1),
  ('Defensive Security',   'defensive-security',   'Detection, monitoring, and incident response capabilities',     30, 1),
  ('Security Analysis',    'security-analysis',    'Forensics, malware analysis, and reverse engineering',          40, 1),
  ('Security Engineering', 'security-engineering', 'Secure design, coding, and cloud architecture',               50, 1),
  ('Reconnaissance',       'reconnaissance',       'OSINT and pre-engagement information gathering',              60, 1);

-- ---------------------------------------------------------------------------
-- 3. Skills (full tree)
-- ---------------------------------------------------------------------------

-- Foundations (no parent)
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Linux', 'linux',
  'Linux command-line proficiency, file permissions, process management, and system administration fundamentals. Security practitioners rely on Linux daily for analysis, scripting, and server hardening. Strong CLI skills accelerate every other domain in the skill tree.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'foundations';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Windows', 'windows',
  'Windows architecture, registry, services, event logs, and user account models. Many enterprise environments run Windows endpoints and servers, making platform knowledge essential for blue and purple teams. Understanding Windows internals supports forensics, incident response, and privilege escalation analysis.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'foundations';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Networking', 'networking',
  'TCP/IP, DNS, HTTP, routing, switching, and network troubleshooting at the packet level. Nearly every attack and defense technique traverses a network, so protocol literacy is non-negotiable. Packet analysis and service enumeration build directly on this foundation.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'foundations';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Security Fundamentals', 'security-fundamentals',
  'Core principles including the CIA triad, threat modeling, authentication, authorization, and basic cryptography. These concepts frame every technical skill and help you reason about risk systematically. A solid fundamentals base prevents chasing tools without understanding why they matter.',
  40, 1 FROM `skill_categories` sc WHERE sc.slug = 'foundations';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Operating Systems', 'operating-systems',
  'How kernels, memory, processes, threads, and privilege models work across Linux and Windows. OS internals explain why exploits succeed and how defenders detect abnormal behavior. This skill bridges user-level tools and low-level security analysis.',
  50, 1 FROM `skill_categories` sc WHERE sc.slug = 'foundations';

-- Offensive Security
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Web Security', 'web-security',
  'Broad coverage of web application attack surfaces, HTTP semantics, session management, and the OWASP Top 10. Web apps remain the most common external entry point for attackers. Parent skill for injection, XSS, CSRF, and access-control specializations.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'offensive-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'SQL Injection', 'sql-injection',
  'Exploiting unsanitized database queries to bypass authentication, extract data, or modify records. SQLi teaches why parameterized queries and input validation are mandatory, not optional. Practice spans error-based, union-based, and blind injection techniques in controlled labs.',
  10, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'offensive-security' AND p.slug = 'web-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'Cross-Site Scripting (XSS)', 'xss',
  'Injecting malicious scripts into web pages viewed by other users to steal sessions or perform actions on their behalf. XSS highlights the gap between server-side trust and browser-side execution. Mitigation requires output encoding, Content Security Policy, and secure cookie flags.',
  20, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'offensive-security' AND p.slug = 'web-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'Cross-Site Request Forgery (CSRF)', 'csrf',
  'Tricking authenticated users into submitting unintended requests that the application trusts. CSRF attacks abuse the browser''s automatic inclusion of session cookies. Defenses include anti-CSRF tokens, SameSite cookies, and verifying request origin.',
  30, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'offensive-security' AND p.slug = 'web-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'Broken Access Control', 'access-control',
  'Exploiting flaws in authorization logic to access resources or perform actions beyond your privilege level. IDOR, privilege escalation, and forced browsing are common manifestations. Secure design enforces authorization on every request, not just the UI.',
  40, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'offensive-security' AND p.slug = 'web-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Network Security', 'network-security',
  'Network-layer attacks, protocol weaknesses, man-in-the-middle scenarios, and packet-level exploitation. Scanning, sniffing, and traffic analysis skills support both offensive assessments and defensive monitoring. Strong networking fundamentals are prerequisite for meaningful progress here.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'offensive-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Penetration Testing', 'penetration-testing',
  'Structured methodology for authorized security assessments: reconnaissance, exploitation, post-exploitation, and reporting. Pen testers combine technical skills with communication and scope management. Findings must be reproducible, risk-rated, and actionable for stakeholders.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'offensive-security';

-- Defensive Security
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Incident Response', 'incident-response',
  'Detecting, containing, eradicating, and recovering from security incidents using established playbooks. IR teams balance speed with evidence preservation for later analysis. Effective response reduces dwell time and limits business impact.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'defensive-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Security Monitoring', 'security-monitoring',
  'SIEM configuration, log aggregation, alert tuning, and continuous visibility across infrastructure. Good monitoring turns raw telemetry into actionable signals without alert fatigue. Correlation rules and baselines help distinguish noise from genuine threats.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'defensive-security';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Threat Detection', 'threat-detection',
  'Identifying adversary behavior through signatures, behavioral analytics, and threat intelligence feeds. Detection engineering maps attacker TTPs to observable events in your environment. Continuous improvement closes gaps as threats evolve.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'defensive-security';

-- Security Analysis
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Digital Forensics', 'digital-forensics',
  'Collecting, preserving, and analyzing digital evidence from disks, logs, and file system artifacts. Chain of custody and integrity verification are as important as technical analysis. Forensic findings often support incident response and legal proceedings.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-analysis';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'Memory Analysis', 'memory-analysis',
  'Volatile memory forensics: process dumps, kernel structures, injected code, and rootkit detection in RAM. Memory captures ephemeral evidence that disappears on reboot. Tools like Volatility help reconstruct running system state at capture time.',
  10, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'security-analysis' AND p.slug = 'digital-forensics';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, p.id, 'Network Forensics', 'network-forensics',
  'Reconstructing security events from PCAPs, NetFlow, firewall logs, and proxy records. Network forensics connects endpoints and timelines when disk evidence is unavailable or incomplete. Packet-level detail reveals C2 channels, exfiltration, and lateral movement.',
  20, 1 FROM `skill_categories` sc, `skills` p
WHERE sc.slug = 'security-analysis' AND p.slug = 'digital-forensics';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Malware Analysis', 'malware-analysis',
  'Static and dynamic analysis of suspicious binaries, scripts, and droppers to understand behavior and intent. Analysts extract indicators of compromise for detection rules and incident scoping. Safe lab environments isolate samples from production networks.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-analysis';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Reverse Engineering', 'reverse-engineering',
  'Disassembly, debugging, and understanding compiled code without access to source. RE skills support malware analysis, vulnerability research, and legacy system audits. Patience and systematic annotation turn opaque binaries into readable logic.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-analysis';

-- Reconnaissance
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'OSINT', 'osint',
  'Gathering intelligence from publicly available sources: social profiles, DNS records, certificate transparency, and leaked data. OSINT supports threat hunting, due diligence, and pre-engagement reconnaissance. Ethical boundaries and privacy laws always apply.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'reconnaissance';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Network Reconnaissance', 'network-recon',
  'Host discovery, port scanning, service enumeration, and banner grabbing on target networks. Network recon maps the attack surface before deeper testing begins. Results feed prioritization for vulnerability assessment and penetration testing.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'reconnaissance';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Web Reconnaissance', 'web-recon',
  'Mapping web applications: directory brute-forcing, technology fingerprinting, and subdomain enumeration. Web recon reveals hidden endpoints, outdated frameworks, and misconfigurations. Passive techniques minimize detection during authorized assessments.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'reconnaissance';

-- Security Engineering
INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Secure Coding', 'secure-coding',
  'Writing code that resists injection, XSS, and logic flaws through secure SDLC practices. Developers who understand attacks build safer defaults and fewer hotfixes. Code review, static analysis, and threat modeling integrate security early.',
  10, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-engineering';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Application Security', 'application-security',
  'Designing, testing, and hardening applications throughout the development lifecycle. AppSec spans architecture review, DAST/SAST tooling, and secure deployment pipelines. Shifting left reduces cost compared to post-release patching.',
  20, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-engineering';

INSERT IGNORE INTO `skills` (`category_id`, `parent_skill_id`, `name`, `slug`, `description`, `display_order`, `is_active`)
SELECT sc.id, NULL, 'Cloud Security', 'cloud-security',
  'Securing cloud IAM, storage, containers, serverless functions, and shared responsibility models. Misconfigurations—not hypervisor escapes—cause most cloud breaches. Infrastructure-as-code and policy-as-code enforce consistent baselines at scale.',
  30, 1 FROM `skill_categories` sc WHERE sc.slug = 'security-engineering';

-- ---------------------------------------------------------------------------
-- 4. Skill prerequisites
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'sql-injection' AND prereq.slug = 'web-security';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'xss' AND prereq.slug = 'web-security';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'csrf' AND prereq.slug = 'web-security';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'access-control' AND prereq.slug = 'web-security';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 2
FROM `skills` child, `skills` prereq
WHERE child.slug = 'memory-analysis' AND prereq.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'network-forensics' AND prereq.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'penetration-testing' AND prereq.slug = 'networking';

INSERT IGNORE INTO `skill_prerequisites` (`skill_id`, `prerequisite_skill_id`, `minimum_level`)
SELECT child.id, prereq.id, 1
FROM `skills` child, `skills` prereq
WHERE child.slug = 'penetration-testing' AND prereq.slug = 'security-fundamentals';

-- ---------------------------------------------------------------------------
-- 5. Skill requirements (evidence ladders)
-- ---------------------------------------------------------------------------

-- sql-injection
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'sql-injection';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'sql-injection';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 2, 'medium' FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'community_best_answer', 1, NULL FROM `skills` s WHERE s.slug = 'sql-injection';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 2, 'hard' FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'writeup', 2, NULL FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'sql-injection';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'instructor_verification', 1, NULL FROM `skills` s WHERE s.slug = 'sql-injection';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'sql-injection';

-- web-security
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'web-security';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'web-security';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'web-security';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 2, 'medium' FROM `skills` s WHERE s.slug = 'web-security';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'web-security';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 2, 'hard' FROM `skills` s WHERE s.slug = 'web-security';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'web-security';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'community_best_answer', 1, NULL FROM `skills` s WHERE s.slug = 'web-security';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'instructor_verification', 1, NULL FROM `skills` s WHERE s.slug = 'web-security';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'web-security';

-- linux
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'linux';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'linux';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'linux';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'lab', 1, NULL FROM `skills` s WHERE s.slug = 'linux';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 2, 'medium' FROM `skills` s WHERE s.slug = 'linux';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'linux';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'challenge_solved', 1, 'hard' FROM `skills` s WHERE s.slug = 'linux';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'instructor_verification', 1, NULL FROM `skills` s WHERE s.slug = 'linux';

-- networking
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'networking';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'networking';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'networking';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'lab', 1, NULL FROM `skills` s WHERE s.slug = 'networking';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 2, 'medium' FROM `skills` s WHERE s.slug = 'networking';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'networking';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'challenge_solved', 1, 'hard' FROM `skills` s WHERE s.slug = 'networking';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'networking';

-- digital-forensics
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'digital-forensics';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 2, 'medium' FROM `skills` s WHERE s.slug = 'digital-forensics';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'digital-forensics';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'instructor_verification', 1, NULL FROM `skills` s WHERE s.slug = 'digital-forensics';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'writeup', 2, NULL FROM `skills` s WHERE s.slug = 'digital-forensics';

-- osint (simpler ladder)
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 1, 'challenge_solved', 1, 'easy' FROM `skills` s WHERE s.slug = 'osint';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 2, 'challenge_solved', 2, 'easy' FROM `skills` s WHERE s.slug = 'osint';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'challenge_solved', 1, 'medium' FROM `skills` s WHERE s.slug = 'osint';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 3, 'writeup', 1, NULL FROM `skills` s WHERE s.slug = 'osint';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'challenge_solved', 1, 'hard' FROM `skills` s WHERE s.slug = 'osint';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 4, 'community_best_answer', 1, NULL FROM `skills` s WHERE s.slug = 'osint';

INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'event_participation', 1, NULL FROM `skills` s WHERE s.slug = 'osint';
INSERT IGNORE INTO `skill_requirements` (`skill_id`, `target_level`, `evidence_type`, `minimum_count`, `minimum_difficulty`)
SELECT s.id, 5, 'project', 1, NULL FROM `skills` s WHERE s.slug = 'osint';

-- ---------------------------------------------------------------------------
-- 6. Challenge → skill mappings (requires seed_arena.sql)
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'sql-injection-basics' AND s.slug = 'sql-injection';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'sql-injection-basics' AND s.slug = 'web-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'php-session-weakness' AND s.slug = 'access-control';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'php-session-weakness' AND s.slug = 'web-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'cross-site-scripting-fundamentals' AND s.slug = 'xss';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'cross-site-scripting-fundamentals' AND s.slug = 'web-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'broken-access-control' AND s.slug = 'access-control';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'broken-access-control' AND s.slug = 'web-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'nmap-fundamentals' AND s.slug = 'network-recon';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.6 FROM `challenges` c, `skills` s
WHERE c.slug = 'nmap-fundamentals' AND s.slug = 'networking';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'tcp-packet-investigation' AND s.slug = 'network-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'tcp-packet-investigation' AND s.slug = 'networking';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'wireshark-basics' AND s.slug = 'network-security';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'wireshark-basics' AND s.slug = 'networking';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'find-the-hidden-file' AND s.slug = 'digital-forensics';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'basic-file-metadata' AND s.slug = 'digital-forensics';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'pcap-investigation' AND s.slug = 'network-forensics';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.6 FROM `challenges` c, `skills` s
WHERE c.slug = 'pcap-investigation' AND s.slug = 'digital-forensics';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'linux-permissions' AND s.slug = 'linux';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'linux-permissions' AND s.slug = 'operating-systems';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'process-investigation' AND s.slug = 'linux';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'process-investigation' AND s.slug = 'operating-systems';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.7 FROM `challenges` c, `skills` s
WHERE c.slug = 'find-the-suspicious-process' AND s.slug = 'linux';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'find-the-suspicious-process' AND s.slug = 'malware-analysis';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.7 FROM `challenges` c, `skills` s
WHERE c.slug = 'caesar-cipher' AND s.slug = 'security-fundamentals';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.7 FROM `challenges` c, `skills` s
WHERE c.slug = 'base64-investigation' AND s.slug = 'security-fundamentals';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'weak-password-hash' AND s.slug = 'security-fundamentals';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.4 FROM `challenges` c, `skills` s
WHERE c.slug = 'weak-password-hash' AND s.slug = 'secure-coding';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'username-investigation' AND s.slug = 'osint';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'metadata-hunt' AND s.slug = 'osint';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'domain-recon-basics' AND s.slug = 'web-recon';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.5 FROM `challenges` c, `skills` s
WHERE c.slug = 'domain-recon-basics' AND s.slug = 'osint';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'string-hunting' AND s.slug = 'reverse-engineering';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'basic-binary-analysis' AND s.slug = 'reverse-engineering';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'simple-crackme' AND s.slug = 'reverse-engineering';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'suspicious-process' AND s.slug = 'malware-analysis';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 1.0 FROM `challenges` c, `skills` s
WHERE c.slug = 'static-analysis-basics' AND s.slug = 'malware-analysis';

INSERT IGNORE INTO `challenge_skills` (`challenge_id`, `skill_id`, `weight`)
SELECT c.id, s.id, 0.8 FROM `challenges` c, `skills` s
WHERE c.slug = 'persistence-investigation' AND s.slug = 'malware-analysis';

-- ---------------------------------------------------------------------------
-- 7. Thread → skill mappings (requires seed.sql community threads)
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `thread_skills` (`thread_id`, `skill_id`, `weight`)
SELECT t.id, s.id, 1.0 FROM `threads` t, `skills` s
WHERE t.title = 'Understanding prepared statements in PHP' AND s.slug = 'sql-injection';

INSERT IGNORE INTO `thread_skills` (`thread_id`, `skill_id`, `weight`)
SELECT t.id, s.id, 0.5 FROM `threads` t, `skills` s
WHERE t.title = 'Understanding prepared statements in PHP' AND s.slug = 'secure-coding';

INSERT IGNORE INTO `thread_skills` (`thread_id`, `skill_id`, `weight`)
SELECT t.id, s.id, 0.4 FROM `threads` t, `skills` s
WHERE t.title = 'Understanding prepared statements in PHP' AND s.slug = 'web-security';

INSERT IGNORE INTO `thread_skills` (`thread_id`, `skill_id`, `weight`)
SELECT t.id, s.id, 0.5 FROM `threads` t, `skills` s
WHERE t.title = 'How should I approach my first CTF?' AND s.slug = 'security-fundamentals';

-- ---------------------------------------------------------------------------
-- 8. User skill progress
-- Live Arena solves and community activity create skill_evidence automatically.
-- No fabricated challenge solves or user_skills rows are seeded here.
-- ---------------------------------------------------------------------------

-- ---------------------------------------------------------------------------
-- 9. Default skills visibility for all users
-- ---------------------------------------------------------------------------
UPDATE `users` SET `skills_visibility` = 'public';
