<?php
/** @var array{id:int,name:string,slug:string} $tag */
?>
<a class="tag-pill" href="<?= e(url('/tag/' . $tag['slug'])) ?>">#<?= e($tag['slug']) ?></a>
