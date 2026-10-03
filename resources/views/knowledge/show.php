<?php
/**
 * @var array<string, mixed> $article
 * @var \App\Models\User|null $author
 * @var string $html
 * @var list<array{level:int,id:string,text:string}> $toc
 * @var list<array<string, mixed>> $skills
 * @var list<array<string, mixed>> $sources
 * @var list<array<string, mixed>> $contributors
 * @var list<array<string, mixed>> $versions
 * @var array<string, mixed>|null $category
 * @var bool $is_owner
 * @var bool $can_review
 */
use App\Core\Auth;

$a = $article;
?>
<article class="writeup-detail knowledge-article-detail">
    <header class="card writeup-detail-header">
        <div class="writeup-detail-badges">
            <?php if (!empty($a['is_official'])): ?><span class="pill verified">Official</span><?php endif; ?>
            <?php if (!empty($a['featured'])): ?><span class="pill featured">★ Featured</span><?php endif; ?>
            <span class="pill"><?= e((string) $a['difficulty']) ?></span>
            <?php if ($category): ?>
                <a class="pill" href="<?= e(url('/knowledge/category/' . $category['slug'])) ?>"><?= e((string) $category['name']) ?></a>
            <?php endif; ?>
        </div>
        <h1><?= e((string) $a['title']) ?></h1>
        <div class="article-meta muted">
            <?php if ($author): ?>
                by <a href="<?= e(url('/profile/' . $author->username)) ?>">@<?= e($author->username) ?></a>
                ·
            <?php endif; ?>
            v<?= (int) ($a['version'] ?? 1) ?>
            · <?= (int) ($a['view_count'] ?? 0) ?> views
            · <?= (int) ($a['helpful_count'] ?? 0) ?> helpful
            <?php if (!empty($a['published_at'])): ?>
                · <?= e(time_ago((string) $a['published_at'])) ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($a['summary'])): ?>
            <p class="writeup-detail-summary"><?= e((string) $a['summary']) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <?php if ($is_owner || $can_review): ?>
                <a class="btn" href="<?= e(url('/knowledge/edit/' . (int) $a['id'])) ?>">Edit</a>
                <a class="btn" href="<?= e(url('/knowledge/' . (int) $a['id'] . '/history')) ?>">History</a>
            <?php endif; ?>
            <button type="button" class="btn" data-report data-type="knowledge_article" data-id="<?= (int) $a['id'] ?>">Report</button>
        </div>
    </header>

    <div class="writeup-layout">
        <?php \App\Core\View::partial('partials/toc', ['toc' => $toc]); ?>
        <div class="writeup-body card">
            <div class="content-body markdown-body"><?= $html ?></div>
        </div>
    </div>

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

    <?php if ($sources !== []): ?>
    <section class="card">
        <h2>Sources</h2>
        <ul class="result-list">
            <?php foreach ($sources as $src): ?>
                <li>
                    <?php if (!empty($src['url'])): ?>
                        <a href="<?= e((string) $src['url']) ?>" rel="noopener noreferrer" target="_blank"><?= e((string) ($src['title'] ?: $src['url'])) ?></a>
                    <?php else: ?>
                        <?= e((string) ($src['title'] ?? 'Source')) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($contributors !== []): ?>
    <section class="card">
        <h2>Contributors</h2>
        <ul class="result-list">
            <?php foreach ($contributors as $c): ?>
                <li>@<?= e((string) $c['username']) ?> <span class="muted">(<?= e((string) $c['role']) ?>)</span></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (Auth::check()): ?>
    <section class="card writeup-reactions">
        <h2>Was this helpful?</h2>
        <form method="post" action="<?= e(url('/knowledge/' . (int) $a['id'] . '/reaction')) ?>" class="reaction-form">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= e(request_path()) ?>">
            <?php foreach (['helpful' => '👍 Helpful', 'clear' => '💡 Clear', 'practical' => '🔧 Practical'] as $type => $label): ?>
                <button class="btn" type="submit" name="reaction_type" value="<?= e($type) ?>"><?= e($label) ?></button>
            <?php endforeach; ?>
        </form>
    </section>
    <?php endif; ?>
</article>
