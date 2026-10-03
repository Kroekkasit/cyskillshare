# CySkillShare

Cybersecurity skill-sharing platform for students at the **College of Computing, Khon Kaen University (KKU)**.

This repository currently contains:

- **Phase 1** — secure PHP foundation (auth, PDO, CSRF, RBAC, routing)
- **Phase 2** — Community & Discussion system
- **Phase 3** — Cyber Arena / CTF challenge system
- **Phase 4** — Cybersecurity Skill Tree & Evidence system
- **Phase 5** — Cybersecurity Portfolio & Project Showcase
- **Phase 6** — Technical Writeups & Cybersecurity Knowledge Base
- **Phase 7** — Cyber Labs & Practical Training Environments
- **Phase 8** — Mentorship, Study Groups & Team Collaboration

## Requirements

| Component | Version |
|-----------|---------|
| Docker | 24+ with Compose plugin |
| (inside containers) | PHP 8.2 + Apache, MySQL 8.4, utf8mb4 |

You do **not** need PHP or MySQL installed on the host. Everything runs via Docker Compose.

Document root inside the app container is `public/` — never the project root.

## Quick start (Docker Compose)

```bash
cd cyskillshare
cp .env.example .env
docker compose up -d --build
```

Open http://localhost:8080

Schema and seed data (including Arena challenges/events) load automatically on the **first** database container start:

1. `01-schema.sql` — core schema  
2. `02-seed.sql` — users, community  
3. `03-arena-schema.sql` — Arena tables  
4. `04-seed-arena.sql` — challenges, hints, events  
5. `05-arena-fk.sql` — `threads.challenge_id` FK  
6. `06-skills-schema.sql` — Skill Tree & evidence tables  
7. `07-seed-skills.sql` — skills, requirements, challenge mappings  
8. `08-portfolio-schema.sql` — portfolios & projects  
9. `09-seed-portfolio.sql` — demo portfolios/projects  
10. `10-knowledge-schema.sql` — writeup relations & knowledge articles  
11. `11-seed-knowledge.sql` — demo writeups & knowledge articles  
12. `12-labs-schema.sql` — Cyber Labs tables + FKs to stubs  
13. `13-seed-labs.sql` — demo labs, tasks, validations, hints  
14. `14-collaboration-schema.sql` — groups, mentors, recruitment, blocks  
15. `15-seed-collaboration.sql` — demo groups, mentors, CTF team  

### Reset database (re-seed)

```bash
docker compose down -v
docker compose up -d --build
```

To apply Arena / Knowledge schemas on an **existing** volume without wiping:

```bash
docker exec -i cyskillshare-db mysql -uroot -prootsecret cyskillshare < database/migrations/003_phase3_arena.sql
docker exec -i cyskillshare-db mysql -uroot -prootsecret cyskillshare < database/seed_arena.sql
docker exec -i cyskillshare-db mysql -uroot -prootsecret cyskillshare < database/knowledge_schema.sql
docker exec -i cyskillshare-db mysql -uroot -prootsecret cyskillshare < database/seed_knowledge.sql
```

### Services

| Service | Container | Host access |
|---------|-----------|-------------|
| `app` | PHP 8.2 + Apache | http://localhost:8080 |
| `db` | MySQL 8.4 | Docker network only (`DB_HOST=db`) |

## Community System (Phase 2)

Features:

- Channels (loaded from DB)
- Discussions / threads (create, edit, soft-delete)
- Replies (edit, soft-delete)
- Voting (upvote/downvote, one vote per user/target, no self-votes)
- Best answers → marks thread `solved`
- Bookmarks (`/community/bookmarks`)
- Tags + `/tag/{slug}`
- Search (`/search?q=`)
- Notifications (reply, mention `@user`, best answer)
- Reports + moderator queue (`/moderation/reports`)
- Pin / lock threads (moderator/admin)
- User profiles with real contribution counts
- Pagination + safe sort whitelist
- Restricted Markdown (escaped; code blocks with copy button)
- Dark Cyber Academy theme (+ light toggle)

### Definitions

- **Unanswered** = threads with **0 non-deleted replies**
- **Solved** = `threads.status = solved` (set when a best answer is marked)

### How to create a discussion

1. Login
2. Open `/community` → **+ New Discussion**
3. Choose channel, title, content, optional tags
4. Submit (CSRF + server validation required)

### How to mark a best answer

1. Open your thread (or act as moderator/admin)
2. On a reply, click **Mark Best Answer**
3. Thread status becomes `solved`

### How moderation works

Accounts with `moderator` or `admin` roles can:

- Visit `/moderation/reports`
- Resolve / dismiss reports
- Pin / lock threads
- Soft-delete content

Actions are written to `activity_logs`.

## Collaboration (Phase 8)

Mentorship, study groups, CTF teams, project teams, people discovery, and recruitment — skill-relevant matching without popularity contests.

> Portfolio `/projects` remains individual showcases. Collaboration project teams use `collab_groups` with `group_type=project` (not a second projects table).

Features:

- Study groups, CTF teams, and project teams (`collab_groups`) with join policies, skills, goals, activities, resources
- Mentors (optional verification by staff) + mentorship requests, goals, sessions
- People discovery by skill / mentor availability with explainable reasons
- Deterministic collaboration recommendations (`CollaborationMatchService`)
- Recruitment posts + applications
- User blocking + report targets for groups
- Discovery privacy (`users.show_in_discovery`)
- Rate limits on invites, join requests, mentorship requests, searches

### Key URLs

| Path | Purpose |
|------|---------|
| `/collaboration` | Recommended matches hub |
| `/people` | People discovery |
| `/groups` | Study / project groups |
| `/teams` | CTF teams |
| `/mentors` | Mentor directory |
| `/mentorship` | My mentorships |
| `/recruitment` | Looking for teammates |
| `/admin/collaboration` | Staff verify mentors / suspend groups |

### Demo seed

- Groups: Web Security Study Group, Linux & Networking, Digital Forensics Club, Packet Pirates (CTF), Open Source SOC Dashboard (project)
- Mentors: `mentor1` (verified), `instructor1` (verified), `student1` (peer)
- Active mentorship: mentor1 ↔ student3
- Recruitment: Web Security teammate for Packet Pirates

### Security notes

- No self-verification of mentors; no mass-assignment of `owner_id` / `verification_status` / group `role`
- Mentorship IDOR protected via participant checks
- Meeting links must be HTTPS; content escaped
- See `docs/COLLABORATION_TEST_CHECKLIST.md`

## Cyber Labs (Phase 7)

Hands-on multi-step training environments — larger than Arena CTF challenges, with tasks, timers, and isolated lab gateways.

> Arena = short focused problems. Labs = investigate / analyze / report workflows. The web app never runs `docker` from user input; a controlled Lab Orchestrator (simulated in v1) provisions environments from approved templates only.

Features:

- Lab catalog with categories, difficulty, skills, prerequisites (recommended/required)
- Multi-step tasks with dependencies, hints, and hashed answer validation
- Per-instance secrets (IPs, flags, IOCs) — never exposed via public lab metadata
- Instance lifecycle: start → provision → run → reset/stop/expire → cleanup
- Server-authoritative timer (`TIMESTAMPDIFF` vs MySQL `NOW()`)
- Browser lab gateway (`/labs/gateway/{id}`) with ownership checks
- Completion → Skill Evidence (`source_type = lab`) + portfolio + writeup CTA
- Admin authoring (`/admin/labs`) — content vs infra roles for resource limits
- Rate limits on start / reset / submit / hints
- Concurrent instance limits (per-user and global)

### First-version execution model

v1 uses a **simulated orchestrator** (no Docker-in-Docker). Templates define allowed environments; the gateway renders a per-instance simulated target. Architecture is ready for a future HTTP orchestrator (`LABS_ORCHESTRATOR=http`) without rewriting controllers.

### Key URLs

| Path | Purpose |
|------|---------|
| `/labs` | Lab catalog |
| `/labs/{slug}` | Lab detail |
| `/labs/{slug}/start` | Start instance (POST) |
| `/labs/{slug}/instance/{id}` | Active lab session |
| `/labs/gateway/{id}` | Lab environment gateway |
| `/labs/history` | Personal lab history |
| `/admin/labs` | Author / publish labs |

### Demo labs (after seed)

- Vulnerable Web Application (intermediate)
- Suspicious Network Traffic (beginner)
- Compromised Workstation (intermediate)
- Web Server Compromise (advanced)
- Linux Security Investigation (intermediate)

Accounts: start labs as `student1` / `Student@123!`. Manage as `instructor1`.

### Security notes

- Never accept Docker commands or Compose YAML from browsers
- Never expose `lab_task_validations.validation_config` or raw `runtime_secrets` to students
- Flag/answer comparison uses `hash_equals` / SHA-256 where applicable
- Gateway enforces authentication, ownership, ready status, and expiry
- See `docs/LABS_TEST_CHECKLIST.md`

## Writeups & Knowledge Base (Phase 6)

Technical writeups and structured knowledge articles — separate content types that connect Community → Arena → Skills → Portfolio.

> Writeups capture personal practical experience. Knowledge articles are reusable, reviewed educational material. Neither executes user code; Markdown is sanitized to safe HTML.

Features:

- Database-driven writeup categories, tags, difficulty, visibility (`public` / `community` / `private`)
- Markdown editor with templates, preview, and rate-limited autosave
- Safe rendering (escaped HTML, inert code blocks, TOC from H2/H3, reading time)
- Links to skills, Arena challenges, projects, community threads
- Published writeups → Skill Evidence (`source_type = writeup`) via existing `SkillEvidenceService`
- Knowledge articles with version history and instructor/mentor review workflow
- Helpful / clear / practical reactions (`content_reactions`)
- Discovery: search, filters, featured content; Learn section on skill pages
- Portfolio featured writeups; home + global search integration
- Admin feature toggle (`/admin/writeups`) and knowledge review queue (`/admin/knowledge/review`)

### Key URLs

| Path | Purpose |
|------|---------|
| `/writeups` | Writeup discovery |
| `/writeups/create` | New writeup (auth) |
| `/writeups/{username}/{slug}` | Writeup reading page |
| `/writeups/edit/{id}` | Edit / preview / publish |
| `/knowledge` | Knowledge Base home |
| `/knowledge/category/{slug}` | Category listing |
| `/knowledge/article/{slug}` | Article reading page |
| `/knowledge/create` | New knowledge article |
| `/knowledge/{id}/history` | Version history |
| `/admin/writeups` | Feature / moderate writeups |
| `/admin/knowledge/review` | Review queue |

### Editorial workflow (knowledge)

```text
Draft → Submit for Review → Under Review → Approved → Published
                              ↓
                     Changes requested → Edit → Resubmit
```

Only instructor / mentor / admin can approve. Status badges such as “Instructor Verified” come from RBAC/`is_official`, not free-text labels.

### Demo content (after seed)

Writeups (published): SQL Injection Beyond the Basics, Memory Analysis with Volatility, Analyzing a Suspicious PE File, Understanding ARP Spoofing.

Knowledge articles: What is SQL Injection?, Understanding CSRF Tokens, Linux File Permissions, TCP Three-Way Handshake, Introduction to Digital Forensics, Understanding PE Files, What is XSS?, Introduction to Network Reconnaissance.

### Security notes

- Never mass-assign `view_count`, `helpful_count`, `published_at`, or `user_id` from POST
- Preview uses the same sanitization pipeline as published pages
- Challenge flags / private lab data must not appear in writeups via challenge joins
- Uploads limited to safe images (JPEG/PNG/WebP); no malware sample hosting
- AI assist architecture reserved — not implemented yet

## Portfolio & Projects (Phase 5)

Professional cybersecurity showcase connected to Skills, Arena, and Community evidence.

> The portfolio answers “what can this student do?” with projects, skills, and verifiable evidence — not XP badges.

Features:

- Public / community / private portfolio visibility (server-enforced)
- Portfolio settings (`/settings/portfolio`) — sections, links, privacy
- Projects CRUD with draft/published, technologies, skill links, challenge links
- Featured projects (configurable limit) + project discovery (`/projects`)
- Secure project image upload (`storage/projects/`, authorized stream)
- Project verification (instructor/mentor/admin; no self-verify)
- Project → Skill Evidence (pending until verified)
- Education / experience / certifications (user-provided label)
- Dashboard completeness guidance (not a skill score)
- Printable resume (`/portfolio/{username}/resume` → browser Print/PDF)
- Lightweight reactions + analytics aggregates
- Global search includes public projects

### Key URLs

| Path | Purpose |
|------|---------|
| `/portfolio/{username}` | Public portfolio |
| `/portfolio/{username}/resume` | Printable resume |
| `/dashboard/portfolio` | Owner dashboard |
| `/settings/portfolio` | Settings & sections |
| `/projects` | Project discovery |
| `/projects/create` | New project |
| `/projects/{username}/{slug}` | Project detail |
| `/admin/portfolio` | Staff overview |
| `/admin/projects/verification` | Verification queue |

### Demo (after seed)

- `student1` — public portfolio with featured projects  
- `student2` — community-only  
- `student3` — private  

## Skill Tree & Evidence (Phase 4)

Evidence-driven cybersecurity skills — **not** a global XP / game score system.

> Skill level is derived from accepted evidence (Arena solves, best answers, verified work), requirements, difficulty breadth, and optional instructor verification.

Features:

- Hierarchical skill tree (DB-managed categories / skills / parents)
- Configurable levels (Not Started → Demonstrated)
- Per-skill requirements stored in `skill_requirements`
- Prerequisites with circular-dependency protection
- `skill_evidence` with unique `(user, source_type, source_id, skill)` 
- Arena solve → automatic evidence via `SkillEvidenceService`
- Community best answer → evidence via `thread_skills` mappings
- Hooks for future writeups / projects / labs (`source_type` + `source_id`)
- Explainable progress (“Why this level?” checklists)
- Profile skill summary + privacy (`public` / `community` / `private`)
- Instructor/mentor verification queue
- Admin skill management + recalculate (includes solve backfill)

### Key URLs

| Path | Purpose |
|------|---------|
| `/skills` | Skill Tree |
| `/skills/{slug}` | Skill detail + evidence/requirements |
| `/skills/evidence` | My evidence |
| `/skills/verification` | Pending review (instructor/mentor/admin) |
| `/admin/skills` | Manage tree / requirements |
| `/admin/skills/recalculate` | Admin-only progress rebuild |

### How progress is calculated

1. Accepted evidence is collected for the skill  
2. Requirements for level 1…5 are checked in order (counts + difficulty)  
3. Highest fully satisfied level becomes `user_skills.current_level`  
4. Progress bar = fraction of **next** level’s requirements met  
5. Rejecting/removing evidence can lower the level on recalculation  

### How to add a skill

1. Login as instructor/admin → `/admin/skills/create`  
2. Set category, optional parent, description  
3. Configure requirements at `/admin/skills/{id}/requirements`  
4. Map Arena challenges via `challenge_skills` (seed or future admin UI)  
5. Run recalculate if requirements changed for existing users  

## Cyber Arena (Phase 3)

Practice cybersecurity skills through hands-on challenges. Arena points are **separate from future global XP**.

Features:

- Challenge catalog (search, filter, sort, pagination)
- Categories & difficulty
- Flag submission (server-side verification; flags stored as SHA-256 hashes)
- Progressive hints with point penalties (once per user/hint)
- Scoring: `max(0, base_points − hint_penalties)` stored at solve time
- Solve tracking + Arena point transactions
- My Progress (`/arena/progress`)
- Leaderboards (all time / this month / semester — dates in `config/arena.php`)
- CTF events with event-only scoring
- Challenge file downloads (staff upload; storage under `storage/challenges/`, not public)
- Challenge ↔ Community discussion (`threads.challenge_id`, spoiler warning)
- Instructor/admin challenge & event management
- Challenge writeups linked via Phase 6 `writeup_challenges`

### Key URLs

| Path | Purpose |
|------|---------|
| `/arena` | Arena home / dashboard |
| `/arena/challenges` | Challenge catalog |
| `/arena/challenges/{id}` | Challenge detail + submit |
| `/arena/categories/{slug}` | Category progress |
| `/arena/events` | CTF / practice events |
| `/arena/leaderboard` | Arena leaderboard |
| `/arena/progress` | Personal progress |
| `/arena/admin/challenges` | Staff challenge admin |

### How to create a challenge

1. Login as `instructor1`, `moderator1`, or `admin`
2. Open `/arena/admin/challenges/new`
3. Fill Basic Information, Content, Scoring, Flag, Tags, Publishing
4. Save as **draft** or **published**
5. On edit: add hints, attach files, then **Publish**

### How scoring & hints work

- Base points come from the challenge
- Revealing a hint applies its penalty once (recorded in `challenge_hint_usage`)
- On first correct flag: awarded points = base − sum(penalties), never below 0
- Later flag/points changes do not rewrite historical solves
- Changing flag/points after solves requires explicit confirmation in the admin form

### How to create an event

1. `/arena/admin/events/new`
2. Set type (`practice` / `ctf` / …), status, visibility, start/end
3. Assign existing challenges (no challenge duplication)
4. Event leaderboard sums points only from those challenges

### Leaderboard tie-break

1. Points DESC  
2. Solved count DESC  
3. Earliest first solve ASC  

### Development seed flags

Seed challenges use flags like `FLAG{cyskillshare_sqli_001}` (documented in `database/seed_arena.sql` comments). **Development only** — never use real secrets.

### Arena security testing

See `docs/ARENA_TEST_CHECKLIST.md`. Especially verify: no flag leak in HTML/JS, student cannot access admin/drafts, rate limiting on submit, duplicate solve prevention, safe file upload/download.

## Demo accounts (DEVELOPMENT ONLY)

| Username | Password | Roles |
|----------|----------|-------|
| `admin` | `Admin@123!` | admin, student |
| `student1` | `Student@123!` | student |
| `student2` | `Student@123!` | student |
| `student3` | `Student@123!` | student |
| `mentor1` | `Mentor@123!` | mentor, student |
| `moderator1` | `Student@123!` | moderator, student |
| `instructor1` | `Student@123!` | instructor, student |

## Security testing notes

Verify manually:

- Anonymous users cannot create/reply/vote
- User A cannot edit User B’s thread/reply (403)
- Students cannot open `/moderation/reports`
- Missing/invalid CSRF rejected
- `<script>alert(1)</script>` renders as text
- `' OR '1'='1` does not bypass auth/search
- Locked threads reject reply POSTs
- Vote uniqueness + no self-vote
- Mass-assignment fields (`is_pinned`, `user_id`, etc.) are not taken from raw `$_POST`

## Directory structure

```text
cyskillshare/
├── app/                 Controllers, Models, Services, Middleware, Helpers, Core
├── bootstrap/
├── config/
├── database/            schema.sql, seed.sql, migrations/
├── docker/
├── docker-compose.yml
├── public/
├── resources/views/
├── routes/web.php
└── storage/
```

## License

Academic / educational project for College of Computing, KKU.
