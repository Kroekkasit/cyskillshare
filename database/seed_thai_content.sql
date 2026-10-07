-- CySkillShare — Thai student / community content (simulates KKU CoC usage)
-- Safe to re-run: uses UPDATE by title/slug + INSERT IGNORE for new rows.
SET NAMES utf8mb4;
USE `cyskillshare`;

-- ---------------------------------------------------------------------------
-- Student profile bios (Thai)
-- ---------------------------------------------------------------------------
UPDATE `users` SET
  `full_name` = 'สมชาย ใจดี',
  `bio` = 'นักศึกษาวิทยาการคอมพิวเตอร์ มข. สนใจ Web Security และ CTF ชอบแชร์โน้ตหลังแล็บ'
WHERE `username` = 'student1';

UPDATE `users` SET
  `full_name` = 'พิมพ์ใจ รักเรียน',
  `bio` = 'ปี 3 สาขาวิทยาการคอมพิวเตอร์ กำลังเรียนวิชา Database and Web Security ค่ะ'
WHERE `username` = 'student2';

UPDATE `users` SET
  `full_name` = 'ธนกร เครือข่าย',
  `bio` = 'ชอบ Network Forensics กับ Wireshark กำลังฝึกทำ writeup เป็นภาษาไทย'
WHERE `username` = 'student3';

UPDATE `users` SET
  `full_name` = 'พี่เมนเทอร์ อานนท์',
  `bio` = 'Peer mentor ช่วยน้องเรื่อง Linux, Web Security และเตรียมสอบ CTF'
WHERE `username` = 'mentor1';

-- ---------------------------------------------------------------------------
-- Existing community threads → Thai (student-like wording)
-- ---------------------------------------------------------------------------
UPDATE `threads` SET
  `title` = 'CSRF ทำงานยังไงอะ? งงมาก',
  `content` = 'ตอนนี้เรียนเรื่อง CSRF อยู่ครับ งงว่าทำไมเว็บปลอมถึงสั่ง request ไปยังเว็บที่เราล็อกอินอยู่ได้\n\n1. เบราว์เซอร์ส่ง cookie ไปให้ทุกครั้งเลยไหม?\n2. SameSite ช่วยได้แค่ไหน?\n3. ตอนไหนยังต้องใช้ CSRF token?\n\nขอตัวอย่าง PHP แบบเข้าใจง่ายหน่อยได้ไหมครับ ขอบคุณครับ'
WHERE `title` = 'How does CSRF actually work?';

UPDATE `threads` SET
  `title` = 'Prepared Statements ใน PHP ใช้ยังไงให้ปลอดภัย?',
  `content` = 'กำลังทำโปรเจกต์ PHP ใช้ PDO ครับ\n\nใช้ prepared statements อย่างเดียวพอป้องกัน SQL Injection ได้ไหม หรือต้อง validate input ด้วย?\n\n```php\n$stmt = $pdo->prepare(\"SELECT * FROM users WHERE username = ?\");\n$stmt->execute([$username]);\n```\n\nพี่ๆ มี best practice แนะนำไหมครับ?'
WHERE `title` = 'Understanding prepared statements in PHP';

UPDATE `threads` SET
  `title` = 'อ่าน TCP Stream ใน Wireshark ยังไงให้เร็ว?',
  `content` = 'ในแล็บมี packet เยอะมาก อยากรู้วิธี Follow TCP Stream แล้วดึง request/response ออกมาทำรายงานเร็วที่สุดครับ'
WHERE `title` = 'How to read a Wireshark TCP stream?';

UPDATE `threads` SET
  `title` = 'จะเริ่มเล่น CTF ครั้งแรกยังไงดี?',
  `content` = 'อยากลอง CTF เทอมนี้ครับ มือใหม่ควรเริ่มหมวดไหนก่อน? แล้วบน Linux ควรลงเครื่องมืออะไรบ้าง?'
WHERE `title` = 'How should I approach my first CTF?';

UPDATE `threads` SET
  `title` = 'chmod 600 กับ 644 ต่างกันยังไง? (ไฟล์ลับ)',
  `content` = 'พี่ๆ ช่วยอธิบายหน่อยได้ไหมครับ เวลาเก็บ secret ใน config ควรใช้ chmod 600 หรือ 644? อยากเขียนในรายงานแล็บให้ถูกต้อง'
WHERE `title` = 'Basic Linux permissions for cybersecurity';

UPDATE `threads` SET
  `title` = 'แฮชกับเข้ารหัสต่างกันยังไงครับ?',
  `content` = 'เรียน Cryptography แล้วยังสับสนระหว่าง hashing กับ encryption อยู่ ขอตัวอย่างในสายไซเบอร์หน่อยได้ไหมครับ เช่นเก็บรหัสผ่าน กับ TLS'
WHERE `title` = 'What is the difference between hashing and encryption?';

-- Thai replies for those threads
UPDATE `replies` r
INNER JOIN `threads` t ON t.id = r.thread_id
INNER JOIN `users` u ON u.id = r.user_id
SET r.content = 'ใช่ครับ — เบราว์เซอร์จะส่ง cookie ตอน POST ข้ามเว็บได้ในหลายเคส\n\n**SameSite=Lax/Strict** ช่วยลด CSRF ได้เยอะ แต่ API กับเบราว์เซอร์เก่ายังควรมี token\n\nแพทเทิร์นที่ใช้บ่อยใน PHP คือเก็บ CSRF token ใน session แล้วตรวจทุก request ที่เปลี่ยนข้อมูล'
WHERE t.title = 'CSRF ทำงานยังไงอะ? งงมาก' AND u.username = 'mentor1';

UPDATE `replies` r
INNER JOIN `threads` t ON t.id = r.thread_id
INNER JOIN `users` u ON u.id = r.user_id
SET r.content = 'Prepared statements คือตัวหลักที่กัน SQL Injection ในโครงสร้างคิวรี\n\nยังควร validate input ตามกฎธุรกิจ (ความยาว, ชนิด, allowlist) แต่ validation **แทน** parameterization ไม่ได้\n\nอย่าเอา input ผู้ใช้ไปต่อสตริง SQL โดยตรง'
WHERE t.title = 'Prepared Statements ใน PHP ใช้ยังไงให้ปลอดภัย?' AND u.username = 'instructor1';

UPDATE `replies` r
INNER JOIN `threads` t ON t.id = r.thread_id
INNER JOIN `users` u ON u.id = r.user_id
SET r.content = 'คลิกขวาที่แพ็กเก็ต → Follow → TCP Stream แล้วสลับฝั่ง client/server ได้ Export เป็น raw/ASCII ไปใส่ใน writeup ได้เลยครับ'
WHERE t.title = 'อ่าน TCP Stream ใน Wireshark ยังไงให้เร็ว?' AND u.username = 'mentor1';

UPDATE `replies` r
INNER JOIN `threads` t ON t.id = r.thread_id
INNER JOIN `users` u ON u.id = r.user_id
SET r.content = '**Hashing** เป็นทางเดียว (เก็บรหัสผ่านด้วย salt/argon2) ส่วน **Encryption** ถอดกลับด้วยกุญแจได้ (TLS, disk encryption)\n\nถ้าต้องเก็บความลับแล้วดึงค่าเดิมทีหลัง → เข้ารหัส ถ้าแค่ตรวจภายหลัง → แฮช'
WHERE t.title = 'แฮชกับเข้ารหัสต่างกันยังไงครับ?' AND u.username = 'mentor1';

-- ---------------------------------------------------------------------------
-- Extra Thai threads (new)
-- ---------------------------------------------------------------------------
INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'ส่งงานแล็บ Web Security แล้วได้ feedback ยังไงบ้าง?',
  'เพื่อนๆ ที่ส่งแล็บ Vulnerable Web Application ไปแล้ว อาจารย์คอมเมนต์ประเด็นไหนเยอะสุดครับ? อยากเตรียม remediation ให้ครบ',
  'open'
FROM channels ch, users u
WHERE ch.slug = 'assignment-help' AND u.username = 'student1'
AND NOT EXISTS (SELECT 1 FROM threads WHERE title = 'ส่งงานแล็บ Web Security แล้วได้ feedback ยังไงบ้าง?')
LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'ขอแนะนำช่องทางฝึก SQLi แบบปลอดภัยหน่อย',
  'อยากฝึก SQL Injection แต่กลัวไปยิงระบบจริง มีแล็บใน CySkillShare หรือ DVWA แนะนำไหมคะ เริ่มจากง่ายไปยากยังไงดี',
  'open'
FROM channels ch, users u
WHERE ch.slug = 'web-security' AND u.username = 'student2'
AND NOT EXISTS (SELECT 1 FROM threads WHERE title = 'ขอแนะนำช่องทางฝึก SQLi แบบปลอดภัยหน่อย')
LIMIT 1;

INSERT INTO `threads` (`channel_id`, `user_id`, `title`, `content`, `status`)
SELECT ch.id, u.id,
  'Linux lab: เจอ process แปลกชื่อ kworker-xxxx',
  'ตอนทำ Linux Security Investigation เห็น process ชื่อคล้าย kworker แต่ดูแปลกๆ ต้องไล่ยังไงต่อดีครับ? ใช้ ps กับ /proc พอไหม',
  'open'
FROM channels ch, users u
WHERE ch.slug = 'ctf-general' AND u.username = 'student3'
AND NOT EXISTS (SELECT 1 FROM threads WHERE title = 'Linux lab: เจอ process แปลกชื่อ kworker-xxxx')
LIMIT 1;

INSERT INTO `replies` (`thread_id`, `user_id`, `content`)
SELECT t.id, u.id,
  'เริ่มจากแล็บในระบบก่อนเลยครับ ปลอดภัยและมี task ให้ทำทีละขั้น อย่าเอา payload ไปลองกับเว็บจริงเด็ดขาด จากนั้นค่อยดู writeup ภาษาไทย/อังกฤษใน Knowledge Base'
FROM threads t, users u
WHERE t.title = 'ขอแนะนำช่องทางฝึก SQLi แบบปลอดภัยหน่อย' AND u.username = 'mentor1'
AND NOT EXISTS (
  SELECT 1 FROM replies r WHERE r.thread_id = t.id AND r.user_id = u.id
)
LIMIT 1;

INSERT INTO `replies` (`thread_id`, `user_id`, `content`)
SELECT t.id, u.id,
  'ดู parent PID และ cmdline ใน /proc/<pid>/ ครับ ถ้าชื่อคล้าย systemd/kworker แต่ path หรือ env แปลก ให้จดชื่อ process นั้นเป็นคำตอบ task แล้วค่อยไปหา flag ท้ายแล็บ'
FROM threads t, users u
WHERE t.title = 'Linux lab: เจอ process แปลกชื่อ kworker-xxxx' AND u.username = 'mentor1'
AND NOT EXISTS (
  SELECT 1 FROM replies r WHERE r.thread_id = t.id AND r.user_id = u.id
)
LIMIT 1;

INSERT IGNORE INTO `thread_tags` (`thread_id`, `tag_id`)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'CSRF ทำงานยังไงอะ? งงมาก' AND tg.slug IN ('csrf', 'web-security', 'php');

INSERT IGNORE INTO `thread_tags` (`thread_id`, `tag_id`)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'Prepared Statements ใน PHP ใช้ยังไงให้ปลอดภัย?' AND tg.slug IN ('php', 'sql-injection', 'web-security');

INSERT IGNORE INTO `thread_tags` (`thread_id`, `tag_id`)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'ขอแนะนำช่องทางฝึก SQLi แบบปลอดภัยหน่อย' AND tg.slug IN ('sql-injection', 'web-security');

INSERT IGNORE INTO `thread_tags` (`thread_id`, `tag_id`)
SELECT t.id, tg.id FROM threads t, tags tg
WHERE t.title = 'Linux lab: เจอ process แปลกชื่อ kworker-xxxx' AND tg.slug IN ('linux', 'ctf');

-- ---------------------------------------------------------------------------
-- Labs — Thai titles / descriptions (keep slugs for code)
-- ---------------------------------------------------------------------------
UPDATE `labs` SET
  `title` = 'เว็บแอปพลิเคชันที่มีช่องโหว่ (Vulnerable Web App)',
  `short_description` = 'สำรวจเว็บแล็บจำลอง: หา SQLi วิเคราะห์ล็อก และเสนอวิธีแก้แบบปลอดภัย',
  `description` = '## สถานการณ์\n\nคุณกำลังประเมินเว็บแอปชมรมในวิทยาเขต สภาพแวดล้อมนี้เป็น**จำลอง**ใน CySkillShare ไม่แตะระบบจริงของมหาวิทยาลัย\n\n## เป้าหมาย\n\nทำ reconnaissance หา endpoint ที่มีช่องโหว่ ยืนยันการโจมตีอย่างปลอดภัยในแล็บ วิเคราะห์ access log และเสนอ remediation\n\n## สภาพแวดล้อม\n\nเปิดแผง **Lab Environment** หลังเริ่มแล็บ จะมีค่าเฉพาะอินสแตนซ์ (IP, token) แสดงที่นั่น',
  `learning_objectives` = '- ระบุ HTTP request ที่น่าสงสัย\n- หา SQL injection อย่างปลอดภัยในแล็บ\n- วิเคราะห์ access log ของเว็บเซิร์ฟเวอร์\n- แนะนำ remediation ด้วย parameterized query'
WHERE `slug` = 'vulnerable-web-application';

UPDATE `labs` SET
  `title` = 'สืบสวนความปลอดภัยบน Linux',
  `short_description` = 'ตรวจสอบโฮสต์ Linux จำลอง: process, ล็อก auth, สิทธิ์ไฟล์ และ persistence',
  `description` = '## สถานการณ์\n\nจัมป์โฮสต์ Linux ในแล็บมีพฤติกรรมผิดปกติ ใช้คอนโซลจำลองเท่านั้น — ไม่มีสิทธิ์เข้าเซิร์ฟเวอร์จริงของมหาวิทยาลัย\n\n## สิ่งที่ต้องทำ\n\n- ไล่ process ที่ผิดปกติ\n- อ่าน auth log หาบัญชีที่ถูก brute-force\n- หา path ที่ world-writable\n- ส่ง completion flag',
  `learning_objectives` = '- อ่าน process list และ /proc\n- วิเคราะห์ failed SSH login\n- ประเมินสิทธิ์ไฟล์/โฟลเดอร์\n- สรุปหาหลักฐานเพื่อส่งคำตอบ'
WHERE `slug` = 'linux-security-investigation';

UPDATE `labs` SET
  `title` = 'ทราฟฟิกเครือข่ายที่น่าสงสัย',
  `short_description` = 'วิเคราะห์ทราฟฟิกจำลอง หา IOC และโดเมน C2 ในแล็บ Network',
  `description` = '## สถานการณ์\n\nทีม SOC ได้รับ PCAP/ล็อกจากเหตุการณ์เครือข่าย ภารกิจของคุณคือหาโฮสต์ที่น่าสงสัยและโดเมนที่เกี่ยวข้อง\n\nค่าในแล็บถูกสุ่มต่ออินสแตนซ์ — ดูแผง Lab Environment'
WHERE `slug` = 'suspicious-network-traffic';

UPDATE `lab_tasks` SET
  `title` = 'ระบุ IP เป้าหมาย',
  `description` = 'เปิด Lab Environment IP ภายในของเป้าหมายที่ได้รับคืออะไร?'
WHERE `slug` = 'identify-target-ip'
  AND `lab_id` = (SELECT id FROM labs WHERE slug = 'vulnerable-web-application' LIMIT 1);

UPDATE `lab_tasks` SET
  `title` = 'ชื่อ process ที่น่าสงสัย',
  `description` = 'ในรายการ process มีชื่อใดที่ดูเป็นมัลแวร์/ผิดปกติ?'
WHERE `slug` = 'bad-process'
  AND `lab_id` = (SELECT id FROM labs WHERE slug = 'linux-security-investigation' LIMIT 1);

UPDATE `lab_tasks` SET
  `title` = 'บัญชีที่ถูก brute-force',
  `description` = 'ชื่อผู้ใช้ใดปรากฏใน failed SSH บ่อยที่สุด?'
WHERE `slug` = 'auth-user'
  AND `lab_id` = (SELECT id FROM labs WHERE slug = 'linux-security-investigation' LIMIT 1);

UPDATE `lab_tasks` SET
  `title` = 'พาธที่ world-writable',
  `description` = 'พาธใดถูกตั้งสิทธิ์ world-writable อย่างไม่ถูกต้อง?'
WHERE `slug` = 'perm-path'
  AND `lab_id` = (SELECT id FROM labs WHERE slug = 'linux-security-investigation' LIMIT 1);

UPDATE `lab_tasks` SET
  `title` = 'แฟล็กจบแล็บ Linux',
  `description` = 'ส่ง completion flag ของอินสแตนซ์นี้'
WHERE `slug` = 'flag'
  AND `lab_id` = (SELECT id FROM labs WHERE slug = 'linux-security-investigation' LIMIT 1);

-- ---------------------------------------------------------------------------
-- Thai writeups
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'บันทึกแล็บ: SQL Injection ฉบับมือใหม่',
  'sql-injection-lab-notes-th',
  'สรุปภาษาไทยจากแล็บเว็บที่มีช่องโหว่ — วิธีหาจุด inject และกันด้วย PDO',
  '## ทำไมถึงสำคัญ\n\nSQL Injection ยังเจอบ่อยในโปรเจกต์นักศึกษา โดยเฉพาะตอนต่อสตริง SQL เอง\n\n## สิ่งที่ลองในแล็บ\n\n1. เปิดหน้า login แล้วลองใส่ `'' OR ''1''=''1` แบบควบคุมในแล็บเท่านั้น\n2. สังเกตว่าผลลัพธ์เปลี่ยนเมื่อเงื่อนไขเป็นจริง\n3. เปิด access log หา request ที่ผิดปกติ\n\n## วิธีแก้ที่ถูกต้อง\n\n```php\n$stmt = $pdo->prepare(''SELECT id FROM users WHERE username = ? AND password_hash = ?'');\n$stmt->execute([$username, $hash]);\n```\n\nอย่าแสดง SQL error ให้ผู้ใช้ทั่วไปเห็น\n\n## สรุปสั้นๆ\n\n- ใช้ prepared statements ทุกจุดที่รับค่าจากผู้ใช้\n- validate เป็นชั้นเสริม ไม่ใช่เกราะหลัก\n- บัญชีฐานข้อมูลควร least privilege\n\nเขียนไว้เป็นโน้ตหลังแล็บ เผื่อเพื่อนปีเดียวกันอ่านเข้าใจง่ายครับ',
  'markdown', 'beginner', 'published', 'public', NOW(), 5
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'student2';

INSERT IGNORE INTO `writeups` (
  `user_id`, `category_id`, `title`, `slug`, `short_description`, `content`,
  `content_format`, `difficulty`, `status`, `visibility`, `published_at`, `reading_time`
)
SELECT
  u.id,
  wc.id,
  'โน้ต Linux: สิทธิ์ไฟล์กับ SUID ที่ต้องระวัง',
  'linux-permissions-notes-th',
  'สรุป chmod/chown และทำไม SUID บนอินเทอร์พรีเตอร์ถึงอันตราย — ภาษาไทย',
  '## พื้นฐานที่ใช้อยู่ทุกวัน\n\n```bash\nls -l secret.conf\nchmod 600 secret.conf   # เจ้าของอ่าน/เขียนได้อย่างเดียว\nchmod 644 readme.md     # คนอื่นอ่านได้ เหมาะกับไฟล์สาธารณะ\n```\n\nไฟล์ที่มีรหัสผ่านหรือคีย์ ไม่ควรเป็น 644\n\n## SUID\n\nถ้าไบนารีมีบิต SUID ผู้ใช้ที่รันจะได้สิทธิ์เจ้าของไฟล์ชั่วคราว\n\n```bash\nfind / -perm -4000 2>/dev/null\n```\n\nในแล็บ Linux Permissions ให้หา SUID ที่ตั้งผิด แล้วอ่านแฟล็ก\n\n## สิ่งที่จำไว้\n\n- อย่าใส่ SUID ให้สคริปต์หรืออินเทอร์พรีเตอร์มั่ว\n- ใช้ least privilege\n- จดคำสั่งที่ใช้ในรายงานแล็บให้ครบ\n\nหวังว่าเพื่อนๆ จะเอาไปใช้ตอนทำ assignment ได้ครับ',
  'markdown', 'beginner', 'published', 'public', NOW(), 4
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'linux'
WHERE u.username = 'student3';

INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'sql-injection'
WHERE w.slug = 'sql-injection-lab-notes-th';

INSERT IGNORE INTO `writeup_tag_map` (`writeup_id`, `tag_id`)
SELECT w.id, t.id FROM `writeups` w
JOIN `writeup_tags` t ON t.slug = 'php'
WHERE w.slug = 'sql-injection-lab-notes-th';

INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`)
SELECT w.id, s.id FROM `writeups` w
JOIN `skills` s ON s.slug = 'sql-injection'
WHERE w.slug = 'sql-injection-lab-notes-th';

INSERT IGNORE INTO `writeup_skills` (`writeup_id`, `skill_id`)
SELECT w.id, s.id FROM `writeups` w
JOIN `skills` s ON s.slug = 'linux'
WHERE w.slug = 'linux-permissions-notes-th';

-- ---------------------------------------------------------------------------
-- Thai knowledge articles
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'SQL Injection คืออะไร? (ฉบับภาษาไทย)',
  'what-is-sql-injection-th',
  'แนะนำ SQL Injection แบบสั้น เข้าใจง่าย สำหรับนักศึกษาปีต้น',
  '## ความหมาย\n\nSQL Injection เกิดเมื่อนำค่าจากผู้ใช้ไปต่อเข้า SQL โดยตรง ทำให้ผู้โจมตีเปลี่ยนตรรกะของคิวรีได้\n\n## ตัวอย่างที่ไม่ปลอดภัย\n\n```php\n$query = "SELECT * FROM users WHERE username = ''" . $_POST["user"] . "''";\n```\n\n## ผลกระทบ\n\n- ข้ามการล็อกอิน\n- ขโมยข้อมูล\n- แก้หรือลบข้อมูล\n\n## วิธีป้องกัน\n\nใช้ **Prepared Statements** (เช่น PDO `prepare` + `bindValue`/`execute`) และอย่าโชว์ SQL error ให้ผู้ใช้ทั่วไป\n\nอ่านเพิ่ม: writeup *บันทึกแล็บ: SQL Injection ฉบับมือใหม่*',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'instructor1';

INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'เวอร์ชันภาษาไทยเริ่มต้น'
FROM `knowledge_articles` a WHERE a.slug = 'what-is-sql-injection-th';

INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'CSRF Token ทำงานอย่างไร?',
  'csrf-token-th',
  'อธิบาย CSRF และการใช้ Synchronizer Token แบบที่ใช่ใน CySkillShare',
  '## ปัญหา\n\nเบราว์เซอร์ส่ง session cookie ให้อัตโนมัติ เว็บไม่ดีอาจหลอกให้ผู้ใช้ที่ล็อกอินอยู่ส่งฟอร์มไปยังเว็บเรา\n\n## วิธีแก้ในระบบนี้\n\n1. สร้างโทเคนสุ่มด้วย `random_bytes(32)` เก็บใน session (`_csrf_token`)\n2. ฝังในฟอร์มเป็น `_csrf` ผ่าน `csrf_field()`\n3. ตรวจด้วย `hash_equals()` ถ้าไม่ตรง → HTTP 419\n\n## ข้อควรจำ\n\n- ใช้คู่กับ SameSite cookie\n- Logout แล้วควรหมุนโทเคนใหม่\n- อย่าพึ่ง Origin header อย่างเดียว',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'web-security'
WHERE u.username = 'instructor1';

INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'เวอร์ชันภาษาไทยเริ่มต้น'
FROM `knowledge_articles` a WHERE a.slug = 'csrf-token-th';

INSERT IGNORE INTO `knowledge_articles` (
  `author_id`, `category_id`, `title`, `slug`, `summary`, `content`,
  `difficulty`, `status`, `visibility`, `is_official`, `version`, `published_at`
)
SELECT
  u.id,
  wc.id,
  'สิทธิ์ไฟล์บน Linux เบื้องต้น',
  'linux-file-permissions-th',
  'อ่าน/เขียน/รัน, เจ้าของกลุ่ม และการตั้งค่าที่พลาดบ่อย',
  '## พื้นฐาน\n\n```bash\nls -l\nchmod 640 secret.conf\nchown student:student secret.conf\n```\n\n- **600** — เหมาะกับไฟล์ลับของเจ้าของ\n- **644** — ไฟล์ที่คนอื่นอ่านได้\n- **755** — โฟลเดอร์/สคริปต์ที่ต้องรันได้\n\n## ข้อควรระวัง\n\nโฟลเดอร์ world-writable บนเครื่องใช้ร่วมกันเสี่ยงมาก ในแล็บ Linux ให้หา path ที่ตั้งผิดแล้วรายงาน\n\n## ทิปสั้นๆ\n\nใช้สิทธิ์น้อยที่สุดที่ยังทำงานได้ (least privilege)',
  'beginner', 'published', 'public', 1, 1, NOW()
FROM `users` u
JOIN `writeup_categories` wc ON wc.slug = 'linux'
WHERE u.username = 'instructor1';

INSERT IGNORE INTO `knowledge_article_versions` (`article_id`, `version`, `content`, `title`, `edited_by`, `change_summary`)
SELECT a.id, 1, a.content, a.title, a.author_id, 'เวอร์ชันภาษาไทยเริ่มต้น'
FROM `knowledge_articles` a WHERE a.slug = 'linux-file-permissions-th';

INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`)
SELECT a.id, s.id FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'sql-injection'
WHERE a.slug = 'what-is-sql-injection-th';

INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`)
SELECT a.id, s.id FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'web-security'
WHERE a.slug = 'csrf-token-th';

INSERT IGNORE INTO `knowledge_article_skills` (`article_id`, `skill_id`)
SELECT a.id, s.id FROM `knowledge_articles` a
JOIN `skills` s ON s.slug = 'linux'
WHERE a.slug = 'linux-file-permissions-th';
