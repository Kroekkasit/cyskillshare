<?php
/**
 * @var array<string, list<array<string, mixed>>> $channelsGrouped
 * @var string|null $activeChannel
 */
?>
<aside class="channel-sidebar" id="channel-drawer" aria-label="Community channels">
    <div class="channel-sidebar-head">
        <strong>COMMUNITY</strong>
        <a class="btn btn-primary btn-sm" href="<?= e(url('/community/new')) ?>">+ New</a>
    </div>
    <div class="channel-search-wrap">
        <form action="<?= e(url('/search')) ?>" method="get" role="search">
            <input type="search" name="q" placeholder="Search discussions…" aria-label="Search" value="">
        </form>
    </div>
    <nav class="channel-nav">
        <a class="channel-link<?= $activeChannel === null && request_path() === '/community' ? ' is-active' : '' ?>"
           href="<?= e(url('/community')) ?>">All discussions</a>
        <?php foreach ($channelsGrouped as $categoryName => $channels): ?>
            <div class="channel-group">
                <div class="channel-group-title"><?= e(strtoupper($categoryName)) ?></div>
                <?php foreach ($channels as $ch): ?>
                    <a class="channel-link<?= ($activeChannel ?? null) === $ch['slug'] ? ' is-active' : '' ?>"
                       href="<?= e(url('/community/' . $ch['slug'])) ?>">
                        <span class="hash">#</span>
                        <span><?= e(ltrim((string) $ch['name'], '#')) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </nav>
</aside>
