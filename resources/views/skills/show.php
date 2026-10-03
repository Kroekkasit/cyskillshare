<?php
/**
 * @var array<string, mixed> $detail
 * @var string $nextLevelName
 * @var bool $showProgress
 */
use App\Core\Auth;
use App\Services\SkillEvidenceService;
use App\Services\SkillTreeService;

/** @var \App\Models\Skill $skill */
$skill = $detail['skill'];
/** @var \App\Models\SkillCategory|null $category */
$category = $detail['category'];
$progress = $detail['progress'];
$currentLevel = (int) ($progress['level'] ?? 0);
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/skills')) ?>">Skill Tree</a>
    <?php if ($category): ?>
        <span>/</span>
        <span><?= e($category->name) ?></span>
    <?php endif; ?>
</nav>

<article class="card skill-detail">
    <header class="skill-detail-head">
        <h1>
            <span class="skill-level-symbol lg"><?= e(SkillTreeService::levelSymbol($currentLevel)) ?></span>
            <?= e($skill->name) ?>
        </h1>
        <?php if (Auth::check()): ?>
            <p class="subtitle">Your level: <strong><?= e($progress['name']) ?></strong></p>
        <?php endif; ?>
        <?php if ($skill->is_gated): ?>
            <p class="skill-gated-note">This skill may require prerequisites before you can demonstrate progress.</p>
        <?php endif; ?>
    </header>

    <div class="content-body">
        <?= nl2br(e($skill->description)) ?>
    </div>

    <?php if ($showProgress): ?>
        <?php \App\Core\View::partial('skills/partials/progress-bar', [
            'progress' => (int) ($progress['progress'] ?? 0),
            'label' => 'Progress toward ' . $nextLevelName,
        ]); ?>
    <?php endif; ?>

    <?php if (!empty($detail['prerequisites'])): ?>
    <section class="skill-section">
        <h2>Prerequisites</h2>
        <ul class="skill-prereq-list">
            <?php foreach ($detail['prerequisites'] as $pre): ?>
                <li class="<?= !empty($pre['met']) ? 'met' : 'unmet' ?>">
                    <a href="<?= e(url('/skills/' . $pre['slug'])) ?>"><?= e($pre['name']) ?></a>
                    <span class="muted">— <?= e(\App\Models\SkillLevel::nameFor((int) $pre['minimum_level'])) ?> or above</span>
                    <?php if (Auth::check()): ?>
                        <span class="pill <?= !empty($pre['met']) ? 'solved' : '' ?>">
                            <?= !empty($pre['met']) ? '✓ Met' : 'Not yet' ?>
                        </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (Auth::check() && !empty($detail['why'])): ?>
    <section class="skill-section">
        <h2>Why this level</h2>
        <ul class="skill-checklist">
            <?php foreach ($detail['why'] as $item): ?>
                <li class="<?= !empty($item['met']) ? 'met' : 'unmet' ?>">
                    <?= !empty($item['met']) ? '✓' : '○' ?>
                    <?= e($item['description']) ?>
                    <span class="muted">(<?= (int) $item['have'] ?>/<?= (int) $item['need'] ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['checklist_by_level'])): ?>
    <section class="skill-section">
        <h2>Requirements by level</h2>
        <?php foreach ($detail['checklist_by_level'] as $lvl => $items): ?>
            <div class="skill-level-reqs">
                <h3><?= e($detail['levels'][$lvl]->name ?? \App\Models\SkillLevel::nameFor((int) $lvl)) ?></h3>
                <ul class="skill-checklist">
                    <?php foreach ($items as $item): ?>
                        <li class="<?= !empty($item['met']) ? 'met' : 'unmet' ?>">
                            <?= !empty($item['met']) ? '✓' : '○' ?>
                            <?= e($item['description']) ?>
                            <?php if (Auth::check()): ?>
                                <span class="muted">(<?= (int) $item['have'] ?>/<?= (int) $item['need'] ?>)</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (Auth::check() && !empty($detail['evidence'])): ?>
    <section class="skill-section">
        <h2>Your evidence</h2>
        <ul class="skill-evidence-list">
            <?php foreach ($detail['evidence'] as $ev): ?>
                <?php
                $sourceUrl = SkillEvidenceService::sourceUrl(
                    (string) $ev['source_type'],
                    isset($ev['source_id']) ? (int) $ev['source_id'] : null
                );
                ?>
                <li>
                    <strong><?= e($ev['title']) ?></strong>
                    <?php if ($sourceUrl): ?>
                        <a href="<?= e(url($sourceUrl)) ?>">View source</a>
                    <?php endif; ?>
                    <div class="muted small"><?= e(time_ago((string) $ev['created_at'])) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
        <a class="btn" href="<?= e(url('/skills/evidence')) ?>">Manage my evidence</a>
    </section>
    <?php elseif (Auth::check()): ?>
    <section class="skill-section">
        <p class="muted">No accepted evidence yet for this skill.</p>
        <a class="btn" href="<?= e(url('/skills/evidence')) ?>">Submit evidence</a>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['next_steps'])): ?>
    <section class="skill-section">
        <h2>Suggested next steps</h2>
        <ul class="skill-next-steps">
            <?php foreach ($detail['next_steps'] as $step): ?>
                <li>
                    <?php if (!empty($step['url'])): ?>
                        <a href="<?= e(url($step['url'])) ?>"><?= e($step['label']) ?></a>
                    <?php else: ?>
                        <?= e($step['label']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (!empty($detail['children'])): ?>
    <section class="skill-section">
        <h2>Related skills</h2>
        <div class="tag-row">
            <?php foreach ($detail['children'] as $child): ?>
                <a class="pill" href="<?= e(url('/skills/' . $child->slug)) ?>"><?= e($child->name) ?></a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</article>
