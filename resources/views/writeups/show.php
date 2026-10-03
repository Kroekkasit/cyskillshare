<?php
/**
 * @var array<string, mixed> $writeup
 * @var \App\Models\User $author
 * @var string $html
 * @var list<array{level:int,id:string,text:string}> $toc
 * @var list<array<string, mixed>> $skills
 * @var list<array<string, mixed>> $tags
 * @var list<array<string, mixed>> $challenges
 * @var list<array<string, mixed>> $projects
 * @var array<string, mixed>|null $category
 * @var bool $is_owner
 * @var array{done:list<string>,missing:list<string>} $suggestions
 */
use App\Core\Auth;

$w = $writeup;
?>
<article class="writeup-detail">
    <header class="card writeup-detail-header">
        <div class="writeup-detail-badges">
            <?php if (!empty($w['featured'])): ?><span class="pill featured">★ Featured</span><?php endif; ?>
            <span class="pill"><?= e((string) $w['difficulty']) ?></span>
            <?php if ($category): ?>
                <a class="pill" href="<?= e(url('/writeups?category=' . $category['slug'])) ?>"><?= e((string) $category['name']) ?></a>
            <?php endif; ?>
        </div>
        <h1><?= e((string) $w['title']) ?></h1>
        <div class="article-meta muted">
            by <a href="<?= e(url('/portfolio/' . $author->username)) ?>">@<?= e($author->username) ?></a>
            · <?= (int) ($w['reading_time'] ?? 0) ?> min read
            · <?= (int) ($w['view_count'] ?? 0) ?> views
            · <?= (int) ($w['helpful_count'] ?? 0) ?> helpful
            <?php if (!empty($w['published_at'])): ?>
                · <?= e(time_ago((string) $w['published_at'])) ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($w['short_description'])): ?>
            <p class="writeup-detail-summary"><?= e((string) $w['short_description']) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <?php if ($is_owner): ?>
                <a class="btn" href="<?= e(url('/writeups/edit/' . (int) $w['id'])) ?>">Edit</a>
            <?php endif; ?>
            <button type="button" class="btn" data-report data-type="writeup" data-id="<?= (int) $w['id'] ?>">Report</button>
        </div>
    </header>

    <div class="writeup-layout">
        <?php \App\Core\View::partial('partials/toc', ['toc' => $toc]); ?>
        <div class="writeup-body card">
            <div class="content-body markdown-body"><?= $html ?></div>

            <?php if ($tags !== []): ?>
            <footer class="writeup-tags">
                <div class="tag-row">
                    <?php foreach ($tags as $tag): ?>
                        <a class="tag-pill" href="<?= e(url('/writeups?tag=' . $tag['slug'])) ?>">#<?= e((string) $tag['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </footer>
            <?php endif; ?>
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

    <?php if ($challenges !== []): ?>
    <section class="card">
        <h2>Related Challenges</h2>
        <ul class="result-list">
            <?php foreach ($challenges as $ch): ?>
                <li>
                    <a href="<?= e(url('/arena/challenges/' . (int) $ch['id'])) ?>"><?= e((string) $ch['title']) ?></a>
                    <span class="muted small"><?= e((string) ($ch['category_name'] ?? '')) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($projects !== []): ?>
    <section class="card">
        <h2>Related Projects</h2>
        <ul class="result-list">
            <?php foreach ($projects as $p): ?>
                <li>
                    <a href="<?= e(url('/projects/' . $p['username'] . '/' . $p['slug'])) ?>"><?= e((string) $p['title']) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if (Auth::check()): ?>
    <section class="card writeup-reactions">
        <h2>Was this helpful?</h2>
        <form method="post" action="<?= e(url('/writeups/' . (int) $w['id'] . '/reaction')) ?>" class="reaction-form">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= e(request_path()) ?>">
            <?php foreach (['helpful' => '👍 Helpful', 'clear' => '💡 Clear', 'practical' => '🔧 Practical'] as $type => $label): ?>
                <button class="btn" type="submit" name="reaction_type" value="<?= e($type) ?>"><?= e($label) ?></button>
            <?php endforeach; ?>
        </form>
    </section>
    <?php endif; ?>

    <?php if ($is_owner): ?>
        <?php \App\Core\View::partial('partials/quality-checklist', ['suggestions' => $suggestions]); ?>
    <?php endif; ?>
</article>
