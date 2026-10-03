<?php
/**
 * @var \App\Models\User $user
 * @var array<string, mixed> $portfolio
 * @var array<string, bool> $sections
 * @var list<array<string, mixed>> $featured_skills
 * @var list<array<string, mixed>> $featured_projects
 * @var list<array<string, mixed>> $projects
 * @var list<array<string, mixed>> $challenge_stats
 * @var array<string, int> $community_stats
 * @var list<array<string, mixed>> $education
 * @var list<array<string, mixed>> $experience
 * @var list<array<string, mixed>> $certifications
 * @var list<array<string, mixed>> $featured_writeups
 * @var list<array<string, mixed>> $completed_labs
 * @var bool $is_owner
 */
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<header class="portfolio-header card">
    <div class="portfolio-header-main">
        <div class="portfolio-avatar"><?= e(strtoupper(substr($user->username, 0, 1))) ?></div>
        <div>
            <h1><?= e($user->full_name ?: $user->username) ?></h1>
            <p class="muted">@<?= e($user->username) ?></p>
            <?php if (!empty($portfolio['headline'])): ?>
                <p class="portfolio-headline"><?= e((string) $portfolio['headline']) ?></p>
            <?php endif; ?>
            <div class="portfolio-header-meta muted">
                <?php if (!empty($portfolio['university'])): ?>
                    <span><?= e((string) $portfolio['university']) ?></span>
                <?php endif; ?>
                <?php if (!empty($portfolio['program'])): ?>
                    <span><?= e((string) $portfolio['program']) ?></span>
                <?php endif; ?>
                <?php if (!empty($portfolio['show_location']) && !empty($portfolio['location'])): ?>
                    <span><?= e((string) $portfolio['location']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/portfolio/' . $user->username . '/resume')) ?>">Print Resume</a>
        <?php if ($is_owner): ?>
            <a class="btn btn-primary" href="<?= e(url('/settings/portfolio')) ?>">Edit Portfolio</a>
            <a class="btn" href="<?= e(url('/dashboard/portfolio')) ?>">Dashboard</a>
        <?php else: ?>
            <a class="btn" href="<?= e(url('/profile/' . $user->username)) ?>">Profile</a>
        <?php endif; ?>
    </div>
</header>

<?php if (!empty($sections['about']) && !empty($portfolio['about'])): ?>
<section class="card portfolio-section">
    <h2>About</h2>
    <div class="markdown-body"><?= markdown((string) $portfolio['about']) ?></div>
</section>
<?php endif; ?>

<?php if (!empty($sections['skills']) && $featured_skills !== []): ?>
<section class="card portfolio-section">
    <h2>Featured Skills</h2>
    <div class="portfolio-skills-grid">
        <?php foreach ($featured_skills as $skill): ?>
            <div class="portfolio-skill-card">
                <a href="<?= e(url('/skills/' . $skill['slug'])) ?>"><strong><?= e((string) $skill['name']) ?></strong></a>
                <span class="pill"><?= e((string) $skill['level_name']) ?></span>
                <span class="muted small"><?= (int) $skill['evidence_count'] ?> evidence</span>
                <?php if (!empty($skill['evidence_summary'])): ?>
                    <?php $es = $skill['evidence_summary']; ?>
                    <div class="portfolio-evidence-summary muted small">
                        <?php if ($es['challenges'] > 0): ?><span><?= (int) $es['challenges'] ?> challenges</span><?php endif; ?>
                        <?php if ($es['projects'] > 0): ?><span><?= (int) $es['projects'] ?> projects</span><?php endif; ?>
                        <?php if ($es['writeups'] > 0): ?><span><?= (int) $es['writeups'] ?> writeups</span><?php endif; ?>
                        <?php if ($es['verified'] > 0): ?><span><?= (int) $es['verified'] ?> verified</span><?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($sections['projects']) && ($featured_projects !== [] || $projects !== [])): ?>
<section class="portfolio-section">
    <?php if ($featured_projects !== []): ?>
        <h2>Featured Projects</h2>
        <div class="project-grid">
            <?php foreach ($featured_projects as $p): ?>
                <?php \App\Core\View::partial('portfolio/partials/project-card', [
                    'project' => $p,
                    'username' => $user->username,
                ]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($projects !== []): ?>
        <h2<?= $featured_projects !== [] ? ' class="portfolio-subheading"' : '' ?>>All Projects</h2>
        <div class="project-grid">
            <?php foreach ($projects as $p): ?>
                <?php \App\Core\View::partial('portfolio/partials/project-card', [
                    'project' => $p,
                    'username' => $user->username,
                ]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if (!empty($sections['writeups']) && ($featured_writeups ?? []) !== []): ?>
<section class="card portfolio-section">
    <h2>Featured Writeups</h2>
    <div class="writeup-grid">
        <?php foreach ($featured_writeups as $wu): ?>
            <?php \App\Core\View::partial('partials/writeup-card', ['writeup' => $wu]); ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (($completed_labs ?? []) !== []): ?>
<section class="card portfolio-section">
    <h2>Completed Labs</h2>
    <ul class="result-list">
        <?php foreach ($completed_labs as $lab): ?>
            <li>
                <a href="<?= e(url('/labs/' . $lab['slug'])) ?>"><?= e((string) $lab['title']) ?></a>
                <span class="pill"><?= (int) ($lab['score'] ?? 0) ?> pts</span>
                <div class="muted small"><?= e((string) ($lab['category_name'] ?? '')) ?> · <?= e(time_ago((string) $lab['completed_at'])) ?></div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<?php if (!empty($sections['challenges']) && $challenge_stats !== []): ?>
<section class="card portfolio-section">
    <h2>Challenge Stats</h2>
    <ul class="portfolio-stat-list">
        <?php foreach ($challenge_stats as $stat): ?>
            <li><strong><?= e((string) $stat['name']) ?></strong> — <?= (int) $stat['solved'] ?> solved</li>
        <?php endforeach; ?>
    </ul>
</section>
<?php endif; ?>

<?php if (!empty($sections['community']) && $community_stats !== []): ?>
<section class="card portfolio-section">
    <h2>Community</h2>
    <div class="arena-stats portfolio-community-stats">
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) ($community_stats['best_answers'] ?? 0) ?></span>
            <span class="arena-stat-label">Best Answers</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) ($community_stats['discussions'] ?? 0) ?></span>
            <span class="arena-stat-label">Discussions</span>
        </div>
        <div class="arena-stat">
            <span class="arena-stat-value"><?= (int) ($community_stats['replies'] ?? 0) ?></span>
            <span class="arena-stat-label">Replies</span>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($sections['education']) && $education !== []): ?>
<section class="card portfolio-section">
    <h2>Education</h2>
    <?php foreach ($education as $edu): ?>
        <div class="portfolio-timeline-item">
            <strong><?= e((string) $edu['institution']) ?></strong>
            <?php if (!empty($edu['program'])): ?>
                <span class="muted"> — <?= e((string) $edu['program']) ?></span>
            <?php endif; ?>
            <?php if (!empty($edu['start_year']) || !empty($edu['end_year'])): ?>
                <div class="muted small">
                    <?= e((string) ($edu['start_year'] ?? '')) ?><?= !empty($edu['end_year']) ? ' – ' . e((string) $edu['end_year']) : '' ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($edu['description'])): ?>
                <p class="muted"><?= e((string) $edu['description']) ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if (!empty($sections['experience']) && $experience !== []): ?>
<section class="card portfolio-section">
    <h2>Experience</h2>
    <?php foreach ($experience as $exp): ?>
        <div class="portfolio-timeline-item">
            <strong><?= e((string) $exp['role']) ?></strong>
            <span class="muted"> @ <?= e((string) $exp['organization']) ?></span>
            <div class="muted small">
                <?= e((string) ($exp['start_date'] ?? '')) ?>
                <?= !empty($exp['is_current']) ? ' – Present' : (!empty($exp['end_date']) ? ' – ' . e((string) $exp['end_date']) : '') ?>
            </div>
            <?php if (!empty($exp['description'])): ?>
                <p class="muted"><?= e((string) $exp['description']) ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if (!empty($sections['certifications']) && $certifications !== []): ?>
<section class="card portfolio-section">
    <h2>Certifications</h2>
    <?php foreach ($certifications as $cert): ?>
        <div class="portfolio-timeline-item">
            <strong><?= e((string) $cert['name']) ?></strong>
            <?php if (empty($cert['is_verified'])): ?>
                <span class="pill user-provided">User-provided</span>
            <?php else: ?>
                <span class="pill verified">Verified</span>
            <?php endif; ?>
            <?php if (!empty($cert['issuer'])): ?>
                <div class="muted small"><?= e((string) $cert['issuer']) ?></div>
            <?php endif; ?>
            <?php if (!empty($cert['credential_url'])): ?>
                <a href="<?= e((string) $cert['credential_url']) ?>" rel="noopener noreferrer" target="_blank">Credential</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if (!empty($sections['links']) && (
    !empty($portfolio['github_url']) || !empty($portfolio['linkedin_url'])
    || !empty($portfolio['website_url']) || !empty($portfolio['resume_url'])
)): ?>
<section class="card portfolio-section">
    <h2>Links</h2>
    <div class="hero-actions">
        <?php if (!empty($portfolio['github_url'])): ?>
            <a class="btn" href="<?= e((string) $portfolio['github_url']) ?>" rel="noopener noreferrer" target="_blank">GitHub</a>
        <?php endif; ?>
        <?php if (!empty($portfolio['linkedin_url'])): ?>
            <a class="btn" href="<?= e((string) $portfolio['linkedin_url']) ?>" rel="noopener noreferrer" target="_blank">LinkedIn</a>
        <?php endif; ?>
        <?php if (!empty($portfolio['website_url'])): ?>
            <a class="btn" href="<?= e((string) $portfolio['website_url']) ?>" rel="noopener noreferrer" target="_blank">Website</a>
        <?php endif; ?>
        <?php if (!empty($portfolio['resume_url'])): ?>
            <a class="btn" href="<?= e((string) $portfolio['resume_url']) ?>" rel="noopener noreferrer" target="_blank">External Resume</a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
