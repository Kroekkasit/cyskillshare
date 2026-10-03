# Phase 7 — Cyber Labs Test Checklist

## Metadata & discovery

- [ ] `/labs` lists published labs with filters
- [ ] Lab detail shows objectives, skills, prerequisites, task counts — no answers
- [ ] Recommended labs appear on home / skill Practice section
- [ ] Search returns labs

## Lifecycle

- [ ] Start lab creates instance (queued → provisioning → ready)
- [ ] Concurrent limit enforced (default 2 per user)
- [ ] Capacity-full message when global max reached
- [ ] Timer uses server `seconds_remaining` (PHP timezone ≠ MySQL must not expire early)
- [ ] Stop / reset (confirm + rate limit)
- [ ] Expired instances cleaned; progress rows preserved
- [ ] Failed provisioning does not charge completion

## Tasks & validation

- [ ] Locked tasks until dependencies complete
- [ ] Flag / exact / multiple-choice / instance_secret validation
- [ ] Answers hashed in `lab_attempts`; expected answers never in HTML/JS
- [ ] Hints progressive with penalty; one reveal per hint
- [ ] Rate limits on submit / hint / start / reset
- [ ] Completing required tasks → completion + skill evidence

## Isolation / security

- [ ] Student cannot open another user’s instance (IDOR → 403)
- [ ] Gateway requires auth + ownership
- [ ] No Docker commands from user input
- [ ] No `runtime_secrets` / `validation_config` in student JSON/HTML (except simulated target teaching panel)
- [ ] Only infra roles can change CPU/memory/disk in admin
- [ ] CSRF on all POSTs

## Integrations

- [ ] Lab completion → `skill_evidence` (`source_type=lab`)
- [ ] Portfolio shows completed labs when `show_on_portfolio=1`
- [ ] Writeup create prefill via `?lab={slug}`
- [ ] Related writeups on lab detail

## Admin

- [ ] `/admin/labs` CRUD, publish/archive/feature
- [ ] Add tasks + validation (plaintext answer hashed server-side)
- [ ] Sweep expired instances
