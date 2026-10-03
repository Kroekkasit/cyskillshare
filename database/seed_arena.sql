-- CySkillShare Phase 3: Cyber Arena seed data
-- DEVELOPMENT ONLY — do not use in production.

USE `cyskillshare`;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Challenge categories (12)
-- ---------------------------------------------------------------------------
INSERT INTO `challenge_categories` (`name`, `slug`, `description`, `icon`, `sort_order`) VALUES
  ('Web Security', 'web-security', 'OWASP Top 10, web app exploitation, and secure coding', 'globe', 10),
  ('Network Security', 'network-security', 'Packet analysis, scanning, and protocol investigation', 'network', 20),
  ('Digital Forensics', 'digital-forensics', 'Disk, memory, and artifact analysis', 'search', 30),
  ('OSINT', 'osint', 'Open-source intelligence and reconnaissance', 'eye', 40),
  ('Cryptography', 'cryptography', 'Classical ciphers, encoding, and password hashing', 'lock', 50),
  ('Reverse Engineering', 'reverse-engineering', 'Binary analysis and crackmes', 'cpu', 60),
  ('Malware Analysis', 'malware-analysis', 'Static and dynamic malware investigation', 'bug', 70),
  ('Linux', 'linux', 'Linux permissions, processes, and system forensics', 'terminal', 80),
  ('Windows', 'windows', 'Windows event logs, registry, and DFIR on Windows', 'monitor', 90),
  ('Cloud Security', 'cloud-security', 'Cloud misconfigurations and IAM issues', 'cloud', 100),
  ('Mobile Security', 'mobile-security', 'Android and iOS security fundamentals', 'smartphone', 110),
  ('Miscellaneous', 'miscellaneous', 'General cybersecurity puzzles and mixed topics', 'puzzle', 120);

-- ---------------------------------------------------------------------------
-- Additional tags for arena challenges
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `tags` (`name`, `slug`) VALUES
  ('sql', 'sql'),
  ('authentication', 'authentication'),
  ('tcp', 'tcp'),
  ('pcap', 'pcap'),
  ('crypto', 'crypto'),
  ('osint', 'osint');

-- ---------------------------------------------------------------------------
-- Challenges (25)
-- ---------------------------------------------------------------------------

-- DEV FLAG: FLAG{cyskillshare_sqli_001}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'SQL Injection Basics',
  'sql-injection-basics',
  'A vulnerable login form concatenates user input directly into a SQL query.\n\nYour task:\n- Identify the injection point in the username field\n- Bypass authentication without valid credentials\n- Extract the hidden flag from the database\n\n**Learning goals:**\n- Understand why string concatenation is dangerous\n- Practice comment-based and boolean-based injection\n- See why prepared statements are mandatory',
  cc.id, 'easy', 75,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_sqli_001}', 256),
  1, 1, 1, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'web-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_php_session_002}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'PHP Session Weakness',
  'php-session-weakness',
  'This PHP application stores session data in a predictable way and exposes a debug endpoint.\n\nInvestigate how session IDs are generated and whether session fixation or weak entropy allows privilege escalation.\n\nHints in the source comments mention `session.save_path` — start there.',
  cc.id, 'medium', 175,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_php_session_002}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'web-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_xss_003}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Cross-Site Scripting Fundamentals',
  'cross-site-scripting-fundamentals',
  'A student profile page reflects user-supplied bio text without proper encoding.\n\nDemonstrate a reflected/stored XSS payload that executes in the victim browser context and retrieves the flag from a hidden DOM element.\n\nDocument which context (HTML, attribute, JS) you targeted.',
  cc.id, 'easy', 100,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_xss_003}', 256),
  1, 1, 1, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'web-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_bac_004}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Broken Access Control',
  'broken-access-control',
  'An API endpoint checks authentication but fails to verify authorization. Regular users can access admin-only resources by manipulating object IDs.\n\nFind the `/api/report/{id}` endpoint and escalate to retrieve the administrator flag.\n\nThis mirrors OWASP A01 — Broken Access Control.',
  cc.id, 'medium', 200,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_bac_004}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'web-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_nmap_005}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Nmap Fundamentals',
  'nmap-fundamentals',
  'Scan the target lab host and identify the non-standard service running on a high port.\n\nConnect to the service banner to recover the flag.\n\nRecommended workflow:\n1. Host discovery\n2. Port scan (-sV for version detection)\n3. Service interaction',
  cc.id, 'easy', 50,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_nmap_005}', 256),
  1, 1, 1, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'network-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_tcp_006}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'TCP Packet Investigation',
  'tcp-packet-investigation',
  'Analyze the provided packet capture focusing on TCP three-way handshake anomalies and out-of-band data.\n\nOne connection carries the flag in an unexpected TCP segment after connection teardown.\n\nTools: Wireshark, tcpdump, or tshark.',
  cc.id, 'medium', 150,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_tcp_006}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'network-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_wireshark_007}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Wireshark Basics',
  'wireshark-basics',
  'Open the sample capture and follow the HTTP stream for a login request.\n\nThe flag is embedded in a custom response header sent by the server after a successful POST.\n\nPractice: Statistics → Protocol Hierarchy, Follow → HTTP Stream.',
  cc.id, 'easy', 75,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_wireshark_007}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'network-security' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_hidden_file_008}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Find the Hidden File',
  'find-the-hidden-file',
  'A disk image contains a deleted file that standard `ls` will not show.\n\nUse forensic carving or inode recovery to restore the artifact containing the flag.\n\nConsider: unallocated space, file slack, and alternate data streams.',
  cc.id, 'easy', 80,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_hidden_file_008}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'digital-forensics' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_metadata_009}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Basic File Metadata',
  'basic-file-metadata',
  'Examine the provided JPEG and PDF files for embedded metadata.\n\nThe flag may appear in EXIF tags, XMP, or document properties.\n\nTry: exiftool, strings, or online metadata viewers for lab practice.',
  cc.id, 'easy', 60,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_metadata_009}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'digital-forensics' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_pcap_010}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'PCAP Investigation',
  'pcap-investigation',
  'A security incident left behind a PCAP file with DNS exfiltration activity.\n\nReconstruct the exfiltrated data from DNS query subdomains and decode the flag.\n\nFocus on unusual query frequency and long subdomain labels.',
  cc.id, 'medium', 180,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_pcap_010}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'digital-forensics' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_linux_perm_011}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Linux Permissions',
  'linux-permissions',
  'On the lab VM, a misconfigured SUID binary allows reading a root-only flag file.\n\nEnumerate permissions with `find / -perm -4000 2>/dev/null` and identify the vulnerable binary.\n\nExplain why SUID on interpreters or scripts is dangerous.',
  cc.id, 'easy', 70,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_linux_perm_011}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'linux' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_process_012}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Process Investigation',
  'process-investigation',
  'A compromised Linux host runs an unexpected background process.\n\nUse `ps`, `/proc`, and `lsof` to identify the malicious process and extract the flag from its command-line arguments or open files.',
  cc.id, 'medium', 160,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_process_012}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'linux' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_susp_proc_013}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Find the Suspicious Process',
  'find-the-suspicious-process',
  'Review the process list snapshot from a DFIR triage script.\n\nOne process masquerades as a system daemon but has an unusual parent PID and network connection.\n\nIdentify it and recover the flag from its environment block.',
  cc.id, 'hard', 280,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_susp_proc_013}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'linux' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_caesar_014}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Caesar Cipher',
  'caesar-cipher',
  'Decrypt the ciphertext below to recover the flag.\n\n```\nSYNT{plfskillfunu_fpnfne_014}\n```\n\nThis is a simple substitution cipher with a fixed shift. Try all 26 rotations or use frequency analysis.',
  cc.id, 'easy', 50,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_caesar_014}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'cryptography' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_base64_015}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Base64 Investigation',
  'base64-investigation',
  'Multiple layers of encoding hide the flag inside a log file.\n\nThe data may be Base64, URL-encoded, or nested. Decode iteratively until you find the `FLAG{...}` format.',
  cc.id, 'easy', 65,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_base64_015}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'cryptography' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_weak_hash_016}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Weak Password Hash',
  'weak-password-hash',
  'A leaked database contains MD5 password hashes. One hash corresponds to a service account password that reveals the flag when cracked.\n\nHash: `a1b2c3d4e5f6789012345678901234ab`\n\nUse a wordlist attack — the password is a common lab credential.',
  cc.id, 'medium', 190,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_weak_hash_016}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'cryptography' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_username_017}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Username Investigation',
  'username-investigation',
  'A threat actor uses the handle `cyber_kku_2024` across public platforms.\n\nPerform OSINT to find their profile on a code-sharing site and locate the flag in a public gist or repository README.',
  cc.id, 'easy', 55,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_username_017}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'osint' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_meta_hunt_018}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Metadata Hunt',
  'metadata-hunt',
  'A photo posted on social media contains GPS and device metadata linking to a location.\n\nExtract coordinates and convert them to the flag format: `FLAG{lat_lon}` rounded to 4 decimal places.',
  cc.id, 'medium', 155,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_meta_hunt_018}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'osint' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_domain_019}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Domain Recon Basics',
  'domain-recon-basics',
  'Perform passive reconnaissance on `lab.cyskillshare.local`.\n\nEnumerate subdomains and check certificate transparency logs. The flag is hidden in a TXT record on a discovered subdomain.',
  cc.id, 'medium', 170,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_domain_019}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'osint' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_strings_020}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'String Hunting',
  'string-hunting',
  'A stripped ELF binary holds the flag as a plaintext string.\n\nUse `strings`, `rabin2 -z`, or a hex editor to locate readable sequences without full disassembly.',
  cc.id, 'easy', 90,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_strings_020}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'reverse-engineering' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_binary_021}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Basic Binary Analysis',
  'basic-binary-analysis',
  'Load the 32-bit binary in Ghidra or IDA Free.\n\nLocate the `check_password` function and determine the expected input that prints the success message containing the flag.',
  cc.id, 'medium', 200,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_binary_021}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'reverse-engineering' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_crackme_022}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Simple Crackme',
  'simple-crackme',
  'This crackme validates a serial number with a custom algorithm.\n\nPatch or keygen the correct serial to unlock the flag. Dynamic analysis with GDB or x64dbg is encouraged.',
  cc.id, 'hard', 300,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_crackme_022}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'reverse-engineering' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_mal_proc_023}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Suspicious Process',
  'suspicious-process',
  'Analyze the provided memory dump for injected code and suspicious process handles.\n\nIdentify the malware process name and decode the C2 configuration blob to extract the flag.',
  cc.id, 'medium', 175,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_mal_proc_023}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'malware-analysis' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_static_mal_024}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Static Analysis Basics',
  'static-analysis-basics',
  'Inspect the PE file imports, sections, and strings without executing it.\n\nLook for suspicious API imports (VirtualAlloc, WriteProcessMemory) and embedded resources containing the flag.',
  cc.id, 'hard', 260,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_static_mal_024}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'malware-analysis' AND u.username = 'instructor1';

-- DEV FLAG: FLAG{cyskillshare_persist_025}
INSERT INTO `challenges` (`title`, `slug`, `description`, `category_id`, `difficulty`, `points`, `author_id`, `status`, `flag_type`, `flag_hash`, `case_sensitive`, `is_active`, `is_featured`, `published_at`)
SELECT
  'Persistence Investigation',
  'persistence-investigation',
  'A Windows host shows signs of persistence after a phishing incident.\n\nAnalyze autostart locations (Registry Run keys, Scheduled Tasks, Services) to find the malicious entry and recover the flag from its command-line payload.',
  cc.id, 'expert', 400,
  u.id, 'published', 'static',
  SHA2('FLAG{cyskillshare_persist_025}', 256),
  1, 1, 0, NOW()
FROM `challenge_categories` cc, `users` u
WHERE cc.slug = 'malware-analysis' AND u.username = 'instructor1';

-- ---------------------------------------------------------------------------
-- Challenge hints
-- ---------------------------------------------------------------------------

-- SQL Injection Basics (3 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Try entering a single quote in the username field and observe the error message.', 10
FROM `challenges` c WHERE c.slug = 'sql-injection-basics';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Use comment syntax (-- or #) to ignore the rest of the query after your payload.', 20
FROM `challenges` c WHERE c.slug = 'sql-injection-basics';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 3, 'A classic bypass: admin'' OR ''1''=''1 in the username with any password.', 30
FROM `challenges` c WHERE c.slug = 'sql-injection-basics';

-- PHP Session Weakness (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Check whether the application accepts a client-supplied PHPSESSID without regenerating on login.', 10
FROM `challenges` c WHERE c.slug = 'php-session-weakness';

-- XSS Fundamentals (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Inspect how the bio field is rendered — is output encoded for HTML context?', 10
FROM `challenges` c WHERE c.slug = 'cross-site-scripting-fundamentals';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Try a simple payload: <script>alert(1)</script> and check if it executes on profile view.', 20
FROM `challenges` c WHERE c.slug = 'cross-site-scripting-fundamentals';

-- Broken Access Control (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Increment the report ID in the URL while logged in as a regular student.', 10
FROM `challenges` c WHERE c.slug = 'broken-access-control';

-- Nmap Fundamentals (3 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Start with a TCP SYN scan: nmap -sS -T4 <target>', 10
FROM `challenges` c WHERE c.slug = 'nmap-fundamentals';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Enable version detection with -sV to identify services on non-standard ports.', 20
FROM `challenges` c WHERE c.slug = 'nmap-fundamentals';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 3, 'Look for a service above port 8000 — connect with netcat to read the banner.', 30
FROM `challenges` c WHERE c.slug = 'nmap-fundamentals';

-- TCP Packet Investigation (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Filter for tcp.flags.reset == 1 and examine packets immediately before RST.', 10
FROM `challenges` c WHERE c.slug = 'tcp-packet-investigation';

-- Find the Hidden File (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Use fls/icat from The Sleuth Kit or photorec to recover deleted inodes.', 10
FROM `challenges` c WHERE c.slug = 'find-the-hidden-file';

-- PCAP Investigation (3 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Filter DNS traffic: dns in Wireshark display filter.', 10
FROM `challenges` c WHERE c.slug = 'pcap-investigation';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Look for unusually long subdomain labels — they may encode hex or Base64.', 20
FROM `challenges` c WHERE c.slug = 'pcap-investigation';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 3, 'Concatenate subdomain labels in query order, then decode from hex.', 30
FROM `challenges` c WHERE c.slug = 'pcap-investigation';

-- Linux Permissions (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Search for SUID binaries owned by root: find / -perm -4000 -user root 2>/dev/null', 10
FROM `challenges` c WHERE c.slug = 'linux-permissions';

-- Process Investigation (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Sort processes by start time: ps aux --sort=start_time', 10
FROM `challenges` c WHERE c.slug = 'process-investigation';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Inspect /proc/<pid>/cmdline and /proc/<pid>/environ for hidden arguments.', 20
FROM `challenges` c WHERE c.slug = 'process-investigation';

-- Find the Suspicious Process (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Compare process names against known-good baselines — typosquatting is common.', 10
FROM `challenges` c WHERE c.slug = 'find-the-suspicious-process';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Check PPID — processes spawned from unexpected parents are suspicious.', 20
FROM `challenges` c WHERE c.slug = 'find-the-suspicious-process';

-- Caesar Cipher (3 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'The cipher shifts each letter by a fixed number of positions in the alphabet.', 10
FROM `challenges` c WHERE c.slug = 'caesar-cipher';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Try shift 13 first (ROT13) — if that fails, brute-force shifts 1–25.', 20
FROM `challenges` c WHERE c.slug = 'caesar-cipher';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 3, 'The shift value is 13 — decode SYNT to FLAG.', 30
FROM `challenges` c WHERE c.slug = 'caesar-cipher';

-- Base64 Investigation (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Look for strings ending in = or == — classic Base64 padding.', 10
FROM `challenges` c WHERE c.slug = 'base64-investigation';

-- Weak Password Hash (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'MD5 is fast — use hashcat -m 0 with rockyou.txt or a small custom wordlist.', 10
FROM `challenges` c WHERE c.slug = 'weak-password-hash';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'The password is a common lab default — try variations of "password" and "admin".', 20
FROM `challenges` c WHERE c.slug = 'weak-password-hash';

-- Metadata Hunt (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Run exiftool on the image and look for GPSLatitude/GPSLongitude tags.', 10
FROM `challenges` c WHERE c.slug = 'metadata-hunt';

-- Domain Recon Basics (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Use crt.sh or subfinder for passive subdomain enumeration.', 10
FROM `challenges` c WHERE c.slug = 'domain-recon-basics';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Query TXT records on discovered subdomains with dig or nslookup.', 20
FROM `challenges` c WHERE c.slug = 'domain-recon-basics';

-- Basic Binary Analysis (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Search for strcmp or memcmp calls in the decompiler — follow the branch on success.', 10
FROM `challenges` c WHERE c.slug = 'basic-binary-analysis';

-- Simple Crackme (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Set a breakpoint on the comparison instruction and inspect register values.', 10
FROM `challenges` c WHERE c.slug = 'simple-crackme';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'The serial validation XORs each character — try reversing the algorithm.', 20
FROM `challenges` c WHERE c.slug = 'simple-crackme';

-- Suspicious Process (1 hint)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Use Volatility pslist and malfind to locate injected code regions.', 10
FROM `challenges` c WHERE c.slug = 'suspicious-process';

-- Static Analysis Basics (2 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Check the .rsrc section for embedded PE resources with Resource Hacker or peview.', 10
FROM `challenges` c WHERE c.slug = 'static-analysis-basics';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Suspicious imports: VirtualAlloc + WriteProcessMemory often indicate shellcode staging.', 20
FROM `challenges` c WHERE c.slug = 'static-analysis-basics';

-- Persistence Investigation (3 hints)
INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 1, 'Export and review Run/RunOnce registry keys from HKCU and HKLM.', 10
FROM `challenges` c WHERE c.slug = 'persistence-investigation';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 2, 'Check Scheduled Tasks for hidden tasks with suspicious actions.', 20
FROM `challenges` c WHERE c.slug = 'persistence-investigation';

INSERT INTO `challenge_hints` (`challenge_id`, `hint_order`, `content`, `point_penalty`)
SELECT c.id, 3, 'The persistence entry uses a Base64-encoded PowerShell command — decode it.', 30
FROM `challenges` c WHERE c.slug = 'persistence-investigation';

-- ---------------------------------------------------------------------------
-- Challenge tags
-- ---------------------------------------------------------------------------
INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'sql-injection-basics' AND t.slug IN ('sql', 'sql-injection', 'web-security');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'php-session-weakness' AND t.slug IN ('php', 'authentication', 'web-security');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'cross-site-scripting-fundamentals' AND t.slug IN ('xss', 'web-security');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'broken-access-control' AND t.slug IN ('authentication', 'web-security');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'nmap-fundamentals' AND t.slug IN ('nmap', 'networking');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'tcp-packet-investigation' AND t.slug IN ('tcp', 'networking', 'wireshark');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'wireshark-basics' AND t.slug IN ('wireshark', 'networking');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'find-the-hidden-file' AND t.slug IN ('forensics');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'basic-file-metadata' AND t.slug IN ('forensics', 'osint');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'pcap-investigation' AND t.slug IN ('pcap', 'forensics', 'networking');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'linux-permissions' AND t.slug IN ('linux');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'process-investigation' AND t.slug IN ('linux', 'forensics');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'find-the-suspicious-process' AND t.slug IN ('linux', 'forensics');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'caesar-cipher' AND t.slug IN ('crypto', 'encryption');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'base64-investigation' AND t.slug IN ('crypto');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'weak-password-hash' AND t.slug IN ('crypto', 'hashing');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'username-investigation' AND t.slug IN ('osint');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'metadata-hunt' AND t.slug IN ('osint', 'forensics');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'domain-recon-basics' AND t.slug IN ('osint', 'networking');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'string-hunting' AND t.slug IN ('reverse-engineering');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'basic-binary-analysis' AND t.slug IN ('reverse-engineering');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'simple-crackme' AND t.slug IN ('reverse-engineering');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'suspicious-process' AND t.slug IN ('malware', 'forensics');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'static-analysis-basics' AND t.slug IN ('malware', 'reverse-engineering');

INSERT INTO `challenge_tags` (`challenge_id`, `tag_id`)
SELECT c.id, t.id FROM `challenges` c, `tags` t
WHERE c.slug = 'persistence-investigation' AND t.slug IN ('malware', 'powershell', 'forensics');

-- ---------------------------------------------------------------------------
-- Arena events
-- ---------------------------------------------------------------------------
INSERT INTO `arena_events` (`name`, `slug`, `description`, `event_type`, `status`, `visibility`, `start_at`, `end_at`, `created_by`)
SELECT
  'KKU Cyber Arena Practice',
  'kku-cyber-arena-practice',
  'Open practice arena for College of Computing students.\n\nWork through beginner-friendly challenges across web, network, crypto, and forensics. No time pressure — learn at your own pace.',
  'practice', 'active', 'public',
  DATE_SUB(NOW(), INTERVAL 7 DAY),
  DATE_ADD(NOW(), INTERVAL 60 DAY),
  u.id
FROM `users` u WHERE u.username = 'instructor1';

INSERT INTO `arena_events` (`name`, `slug`, `description`, `event_type`, `status`, `visibility`, `start_at`, `end_at`, `created_by`)
SELECT
  'Introduction to Web Security CTF',
  'introduction-to-web-security-ctf',
  'A focused CTF event covering OWASP fundamentals.\n\nFour web challenges — SQL injection, session security, XSS, and access control. Perfect for students completing the web security module.',
  'ctf', 'upcoming', 'public',
  DATE_ADD(NOW(), INTERVAL 14 DAY),
  DATE_ADD(NOW(), INTERVAL 21 DAY),
  u.id
FROM `users` u WHERE u.username = 'instructor1';

-- KKU Cyber Arena Practice — 8 challenges
INSERT INTO `arena_event_challenges` (`event_id`, `challenge_id`)
SELECT e.id, c.id
FROM `arena_events` e, `challenges` c
WHERE e.slug = 'kku-cyber-arena-practice'
  AND c.slug IN (
    'sql-injection-basics',
    'nmap-fundamentals',
    'caesar-cipher',
    'linux-permissions',
    'string-hunting',
    'find-the-hidden-file',
    'username-investigation',
    'wireshark-basics'
  );

-- Introduction to Web Security CTF — 4 web challenges
INSERT INTO `arena_event_challenges` (`event_id`, `challenge_id`)
SELECT e.id, c.id
FROM `arena_events` e, `challenges` c
WHERE e.slug = 'introduction-to-web-security-ctf'
  AND c.slug IN (
    'sql-injection-basics',
    'php-session-weakness',
    'cross-site-scripting-fundamentals',
    'broken-access-control'
  );
