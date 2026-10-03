<?php
/**
 * @var \App\Models\User $user
 * @var array<string, mixed> $project
 * @var list<string> $technologies
 * @var list<array<string, mixed>> $skills
 * @var list<array<string, mixed>> $images
 * @var array<string, mixed>|null $verification
 * @var list<array<string, mixed>> $challenges
 * @var list<array<string, mixed>> $reactions
 * @var bool $is_owner
 */
use App\Core\Auth;

$verified = !empty($verification) && ($verification['status'] ?? '') === 'verified';
$pendingVerification = !empty($verification) && ($verification['status'] ?? '') === 'pending';
?>
<?php \App\Core\View::partial('portfolio/partials/portfolio-nav'); ?>

<article class="project-detail">
    <header class="card project-detail-header">
        <div class="project-detail-badges">
            <span class="pill"><?= e(str_replace('_', ' ', (string) $project['project_type'])) ?></span>
            <span class="pill"><?= e(str_replace('_', ' ', (string) $project['status'])) ?></span>
            <?php if (!empty($project['featured'])): ?><span class="pill featured">★ Featured</span><?php endif; ?>
            <?php if ($verified): ?><span class="pill verified">✓ Verified</span><?php endif; ?>
            <?php if ($pendingVerification): ?><span class="pill">Verification pending</span><?php endif; ?>
        </div>
        <h1><?= e((string) $project['title']) ?></h1>
        <p class="muted">
            by <a href="<?= e(url('/portfolio/' . $user->username)) ?>">@<?= e($user->username) ?></a>
            · <?= (int) ($project['view_count'] ?? 0) ?> views
        </p>
        <?php if (!empty($project['short_description'])): ?>
            <p class="project-detail-summary"><?= e((string) $project['short_description']) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <?php if (!empty($project['repository_url'])): ?>
                <a class="btn btn-primary" href="<?= e((string) $project['repository_url']) ?>" rel="noopener noreferrer" target="_blank">Repository</a>
            <?php endif; ?>
            <?php if (!empty($project['demo_url'])): ?>
                <a class="btn" href="<?= e((string) $project['demo_url']) ?>" rel="noopener noreferrer" target="_blank">Demo</a>
            <?php endif; ?>
            <?php if (!empty($project['documentation_url'])): ?>
                <a class="btn" href="<?= e((string) $project['documentation_url']) ?>" rel="noopener noreferrer" target="_blank">Docs</a>
            <?php endif; ?>
            <?php if ($is_owner): ?>
                <a class="btn" href="<?= e(url('/projects/edit/' . (int) $project['id'])) ?>">Edit</a>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($images !== []): ?>
    <section class="card project-gallery">
        <h2>Screenshots</h2>
        <div class="project-image-grid">
            <?php foreach ($images as $img): ?>
                <figure class="project-image-item">
                    <img src="<?= e(url('/projects/images/' . (int) $img['id'])) ?>" alt="<?= e((string) ($img['caption'] ?: $img['original_name'])) ?>" loading="lazy">
                    <?php if (!empty($img['caption'])): ?>
                        <figcaption class="muted small"><?= e((string) $img['caption']) ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($project['description'])): ?>
    <section class="card project-detail-body">
        <h2>Description</h2>
        <div class="markdown-body"><?= markdown((string) $project['description']) ?></div>
    </section>
    <?php endif; ?>

    <?php if ($technologies !== []): ?>
    <section class="card">
        <h2>Technologies</h2>
        <div class="tag-row">
            <?php foreach ($technologies as $tech): ?>
                <span class="tag-pill"><?= e((string) $tech) ?></span>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($skills !== []): ?>
    <section class="card">
        <h2>Related Skills</h2>
        <div class="tag-row">
            <?php foreach ($skills as $sk): ?>
                <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($challenges !== []): ?>
    <section class="card">
        <h2>Related Challenges</h2>
        <ul class="portfolio-stat-list">
            <?php foreach ($challenges as $ch): ?>
                <li>
                    <a href="<?= e(url('/arena/challenges/' . (int) $ch['id'])) ?>"><?= e((string) $ch['title']) ?></a>
                    <span class="muted"> · <?= e((string) $ch['difficulty']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($verified && !empty($verification['verifier_username'])): ?>
    <section class="card muted">
        <p>Verified by @<?= e((string) $verification['verifier_username']) ?>
            <?php if (!empty($verification['verified_at'])): ?>
                on <?= e(date('M j, Y', strtotime((string) $verification['verified_at']))) ?>
            <?php endif; ?>
        </p>
        <?php if (!empty($verification['verification_note'])): ?>
            <p><?= e((string) $verification['verification_note']) ?></p>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php if (Auth::check() && !$is_owner): ?>
    <section class="card project-reactions">
        <h2>React</h2>
        <div class="hero-actions">
            <?php foreach (['helpful' => 'Helpful', 'interesting' => 'Interesting', 'impressive' => 'Impressive'] as $type => $label): ?>
                <form method="post" action="<?= e(url('/projects/' . (int) $project['id'] . '/reaction')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="reaction_type" value="<?= e($type) ?>">
                    <input type="hidden" name="redirect" value="<?= e('/projects/' . $user->username . '/' . $project['slug']) ?>">
                    <button class="btn btn-sm" type="submit"><?= e($label) ?></button>
                </form>
            <?php endforeach; ?>
        </div>
        <?php if ($reactions !== []): ?>
            <div class="muted small project-reaction-counts">
                <?php foreach ($reactions as $r): ?>
                    <span><?= e(ucfirst((string) $r['reaction_type'])) ?>: <?= (int) $r['cnt'] ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</article>
