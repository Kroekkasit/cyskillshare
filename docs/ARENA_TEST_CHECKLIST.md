# Cyber Arena — Manual Test Checklist

```text
[ ] Browse Arena (/arena)
[ ] Browse challenges
[ ] Search challenges (?search=)
[ ] Filter challenges (category, difficulty, solved/unsolved)
[ ] Sort challenges (newest, points, most solved, difficulty)
[ ] Open challenge
[ ] View attachment (if any) via authorized download
[ ] Reveal hint (once; progressive order)
[ ] Reveal same hint again → no extra penalty
[ ] Submit incorrect flag
[ ] Submit correct flag → points awarded
[ ] Solve recorded on progress + leaderboard
[ ] Duplicate correct submit → no double points
[ ] Rate limit: many incorrect submits → 429 / wait message
[ ] Progress page updates
[ ] Leaderboard filters (all / month / semester)
[ ] Event page shows event challenges + event leaderboard only
[ ] Challenge discussion opens Community thread with spoiler warning
[ ] Draft challenge ID guess → 404 for students
[ ] Student cannot open /arena/admin/challenges
[ ] Instructor can create / publish / archive challenge
[ ] Flag never appears in HTML source / JS / API
[ ] XSS in description rendered escaped (markdown-safe)
[ ] SQL injection in search/IDs does not succeed
[ ] CSRF missing on submit → rejected
[ ] Dangerous upload (shell.php, .htaccess) rejected
```
