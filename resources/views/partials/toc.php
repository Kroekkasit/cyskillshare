<?php
/**
 * @var list<array{level:int,id:string,text:string}> $toc
 * @var string|null $tocId
 */
$tocId = $tocId ?? 'article-toc';
if ($toc === []): ?>
    <?php return; ?>
<?php endif; ?>
<nav class="writeup-toc" id="<?= e($tocId) ?>" aria-label="Table of contents">
    <h2 class="writeup-toc-title">Contents</h2>
    <button type="button" class="writeup-toc-toggle btn btn-sm" aria-expanded="false" aria-controls="<?= e($tocId) ?>-list">
        Show contents
    </button>
    <ol class="writeup-toc-list" id="<?= e($tocId) ?>-list">
        <?php foreach ($toc as $item): ?>
            <li class="toc-level-<?= (int) $item['level'] ?>">
                <a href="#<?= e($item['id']) ?>"><?= e($item['text']) ?></a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
