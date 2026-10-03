<?php
/**
 * @var array<string, mixed> $detail
 * @var array{html:string,toc:list<array{level:int,id:string,text:string}>} $formattedDescription
 * @var array{html:string,toc:list<array{level:int,id:string,text:string}>}|null $formattedObjectives
 */
use App\Core\Auth;

$lab = $detail['lab'];
$slug = (string) $lab['slug'];
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/labs')) ?>">Cyber Labs</a>
    <span>/</span>
    <span><?= e((string) $lab['title']) ?></span>
</nav>

<article class="card lab-detail">
    <header class="lab-detail-head">
        <h1><?= e((string) $lab['title']) ?></h1>
        <div class="lab-detail-meta">
            <span class="difficulty-badge difficulty-<?= e((string) $lab['difficulty']) ?>"><?= e(ucfirst((string) $lab['difficulty'])) ?></span>
            <span class="pill"><?= (int) ($lab['estimated_minutes'] ?? 0) ?> min</span>
            <span class="pill"><?= (int) ($detail['task_counts']['total'] ?? 0) ?> tasks</span>
            <?php if (!empty($detail['completion'])): ?>
                <span class="pill solved">✓ Completed</span>
            <?php endif; ?>
        </div>
    </header>

    <div class="lab-detail-stats muted">
        <span><?= (int) ($lab['start_count'] ?? 0) ?> starts</span>
        <span><?= (int) ($lab['completion_count'] ?? 0) ?> completions</span>
        <span><?= e((string) ($lab['environment_type'] ?? 'browser')) ?> environment</span>
    </div>

    <?php if (!empty($lab['short_description'])): ?>
        <p class="lab-lead"><?= e((string) $lab['short_description']) ?></p>
    <?php endif; ?>

    <div class="content-body lab-description">
        <?= $formattedDescription['html'] ?>
    </div>

    <?php if ($formattedObjectives !== null): ?>
    <section class="lab-section">
        <h2>Learning Objectives</h2>
        <div class="content-body"><?= $formattedObjectives['html'] ?></div>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['skills'])): ?>
    <section class="lab-section">
        <h2>Skills Practiced</h2>
        <div class="tag-row">
            <?php foreach ($detail['skills'] as $sk): ?>
                <a class="pill skill-chip" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['prerequisites'])): ?>
    <section class="lab-section">
        <h2>Prerequisites</h2>
        <ul class="lab-prereq-list">
            <?php foreach ($detail['prerequisites'] as $pre): ?>
                <li>
                    <a href="<?= e(url('/skills/' . $pre['slug'])) ?>"><?= e((string) $pre['name']) ?></a>
                    <span class="muted">— level <?= (int) $pre['minimum_level'] ?>+</span>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="muted small">Mode: <?= e((string) ($lab['prerequisite_mode'] ?? 'recommended')) ?></p>
    </section>
    <?php endif; ?>

    <section class="lab-section">
        <h2>Tasks Overview</h2>
        <p class="muted"><?= (int) ($detail['task_counts']['required'] ?? 0) ?> required · <?= (int) ($detail['task_counts']['optional'] ?? 0) ?> optional</p>
        <ul class="lab-task-overview">
            <?php foreach ($detail['tasks'] as $i => $task): ?>
                <li>
                    <span class="lab-task-num"><?= (int) $i + 1 ?></span>
                    <?= e((string) $task['title']) ?>
                    <?php if ((int) ($task['required'] ?? 0) === 1): ?>
                        <span class="pill">Required</span>
                    <?php endif; ?>
                    <span class="muted"><?= (int) ($task['points'] ?? 0) ?> pts</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php if (!empty($detail['services'])): ?>
    <section class="lab-section">
        <h2>Environment Services</h2>
        <ul class="lab-services-list">
            <?php foreach ($detail['services'] as $svc): ?>
                <li>
                    <strong><?= e((string) $svc['name']) ?></strong>
                    <span class="muted"><?= e((string) $svc['protocol']) ?>:<?= (int) $svc['display_port'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['writeups'])): ?>
    <section class="lab-section">
        <h2>Related Writeups</h2>
        <ul class="result-list">
            <?php foreach ($detail['writeups'] as $wu): ?>
                <li>
                    <a href="<?= e(url('/writeups/' . $wu['username'] . '/' . $wu['slug'])) ?>"><?= e((string) $wu['title']) ?></a>
                    <span class="muted small">@<?= e((string) $wu['username']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <div class="lab-start-panel card-inline-actions">
        <?php if (Auth::check()): ?>
            <?php if (!empty($detail['active_instance'])): ?>
                <?php $inst = $detail['active_instance']; ?>
                <a class="btn btn-primary" href="<?= e(url('/labs/' . $slug . '/instance/' . (int) $inst['id'])) ?>">
                    Continue Lab Session
                </a>
                <span class="pill"><?= e(ucfirst((string) ($inst['status'] ?? ''))) ?></span>
            <?php else: ?>
                <form method="post" action="<?= e(url('/labs/' . $slug . '/start')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary" type="submit">Start Lab</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Sign in to Start</a>
        <?php endif; ?>
    </div>
</article>

<?php if (Auth::check() && !empty($detail['completion'])): ?>
<section class="card lab-feedback-section">
    <h2>Rate this lab</h2>
    <form method="post" action="<?= e(url('/labs/' . $slug . '/feedback')) ?>">
        <?= csrf_field() ?>
        <div class="filter-row">
            <div class="form-group">
                <label for="difficulty_rating">Difficulty</label>
                <select id="difficulty_rating" name="difficulty_rating" required>
                    <option value="too_easy">Too easy</option>
                    <option value="appropriate" selected>Appropriate</option>
                    <option value="too_hard">Too hard</option>
                </select>
            </div>
            <div class="form-group">
                <label for="clarity_rating">Clarity</label>
                <select id="clarity_rating" name="clarity_rating" required>
                    <option value="poor">Poor</option>
                    <option value="okay">Okay</option>
                    <option value="good" selected>Good</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="feedback">Comments (optional)</label>
            <textarea id="feedback" name="feedback" rows="3" maxlength="5000"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Submit Feedback</button>
    </form>
</section>
<?php endif; ?>
