<?php
/**
 * @var array<string, mixed> $thread
 */
$tags = $thread['tags'] ?? [];
$status = (string) ($thread['status'] ?? 'open');
?>
<article class="thread-card">
    <div class="thread-card-main">
        <div class="thread-card-meta">
            <span class="avatar sm"><?= e(strtoupper(substr((string) $thread['username'], 0, 1))) ?></span>
            <div>
                <a class="author" href="<?= e(url('/profile/' . $thread['username'])) ?>"><?= e((string) $thread['username']) ?></a>
                <div class="muted small">
                    <?php if (!empty($thread['program'])): ?><?= e((string) $thread['program']) ?><?php endif; ?>
                    <?php if (!empty($thread['year_level'])): ?> · Year <?= (int) $thread['year_level'] ?><?php endif; ?>
                    · <?= e(time_ago((string) $thread['created_at'])) ?>
                    · <a href="<?= e(url('/community/' . $thread['channel_slug'])) ?>"><?= e((string) $thread['channel_name']) ?></a>
                </div>
            </div>
        </div>

        <h3 class="thread-card-title">
            <?php if (!empty($thread['is_pinned'])): ?><span class="pill pin" title="Pinned">📌 Pinned</span><?php endif; ?>
            <?php if (!empty($thread['is_locked'])): ?><span class="pill lock" title="Locked">🔒</span><?php endif; ?>
            <a href="<?= e(url('/thread/' . $thread['id'])) ?>"><?= e((string) $thread['title']) ?></a>
        </h3>

        <p class="thread-card-excerpt"><?= e(excerpt((string) $thread['content'])) ?></p>

        <?php if ($tags !== []): ?>
            <div class="tag-row">
                <?php foreach ($tags as $tag): ?>
                    <?php \App\Core\View::partial('components/tag', ['tag' => $tag]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="thread-card-stats">
        <span title="Score">↑ <?= (int) ($thread['score'] ?? 0) ?></span>
        <span title="Replies">💬 <?= (int) ($thread['reply_count'] ?? 0) ?></span>
        <?php if ($status === 'solved'): ?>
            <span class="pill solved">✓ Solved</span>
        <?php endif; ?>
    </div>
</article>
