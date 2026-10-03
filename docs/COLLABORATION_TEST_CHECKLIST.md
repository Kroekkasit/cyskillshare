# Phase 8 — Collaboration Test Checklist

## Groups & teams

- [ ] Create study group / CTF team / project team
- [ ] Open join vs approval vs invite-only
- [ ] Invite, accept/decline invitation
- [ ] Review join request (cannot self-approve)
- [ ] Leave group; owner cannot leave without transfer
- [ ] Community groups hidden from anonymous users
- [ ] CTF team dashboard shows aggregate Arena solves (not manual strengths)

## Mentorship

- [ ] Enable mentor profile (settings)
- [ ] Staff verify mentor; users cannot self-verify
- [ ] Request mentorship; capacity limit enforced
- [ ] Accept / decline / cancel
- [ ] Add goals & schedule HTTPS-only sessions
- [ ] Non-participants get 403 on mentorship detail

## Discovery & recruitment

- [ ] People search by skill; blocked users excluded
- [ ] Recommendations show human-readable reasons (no scores)
- [ ] `show_in_discovery` privacy toggle
- [ ] Create recruitment post; apply; owner notified

## Security

- [ ] CSRF on all POSTs
- [ ] XSS in bios/descriptions escaped
- [ ] IDOR on mentorship / group manage
- [ ] Rate limits on invites / requests / search
- [ ] Report `collab_group` via moderation dialog
