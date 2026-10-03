# CySkillShare

Cybersecurity skill-sharing platform for students at the **College of Computing, Khon Kaen University (KKU)**.

This repository currently contains **Phase 1**: project architecture, database schema, and a secure PHP foundation.

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

Schema and seed data load automatically on the **first** database container start (Docker volume init).

### Useful commands

```bash
# View logs
docker compose logs -f app
docker compose logs -f db

# Stop
docker compose down

# Reset database (re-run schema + seed)
docker compose down -v
docker compose up -d --build
```

### Services

| Service | Container | Host access |
|---------|-----------|-------------|
| `app` | PHP 8.2 + Apache | http://localhost:8080 |
| `db` | MySQL 8.4 | **not** published to the host (Docker network only) |

App connects to MySQL with `DB_HOST=db` (Compose service name).

Why `docker/` exists: Dockerfile and container entrypoint stay out of application code.

## Directory structure

```text
cyskillshare/
├── app/                 Controllers, Models, Services, Middleware, Helpers, Core
├── bootstrap/           Application bootstrapping
├── config/              app.php, database.php
├── database/            schema.sql, seed.sql (auto-imported by MySQL container)
├── docker/              PHP/Apache image + entrypoint
├── docker-compose.yml
├── public/              Web root (index.php, assets, .htaccess)
├── resources/views/     Layouts, components, pages
├── routes/              web.php
└── storage/             logs/, uploads/
```

## Development

- Routes live in `routes/web.php`.
- Controllers stay thin; SQL belongs in Models; HTML belongs in Views.
- Shared security tools: `Auth`, `Csrf`, `Validator`, `Session`, `e()`.
- Errors in development are displayed; in production they are logged to `storage/logs/app.log`.
- Source code is bind-mounted into the `app` container — edit locally, refresh the browser.

### Architecture decisions (Phase 1)

- Plain PHP MVC-inspired layout (no Laravel/ORM).
- Polymorphic `votes` / `bookmarks` / `reports` use allowlisted `target_type` values validated in application code.
- Soft deletes on `replies` (`deleted_at`) for moderation/audit.
- Roles via `roles` + `user_roles` (not a free-text column on `users`).

## Security

| Control | Implementation |
|---------|----------------|
| Password hashing | `password_hash()` / `password_verify()` with `PASSWORD_DEFAULT` |
| CSRF | `Csrf::token()` / middleware on state-changing routes |
| Sessions | HttpOnly, SameSite=Lax, Secure when HTTPS; regenerate on login; destroy on logout |
| Authorization | Server-side `Auth::hasRole()`, `Auth::requireRole()`, `Auth::canManage()` |
| SQL | PDO prepared statements only |
| XSS | `e()` helper (`htmlspecialchars`) for all user-generated output |
| Secrets | Credentials in `.env` (not committed); activity logs redact sensitive keys |

Protected paths (`/app`, `/config`, `/database`, `/storage`, `.env`) are outside the public document root. MySQL is not exposed on the host.

## Demo accounts (DEVELOPMENT ONLY)

**Do not use these credentials in production.**

| Username | Password | Roles |
|----------|----------|-------|
| `admin` | `Admin@123!` | admin, student |
| `student1` | `Student@123!` | student |
| `student2` | `Student@123!` | student |
| `student3` | `Student@123!` | student |
| `mentor1` | `Mentor@123!` | mentor, student |

## Security test checklist (Phase 1)

- [x] PDO + prepared statements + utf8mb4
- [x] Password hash/verify
- [x] Session start, regenerate on login, destroy on logout
- [x] Unauthenticated users blocked from protected actions
- [x] Students cannot access `/admin/demo`
- [x] Missing/invalid CSRF rejected on POST
- [x] XSS payload rendered as text via `e()`
- [x] SQL built with bound parameters (no string concatenation of user input)

## What is intentionally not in Phase 1

CTF engine, Docker labs, AI assistant, skill tree/XP/achievements, leaderboard, mentorship UI, advanced analytics, and polished forum design — reserved for later phases. The schema is structured so those features can be added without redesigning core tables.

## License

Academic / educational project for College of Computing, KKU.
