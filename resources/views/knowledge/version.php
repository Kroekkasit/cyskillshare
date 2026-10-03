<?php
/**
 * @var array<string, mixed> $article
 * @var array<string, mixed> $version
 * @var string $html
 * @var list<array{level:int,id:string,text:string}> $toc
 */
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/knowledge')) ?>">Knowledge Base</a>
    <span>/</span>
    <a href="<?= e(url('/knowledge/' . (int) $article['id'] . '/history')) ?>">History</a>
    <span>/</span>
    <span>v<?= (int) $version['version'] ?></span>
</nav>

<section class="page-header">
    <div>
        <h1><?= e((string) $version['title']) ?></h1>
        <p class="muted">Version <?= (int) $version['version'] ?> · <?= e((string) ($version['change_summary'] ?? '')) ?></p>
    </div>
</section>

<div class="writeup-layout">
    <?php \App\Core\View::partial('partials/toc', ['toc' => $toc]); ?>
    <div class="writeup-body card">
        <div class="content-body markdown-body"><?= $html ?></div>
    </div>
</div>

<div class="hero-actions">
    <a class="btn" href="<?= e(url('/knowledge/' . (int) $article['id'] . '/history')) ?>">← Back to History</a>
    <a class="btn" href="<?= e(url('/knowledge/article/' . $article['slug'])) ?>">Current Version</a>
</div>
