<?php
/**
 * @var array<string, mixed> $portfolio
 * @var array<string, bool> $sections
 * @var list<int> $featuredSkillIds
 * @var list<array<string, mixed>> $userSkills
 * @var list<array<string, mixed>> $education
 * @var list<array<string, mixed>> $experience
 * @var list<array<string, mixed>> $certifications
 * @var array<string, string> $sectionLabels
 */
$maxFeaturedSkills = (int) config('portfolio.max_featured_skills', 6);
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<section class="page-header">
    <div>
        <h1>Portfolio Settings</h1>
        <p class="muted">Configure visibility, sections, and profile details.</p>
    </div>
</section>

<form class="card portfolio-settings-form" method="post" action="<?= e(url('/settings/portfolio')) ?>">
    <?= csrf_field() ?>

    <h2>General</h2>
    <div class="form-group">
        <label><input type="checkbox" name="is_enabled" value="1" <?= !empty($portfolio['is_enabled']) ? 'checked' : '' ?>> Enable portfolio</label>
    </div>
    <div class="form-group">
        <label for="visibility">Visibility</label>
        <select id="visibility" name="visibility">
            <?php foreach (['public', 'community', 'private'] as $v): ?>
                <option value="<?= e($v) ?>" <?= ($portfolio['visibility'] ?? '') === $v ? 'selected' : '' ?>><?= e(ucfirst($v)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="headline">Headline</label>
        <input id="headline" name="headline" type="text" maxlength="200" value="<?= e((string) ($portfolio['headline'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="about">About (Markdown supported)</label>
        <textarea id="about" name="about" rows="6"><?= e((string) ($portfolio['about'] ?? '')) ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="university">University</label>
            <input id="university" name="university" type="text" maxlength="150" value="<?= e((string) ($portfolio['university'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <input id="program" name="program" type="text" maxlength="150" value="<?= e((string) ($portfolio['program'] ?? '')) ?>">
        </div>
        <div class="form-group">
            <label for="graduation_year">Graduation Year</label>
            <input id="graduation_year" name="graduation_year" type="number" min="2000" max="2100" value="<?= e((string) ($portfolio['graduation_year'] ?? '')) ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="location">Location</label>
        <input id="location" name="location" type="text" maxlength="120" value="<?= e((string) ($portfolio['location'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="show_location" value="1" <?= !empty($portfolio['show_location']) ? 'checked' : '' ?>> Show location on portfolio</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="show_email" value="1" <?= !empty($portfolio['show_email']) ? 'checked' : '' ?>> Show email on portfolio</label>
    </div>

    <h2>Links</h2>
    <div class="form-group">
        <label for="github_url">GitHub URL</label>
        <input id="github_url" name="github_url" type="url" value="<?= e((string) ($portfolio['github_url'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="linkedin_url">LinkedIn URL</label>
        <input id="linkedin_url" name="linkedin_url" type="url" value="<?= e((string) ($portfolio['linkedin_url'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="website_url">Website URL</label>
        <input id="website_url" name="website_url" type="url" value="<?= e((string) ($portfolio['website_url'] ?? '')) ?>">
    </div>
    <div class="form-group">
        <label for="resume_url">External Resume URL</label>
        <input id="resume_url" name="resume_url" type="url" value="<?= e((string) ($portfolio['resume_url'] ?? '')) ?>">
    </div>

    <h2>Display Options</h2>
    <div class="form-group">
        <label><input type="checkbox" name="show_challenge_stats" value="1" <?= !empty($portfolio['show_challenge_stats']) ? 'checked' : '' ?>> Show challenge stats</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="show_skill_evidence" value="1" <?= !empty($portfolio['show_skill_evidence']) ? 'checked' : '' ?>> Show skill evidence summary</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="show_community_stats" value="1" <?= !empty($portfolio['show_community_stats']) ? 'checked' : '' ?>> Show community stats</label>
    </div>
    <div class="form-group">
        <label for="featured_project_limit">Featured project limit</label>
        <input id="featured_project_limit" name="featured_project_limit" type="number" min="1" max="6" value="<?= (int) ($portfolio['featured_project_limit'] ?? 3) ?>">
    </div>

    <h2>Visible Sections</h2>
    <div class="portfolio-sections-grid">
        <?php foreach ($sectionLabels as $key => $label): ?>
            <label class="portfolio-section-check">
                <input type="checkbox" name="sections[<?= e($key) ?>]" value="1" <?= !empty($sections[$key]) ? 'checked' : '' ?>>
                <?= e($label) ?>
            </label>
        <?php endforeach; ?>
    </div>

    <h2>Featured Skills <span class="muted small">(max <?= $maxFeaturedSkills ?>)</span></h2>
    <?php if ($userSkills === []): ?>
        <p class="muted">Earn skill evidence first to feature skills on your portfolio.</p>
    <?php else: ?>
        <div class="portfolio-skills-select">
            <?php foreach ($userSkills as $sk): ?>
                <label class="portfolio-section-check">
                    <input type="checkbox" name="skill_ids[]" value="<?= (int) $sk['id'] ?>"
                        <?= in_array((int) $sk['id'], $featuredSkillIds, true) ? 'checked' : '' ?>>
                    <?= e((string) $sk['name']) ?> <span class="muted">(L<?= (int) $sk['current_level'] ?>)</span>
                </label>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <button class="btn btn-primary" type="submit">Save Settings</button>
</form>

<section class="card portfolio-settings-subsection">
    <h2>Education Entries</h2>
    <?php if ($education !== []): ?>
        <ul class="portfolio-entry-list">
            <?php foreach ($education as $edu): ?>
                <li class="portfolio-entry-row">
                    <span><strong><?= e((string) $edu['institution']) ?></strong> — <?= e((string) ($edu['program'] ?? '')) ?></span>
                    <form method="post" action="<?= e(url('/settings/portfolio/education/' . (int) $edu['id'] . '/delete')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm" type="submit">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/settings/portfolio/education')) ?>" class="portfolio-inline-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group"><label>Institution</label><input name="institution" required maxlength="200"></div>
            <div class="form-group"><label>Program</label><input name="program" maxlength="200"></div>
            <div class="form-group"><label>Field</label><input name="field" maxlength="200"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Start Year</label><input name="start_year" type="number" min="1950" max="2100"></div>
            <div class="form-group"><label>End Year</label><input name="end_year" type="number" min="1950" max="2100"></div>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
        <button class="btn btn-primary btn-sm" type="submit">Add Education</button>
    </form>
</section>

<section class="card portfolio-settings-subsection">
    <h2>Experience</h2>
    <?php if ($experience !== []): ?>
        <ul class="portfolio-entry-list">
            <?php foreach ($experience as $exp): ?>
                <li class="portfolio-entry-row">
                    <span><strong><?= e((string) $exp['role']) ?></strong> @ <?= e((string) $exp['organization']) ?></span>
                    <form method="post" action="<?= e(url('/settings/portfolio/experience/' . (int) $exp['id'] . '/delete')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm" type="submit">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/settings/portfolio/experience')) ?>" class="portfolio-inline-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group"><label>Organization</label><input name="organization" required maxlength="200"></div>
            <div class="form-group"><label>Role</label><input name="role" required maxlength="200"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Start Date</label><input name="start_date" type="date"></div>
            <div class="form-group"><label>End Date</label><input name="end_date" type="date"></div>
            <div class="form-group"><label><input type="checkbox" name="is_current" value="1"> Current</label></div>
        </div>
        <div class="form-group">
            <label>Visibility</label>
            <select name="visibility">
                <option value="public">Public</option>
                <option value="community">Community</option>
                <option value="private">Private</option>
            </select>
        </div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
        <button class="btn btn-primary btn-sm" type="submit">Add Experience</button>
    </form>
</section>

<section class="card portfolio-settings-subsection">
    <h2>Certifications <span class="muted small">(user-provided unless verified by staff)</span></h2>
    <?php if ($certifications !== []): ?>
        <ul class="portfolio-entry-list">
            <?php foreach ($certifications as $cert): ?>
                <li class="portfolio-entry-row">
                    <span>
                        <strong><?= e((string) $cert['name']) ?></strong>
                        <?php if (empty($cert['is_verified'])): ?>
                            <span class="pill user-provided">User-provided</span>
                        <?php endif; ?>
                    </span>
                    <form method="post" action="<?= e(url('/settings/portfolio/certifications/' . (int) $cert['id'] . '/delete')) ?>" class="inline-form">
                        <?= csrf_field() ?>
                        <button class="btn btn-sm" type="submit">Remove</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form method="post" action="<?= e(url('/settings/portfolio/certifications')) ?>" class="portfolio-inline-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group"><label>Name</label><input name="name" required maxlength="200"></div>
            <div class="form-group"><label>Issuer</label><input name="issuer" maxlength="200"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Credential ID</label><input name="credential_id" maxlength="120"></div>
            <div class="form-group"><label>Credential URL</label><input name="credential_url" type="url"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Issued</label><input name="issued_date" type="date"></div>
            <div class="form-group"><label>Expires</label><input name="expiration_date" type="date"></div>
        </div>
        <div class="form-group">
            <label>Visibility</label>
            <select name="visibility">
                <option value="public">Public</option>
                <option value="community">Community</option>
                <option value="private">Private</option>
            </select>
        </div>
        <button class="btn btn-primary btn-sm" type="submit">Add Certification</button>
    </form>
</section>
