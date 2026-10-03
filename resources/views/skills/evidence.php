<?php
/**
 * @var list<array<string, mixed>> $evidence
 * @var string $statusFilter
 * @var list<array<string, mixed>> $skills
 */
use App\Services\SkillEvidenceService;
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>My Evidence</h1>
        <p class="muted">Evidence submitted for your skill progress. Manual submissions require verification.</p>
    </div>
</section>

<div class="card">
    <form class="filter-row" method="get" action="<?= e(url('/skills/evidence')) ?>">
        <div class="form-group">
            <label for="status">Filter by status</label>
            <select id="status" name="status" onchange="this.form.submit()">
                <?php foreach (['all' => 'All', 'accepted' => 'Accepted', 'pending' => 'Pending', 'rejected' => 'Rejected'] as $val => $label): ?>
                    <option value="<?= e($val) ?>" <?= $statusFilter === $val ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($evidence === []): ?>
        <p class="muted">No evidence found<?= $statusFilter !== 'all' ? ' with this filter' : '' ?>.</p>
    <?php else: ?>
        <table class="leaderboard-table skill-evidence-table">
            <thead>
                <tr>
                    <th scope="col">Skill</th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                    <th scope="col">Source</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evidence as $ev): ?>
                    <?php
                    $sourceUrl = SkillEvidenceService::sourceUrl(
                        (string) $ev['source_type'],
                        isset($ev['source_id']) ? (int) $ev['source_id'] : null
                    );
                    ?>
                    <tr>
                        <td><a href="<?= e(url('/skills/' . $ev['skill_slug'])) ?>"><?= e($ev['skill_name']) ?></a></td>
                        <td><?= e($ev['title']) ?></td>
                        <td><span class="pill"><?= e(str_replace('_', ' ', $ev['evidence_type'])) ?></span></td>
                        <td>
                            <span class="pill status-<?= e($ev['status']) ?>"><?= e(ucfirst($ev['status'])) ?></span>
                        </td>
                        <td class="muted"><?= e(time_ago((string) $ev['created_at'])) ?></td>
                        <td>
                            <?php if ($sourceUrl): ?>
                                <a href="<?= e(url($sourceUrl)) ?>">View</a>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Submit manual evidence</h2>
    <p class="muted">Describe work you have done that demonstrates this skill. An instructor or mentor will review it.</p>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/skills/evidence/manual')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="skill_id">Skill</label>
            <select id="skill_id" name="skill_id" required>
                <option value="">Select a skill…</option>
                <?php foreach ($skills as $s): ?>
                    <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required maxlength="255" placeholder="Brief title for your evidence">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" maxlength="2000" placeholder="Describe what you did and how it demonstrates the skill"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Submit for review</button>
    </form>
</div>
