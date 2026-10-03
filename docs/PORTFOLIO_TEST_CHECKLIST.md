# Portfolio — Manual Test Checklist

```text
[ ] View public /portfolio/student1
[ ] Anonymous blocked from community portfolio (student2)
[ ] Anonymous blocked from private portfolio (student3) — 403
[ ] Owner can view own private portfolio
[ ] Update settings (CSRF)
[ ] Create project as draft — not on public portfolio
[ ] Publish project — appears publicly
[ ] Feature project — limit enforced
[ ] Connect skills — project_skills rows
[ ] Request verification — pending
[ ] Student cannot approve own project
[ ] Instructor can verify → skill evidence accepted
[ ] Reject verification stores reason
[ ] Upload valid PNG
[ ] Reject shell.php / .svg / fake image / path traversal name
[ ] IDOR: cannot edit another user’s project
[ ] Mass assignment: cannot set user_id via POST
[ ] XSS escaped in bio/description
[ ] javascript: URL rejected
[ ] Resume page prints cleanly
[ ] Discovery filters hide private/draft
[ ] Reactions unique per type
```
