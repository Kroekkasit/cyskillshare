# Phase 6 — Writeups & Knowledge Base Test Checklist

## Writeups

- [ ] Create draft at `/writeups/create` (auth required)
- [ ] Autosave returns without error; “Saved …” updates (debounce ≥ ~8s)
- [ ] Preview uses same Markdown → sanitize → HTML pipeline as publish
- [ ] Owner can edit; other student gets 403
- [ ] Publish / archive via owner actions
- [ ] Visibility: private not listed for others; community requires login
- [ ] XSS payload in content renders as text (no script execution)
- [ ] Code blocks scroll horizontally; content is inert text
- [ ] Tags / skills / challenges link correctly; no flag leakage on challenge cards
- [ ] Helpful / clear / practical reaction once per user
- [ ] Quality checklist shows done / missing items (not a numeric score)

## Knowledge articles

- [ ] Create draft → submit for review
- [ ] Instructor/mentor/admin can approve / request changes / reject
- [ ] Student cannot approve own or others’ articles
- [ ] Version history preserved on significant edit
- [ ] Official / verified badge only when `is_official` or reviewer role applies
- [ ] Sources / skill links display; HTTPS external refs only

## Integrations

- [ ] Skill page `/skills/{slug}` shows Learn (articles + writeups)
- [ ] Portfolio can show featured writeups (public published only)
- [ ] Global search returns writeups + knowledge
- [ ] Home shows latest public writeups
- [ ] Published writeup creates pending/accepted skill evidence via SkillEvidenceService

## Security

- [ ] CSRF on all POSTs
- [ ] Mass assignment: cannot set `view_count`, `helpful_count`, `user_id`, `published_at` via POST
- [ ] IDOR on edit/autosave/publish
- [ ] Unauthorized article approval rejected
- [ ] Dangerous URLs (`javascript:`) stripped in Markdown links
- [ ] Image uploads limited to JPEG/PNG/WebP when media upload is used
