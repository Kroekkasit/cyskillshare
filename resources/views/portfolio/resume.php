<?php
/**
 * @var array{data: array<string, mixed>, include: list<string>} $resume
 */
$data = $resume['data'];
$include = $resume['include'];
$user = $data['user'];
$portfolio = $data['portfolio'];
?>
<div class="resume-document">
    <div class="resume-toolbar no-print">
        <button class="btn btn-primary" type="button" data-print>Print / Save PDF</button>
        <a class="btn" href="<?= e(url('/portfolio/' . $user->username)) ?>">Back to Portfolio</a>
    </div>

    <header class="resume-header">
        <h1><?= e($user->full_name ?: $user->username) ?></h1>
        <?php if (!empty($portfolio['headline'])): ?>
            <p class="resume-headline"><?= e((string) $portfolio['headline']) ?></p>
        <?php endif; ?>
        <div class="resume-contact muted">
            @<?= e($user->username) ?>
            <?php if (!empty($portfolio['university'])): ?> · <?= e((string) $portfolio['university']) ?><?php endif; ?>
            <?php if (!empty($portfolio['program'])): ?> · <?= e((string) $portfolio['program']) ?><?php endif; ?>
            <?php if (!empty($portfolio['show_location']) && !empty($portfolio['location'])): ?> · <?= e((string) $portfolio['location']) ?><?php endif; ?>
        </div>
        <div class="resume-links">
            <?php if (!empty($portfolio['github_url'])): ?>
                <span><?= e((string) $portfolio['github_url']) ?></span>
            <?php endif; ?>
            <?php if (!empty($portfolio['linkedin_url'])): ?>
                <span><?= e((string) $portfolio['linkedin_url']) ?></span>
            <?php endif; ?>
            <?php if (!empty($portfolio['website_url'])): ?>
                <span><?= e((string) $portfolio['website_url']) ?></span>
            <?php endif; ?>
        </div>
    </header>

    <?php if (in_array('about', $include, true) && !empty($portfolio['about'])): ?>
    <section class="resume-section">
        <h2>Summary</h2>
        <div class="resume-text"><?= e(strip_tags((string) $portfolio['about'])) ?></div>
    </section>
    <?php endif; ?>

    <?php if (in_array('skills', $include, true) && !empty($data['featured_skills'])): ?>
    <section class="resume-section">
        <h2>Skills</h2>
        <ul class="resume-list-inline">
            <?php foreach ($data['featured_skills'] as $skill): ?>
                <li><?= e((string) $skill['name']) ?> (<?= e((string) $skill['level_name']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (in_array('projects', $include, true) && !empty($data['projects'])): ?>
    <section class="resume-section">
        <h2>Projects</h2>
        <?php foreach ($data['projects'] as $p): ?>
            <div class="resume-item">
                <strong><?= e((string) $p['title']) ?></strong>
                <?php if (!empty($p['short_description'])): ?>
                    <p><?= e((string) $p['short_description']) ?></p>
                <?php endif; ?>
                <?php if (!empty($p['technologies'])): ?>
                    <p class="muted small"><?= e(implode(', ', $p['technologies'])) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (in_array('education', $include, true) && !empty($data['education'])): ?>
    <section class="resume-section">
        <h2>Education</h2>
        <?php foreach ($data['education'] as $edu): ?>
            <div class="resume-item">
                <strong><?= e((string) $edu['institution']) ?></strong>
                <?php if (!empty($edu['program'])): ?> — <?= e((string) $edu['program']) ?><?php endif; ?>
                <span class="muted">
                    <?php if (!empty($edu['start_year']) || !empty($edu['end_year'])): ?>
                        (<?= e((string) ($edu['start_year'] ?? '')) ?><?= !empty($edu['end_year']) ? '–' . e((string) $edu['end_year']) : '' ?>)
                    <?php endif; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (in_array('experience', $include, true) && !empty($data['experience'])): ?>
    <section class="resume-section">
        <h2>Experience</h2>
        <?php foreach ($data['experience'] as $exp): ?>
            <div class="resume-item">
                <strong><?= e((string) $exp['role']) ?></strong>, <?= e((string) $exp['organization']) ?>
                <span class="muted">
                    (<?= e((string) ($exp['start_date'] ?? '')) ?><?= !empty($exp['is_current']) ? ' – Present' : (!empty($exp['end_date']) ? ' – ' . e((string) $exp['end_date']) : '') ?>)
                </span>
                <?php if (!empty($exp['description'])): ?>
                    <p><?= e((string) $exp['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (in_array('certifications', $include, true) && !empty($data['certifications'])): ?>
    <section class="resume-section">
        <h2>Certifications</h2>
        <?php foreach ($data['certifications'] as $cert): ?>
            <div class="resume-item">
                <strong><?= e((string) $cert['name']) ?></strong>
                <?php if (empty($cert['is_verified'])): ?><span class="muted">(user-provided)</span><?php endif; ?>
                <?php if (!empty($cert['issuer'])): ?> — <?= e((string) $cert['issuer']) ?><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (in_array('challenges', $include, true) && !empty($data['challenge_stats'])): ?>
    <section class="resume-section">
        <h2>Challenge Achievements</h2>
        <ul class="resume-list-inline">
            <?php foreach ($data['challenge_stats'] as $stat): ?>
                <li><?= e((string) $stat['name']) ?>: <?= (int) $stat['solved'] ?> solved</li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>
</div>
