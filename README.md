# CySkillShare

Cybersecurity skill-sharing platform for students at the **College of Computing, Khon Kaen University (KKU)**.

This repository currently contains:

- **Phase 1** — secure PHP foundation (auth, PDO, CSRF, RBAC, routing)
- **Phase 2** — Community & Discussion system

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

Schema and seed data load automatically on the **first** database container start.

### Reset database (re-seed)

```bash
docker compose down -v
docker compose up -d --build
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
