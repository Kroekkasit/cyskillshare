-- CySkillShare Phase 6: Knowledge Base + Writeups seed data
-- DEVELOPMENT ONLY — do not use in production.
-- Requires: seed.sql, seed_arena.sql, seed_skills.sql, seed_portfolio.sql, knowledge_schema.sql
-- Generated with safe SQL string escaping (no nested quote breakage).

SET NAMES utf8mb4;
USE `cyskillshare`;

-- ---------------------------------------------------------------------------
-- Writeup categories
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `writeup_categories` (`slug`, `name`, `display_order`) VALUES
  ('web-security',         'Web Security',         10),
  ('network-security',     'Network Security',     20),
  ('digital-forensics',    'Digital Forensics',    30),
  ('malware-analysis',     'Malware Analysis',     40),
  ('reverse-engineering',  'Reverse Engineering',  50),
  ('osint',                'OSINT',                60),
  ('cryptography',         'Cryptography',         70),
  ('linux',                'Linux',                80),
  ('windows',              'Windows',              90),
  ('cloud-security',       'Cloud Security',       100),
  ('secure-coding',        'Secure Coding',        110),
  ('incident-response',    'Incident Response',    120),
  ('ctf-writeup',          'CTF Writeup',          130),
  ('tutorial',             'Tutorial',             140),
  ('case-study',           'Case Study',           150);

-- ---------------------------------------------------------------------------
-- Writeup tags
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `writeup_tags` (`slug`, `name`) VALUES
  ('sql-injection', 'SQL Injection'),
  ('xss',           'XSS'),
  ('burp-suite',    'Burp Suite'),
  ('nmap',          'Nmap'),
  ('wireshark',     'Wireshark'),
  ('volatility',    'Volatility'),
  ('ghidra',        'Ghidra'),
  ('linux',         'Linux'),
  ('jwt',           'JWT'),
  ('php',           'PHP'),
  ('apache',        'Apache'),
  ('docker',        'Docker'),
  ('csrf',          'CSRF'),
  ('networking',    'Networking'),
  ('forensics',     'Forensics'),
  ('windows',       'Windows');


INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `featured`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'SQL Injection Beyond the Basics',
  'sql-injection-beyond-the-basics',
  'Moving past login bypass: second-order injection, blind techniques, and why prepared statements remain the fix.',
  '## Overview

After completing the **SQL Injection Basics** challenge, I wanted to document the techniques that appear once error messages disappear and queries get more complex.

## Second-order injection

User input stored safely on insert can still be dangerous when read back into a dynamic query:

```php
// Registration stores bound input
$stmt = $pdo->prepare("INSERT INTO users (username) VALUES (?)");
$stmt->execute([$username]);

// Later, a report builder concatenates without binding
$query = "SELECT * FROM logs WHERE user = ''" . $username . "''";
```

The payload may sit dormant until a different code path executes it.

## Blind boolean-based probing

When the application returns identical pages for true and false conditions, infer answers one bit at a time:

```sql
admin'' AND SUBSTRING((SELECT password FROM users LIMIT 1),1,1)=''a''-- -
```

Compare response length, timing, or subtle markup differences.

## Mitigation checklist

- Parameterized queries everywhere — including ORDER BY and dynamic identifiers where possible
- Least-privilege DB accounts
- Avoid displaying raw SQL errors in production

## Lessons learned

Never assume escaped storage equals safe reuse. Trace every place a value re-enters SQL.

## Related community thread

This writeup complements the discussion on prepared statements in PHP.',
  'markdown', 'intermediate', 'published', 'public', 1, NOW(), 8
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'student1';


INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'Memory Analysis with Volatility',
  'memory-analysis-with-volatility',
  'Triaging a Windows memory dump with Volatility 3 plugins for process, network, and malware artifacts.',
  '## Lab setup

Acquire a memory image from an isolated analysis VM. Verify integrity before processing:

```bash
sha256sum suspect-host.raw > suspect-host.raw.sha256
vol -f suspect-host.raw windows.info
```

## Process enumeration

Identify unexpected processes and parent-child relationships:

```bash
vol -f suspect-host.raw windows.pslist
vol -f suspect-host.raw windows.pstree
vol -f suspect-host.raw windows.cmdline
```

## Network connections

Map suspicious binaries to outbound connections:

```bash
vol -f suspect-host.raw windows.netscan
```

## Lessons learned

Memory analysis captures ephemeral artifacts — injected code, decrypted strings, and active C2 sessions — that disk forensics may miss. Document your plugin order and timestamps for reproducibility.',
  'markdown', 'intermediate', 'published', 'public', NOW(), 6
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'digital-forensics'
WHERE u.username = 'student1';


INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'Analyzing a Suspicious PE File',
  'analyzing-a-suspicious-pe-file',
  'Static triage of a Windows PE sample: headers, imports, strings, and packing indicators.',
  '## Sample information

Hash the sample before analysis. Work only inside an isolated lab VM.

```bash
file sample.bin
sha256sum sample.bin
```

## Static analysis

Inspect PE headers, sections, and imports without executing the binary:

```bash
objdump -x sample.bin | head
strings -n 8 sample.bin | head -n 50
```

## Behavioral analysis (sandbox notes)

Observe process creation, file drops, and registry persistence. Record IOCs for later hunting.

## Lessons learned

Static analysis narrows hypotheses; dynamic analysis confirms behavior. Never run unknown samples on a production host.

## References

- Microsoft PE format documentation
- MITRE ATT&CK persistence techniques',
  'markdown', 'advanced', 'published', 'public', NOW(), 10
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'malware-analysis'
WHERE u.username = 'student1';


INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'Understanding ARP Spoofing',
  'understanding-arp-spoofing',
  'How ARP poisoning works on a local network and how defenders detect abnormal MAC-IP bindings.',
  '## Background

ARP maps IP addresses to MAC addresses on a LAN. It has no built-in authentication.

## Attack overview

An attacker claims to own the gateway IP, poisoning neighbor caches so traffic flows through the attacker host.

## Detection

Watch for duplicate IP-to-MAC bindings and sudden gateway MAC changes:

```bash
arp -an
# Compare against a known-good baseline
```

## Mitigation

- Dynamic ARP inspection on managed switches
- Static ARP for critical hosts where practical
- Network segmentation

## Lessons learned

Layer-2 trust assumptions break in shared networks. Monitoring ARP anomalies is a practical first step.',
  'markdown', 'beginner', 'published', 'public', NOW(), 5
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'network-security'
WHERE u.username = 'student2';


INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'Draft: JWT Pitfalls Lab Notes',
  'draft-jwt-pitfalls-lab-notes',
  'Work-in-progress notes on alg=none and weak HMAC secrets.',
  '## WIP

Notes from the JWT lab. Not ready for publication.

```http
POST /login HTTP/1.1
Host: example.local
Content-Type: application/json

{"username":"student","password":"..."}
```

Still need to add verification steps and screenshots.',
  'markdown', 'intermediate', 'draft', 'private', 3
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'student1';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'sql-injection'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'php'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'burp-suite'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'volatility'
WHERE w.slug = 'memory-analysis-with-volatility';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'forensics'
WHERE w.slug = 'memory-analysis-with-volatility';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'windows'
WHERE w.slug = 'memory-analysis-with-volatility';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'ghidra'
WHERE w.slug = 'analyzing-a-suspicious-pe-file';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'nmap'
WHERE w.slug = 'understanding-arp-spoofing';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'networking'
WHERE w.slug = 'understanding-arp-spoofing';


INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'wireshark'
WHERE w.slug = 'understanding-arp-spoofing';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'sql-injection'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'web-security'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'secure-coding'
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE w.slug = 'memory-analysis-with-volatility';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'malware-analysis'
WHERE w.slug = 'memory-analysis-with-volatility';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'malware-analysis'
WHERE w.slug = 'analyzing-a-suspicious-pe-file';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'reverse-engineering'
WHERE w.slug = 'analyzing-a-suspicious-pe-file';


INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`, `weight`)
SELECT w.id, s.id, 1.00 FROM `writeups` w
JOIN `skills` s ON s.slug = 'network-security'
WHERE w.slug = 'understanding-arp-spoofing';


INSERT IGNORE INTO `writeup_challenges` (`writeup_id`, `challenge_id`)
SELECT w.id, c.id
FROM `writeups` w
CROSS JOIN (
  SELECT id FROM `challenges`
  WHERE slug = 'sql-injection-basics' OR title LIKE '%SQL Injection%'
  ORDER BY id ASC LIMIT 1
) c
WHERE w.slug = 'sql-injection-beyond-the-basics';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'What is SQL Injection?',
  'what-is-sql-injection',
  'A clear introduction to SQL injection, impact, and prevention with prepared statements.',
  '## Definition

SQL injection occurs when untrusted input is concatenated into a SQL statement, allowing attackers to alter query logic.

## Classic example

```php
$query = "SELECT * FROM users WHERE username = ''" . $_POST["user"] . "'' AND password = ''" . $_POST["pass"] . "''";
$result = mysqli_query($conn, $query);
```

An attacker may supply:

```sql
admin''-- -
```

## Impact

- Authentication bypass
- Data exfiltration
- Data modification or deletion

## Prevention

Use **prepared statements** with bound parameters. Validate input as a secondary layer, not the primary defense.

See also: student writeup *SQL Injection Beyond the Basics* and OWASP guidance.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'what-is-sql-injection';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'Understanding CSRF Tokens',
  'understanding-csrf-tokens',
  'How cross-site request forgery works and how anti-CSRF tokens protect state-changing requests.',
  '## The problem

Browsers automatically attach session cookies. A malicious site can trick a logged-in user into submitting a form to your application.

## Token pattern

1. Server generates a random token stored in the session
2. Token is embedded in forms or headers
3. Server rejects state-changing requests without a valid token

```php
$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
// In form: hidden input named csrf_token
```

## Verification

Compare the submitted token to the session value using a timing-safe comparison (`hash_equals` in PHP).

## Complementary controls

SameSite cookies and verifying the Origin/Referer headers add defense in depth.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'understanding-csrf-tokens';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'Linux File Permissions',
  'linux-file-permissions',
  'Read, write, execute bits, ownership, and common privilege pitfalls.',
  '## Basics

Linux permissions use owner, group, and other classes with read (r), write (w), and execute (x) bits.

```bash
ls -l /etc/passwd
chmod 640 secret.conf
chown root:admins secret.conf
```

## Special bits

Setuid, setgid, and sticky bit change execution and deletion semantics. Misconfigured setuid binaries are a common privilege-escalation path.

## Practical tip

Prefer least privilege. Avoid world-writable directories on multi-user systems.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'linux'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'linux-file-permissions';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'TCP Three-Way Handshake',
  'tcp-three-way-handshake',
  'SYN, SYN-ACK, ACK and why handshake anomalies matter during incident triage.',
  '## Steps

1. Client → Server: **SYN** (seq = x)
2. Server → Client: **SYN-ACK** (seq = y, ack = x+1)
3. Client → Server: **ACK** (ack = y+1)

## Wireshark filter

```bash
tshark -r capture.pcap -Y "tcp.flags.syn==1 || tcp.flags.ack==1"
```

## Why it matters for security

SYN floods abuse step 1. Half-open connections and unexpected RST packets may indicate scanning or firewall interference.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'network-security'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'tcp-three-way-handshake';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'Introduction to Digital Forensics',
  'introduction-to-digital-forensics',
  'Core forensic process: identification, preservation, analysis, and reporting.',
  '## Process overview

1. Identify relevant evidence sources
2. Preserve integrity (write blockers, hashing)
3. Analyze with documented methods
4. Report findings clearly

## Chain of custody

Record who handled evidence, when, and why. Hash values before and after acquisition.

## Lessons for students

Reproducibility matters as much as clever analysis. Always document tools and versions.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'digital-forensics'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'introduction-to-digital-forensics';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'Understanding PE Files',
  'understanding-pe-files',
  'Structure of Windows Portable Executable files and what analysts look for first.',
  '## PE structure

DOS header, PE signature, COFF header, optional header, section table, and sections (`.text`, `.data`, `.rdata`, …).

## First-pass checks

- Unusual section names or entropy (packing)
- Suspicious imports (VirtualAlloc, WriteProcessMemory, URLDownloadToFile)
- Resource anomalies

## Safety

Treat every sample as hostile. Analyze offline. Do not host live malware on the learning platform.',
  'intermediate', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'malware-analysis'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'understanding-pe-files';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'What is XSS?',
  'what-is-xss',
  'Cross-site scripting types, impact, and encoding defenses.',
  '## Definition

XSS lets an attacker run script in another user’s browser within the origin of a vulnerable site.

## Types

- Reflected
- Stored
- DOM-based

## Defense

Context-aware output encoding, CSP, and avoiding unsafe sinks such as `innerHTML` with untrusted data.',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'instructor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'what-is-xss';


INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'Introduction to Network Reconnaissance',
  'introduction-to-network-reconnaissance',
  'Ethical recon concepts: passive vs active techniques and documentation habits.',
  '## Passive vs active

Passive recon uses public sources. Active recon probes targets directly and must stay within authorization scope.

## Common tools (authorized labs only)

```bash
nmap -sV -T3 target.lab
```

## Documentation

Record scope, timestamps, and commands. Recon notes often become writeups and evidence.',
  'beginner', 'published', 'public', 0, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'network-security'
WHERE u.username = 'mentor1';


INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'Initial published version'
FROM `knowledge_articles` a WHERE a.slug = 'introduction-to-network-reconnaissance';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'sql-injection'
WHERE a.slug = 'what-is-sql-injection';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'web-security'
WHERE a.slug = 'what-is-sql-injection';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'web-security'
WHERE a.slug = 'understanding-csrf-tokens';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'linux'
WHERE a.slug = 'linux-file-permissions';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'network-security'
WHERE a.slug = 'tcp-three-way-handshake';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'digital-forensics'
WHERE a.slug = 'introduction-to-digital-forensics';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'malware-analysis'
WHERE a.slug = 'understanding-pe-files';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'web-security'
WHERE a.slug = 'what-is-xss';


INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`, `weight`)
SELECT a.id, s.id, 1.00 FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'network-security'
WHERE a.slug = 'introduction-to-network-reconnaissance';


INSERT IGNORE INTO `knowledge_article_sources` (`article_id`, `source_type`, `source_id`, `description`)
SELECT a.id, 'external_reference', NULL, 'OWASP SQL Injection'
FROM `knowledge_articles` a WHERE a.slug = 'what-is-sql-injection';

INSERT IGNORE INTO `knowledge_article_sources` (`article_id`, `source_type`, `source_id`, `description`)
SELECT a.id, 'writeup', w.id, 'Student writeup: SQL Injection Beyond the Basics'
FROM `knowledge_articles` a
JOIN `writeups` w ON w.slug = 'sql-injection-beyond-the-basics'
WHERE a.slug = 'what-is-sql-injection';

-- Featured writeups on student1 portfolio (if table exists)
INSERT IGNORE INTO `portfolio_featured_writeups` (`user_id`, `writeup_id`, `display_order`)
SELECT u.id, w.id, 1
FROM `users` u
JOIN `writeups` w ON w.slug = 'sql-injection-beyond-the-basics' AND w.user_id = u.id
WHERE u.username = 'student1';

INSERT IGNORE INTO `portfolio_featured_writeups` (`user_id`, `writeup_id`, `display_order`)
SELECT u.id, w.id, 2
FROM `users` u
JOIN `writeups` w ON w.slug = 'memory-analysis-with-volatility' AND w.user_id = u.id
WHERE u.username = 'student1';

