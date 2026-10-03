<?php
/**
 * @var \App\Models\Thread $thread
 * @var \App\Models\User|null $author
 * @var \App\Models\Channel|null $channel
 * @var list<array<string, mixed>> $replies
 * @var list<array{id:int,name:string,slug:string}> $tags
 * @var int $score
 * @var string|null $userVote
 * @var bool $bookmarked
 * @var array<int, array<string, mixed>|null> $replyVotes
 * @var bool $canModerate
 * @var bool $canManageThread
 */
use App\Core\Auth;
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/community')) ?>">Community</a>
    <?php if ($channel): ?>
        <span>/</span>
        <a href="<?= e(url('/community/' . $channel->slug)) ?>"><?= e($channel->name) ?></a>
    <?php endif; ?>
</nav>

<?php if ($thread->deleted_at !== null): ?>
    <div class="flash flash-error">This discussion was removed (staff view).</div>
<?php endif; ?>

<?php if ($thread->challenge_id !== null): ?>
    <div class="flash flash-error arena-spoiler-banner" role="alert">
        <strong>⚠ Spoiler Warning</strong> — This discussion is linked to a Cyber Arena challenge and may contain hints or solutions.
        <a href="<?= e(url('/arena/challenges/' . $thread->challenge_id)) ?>">Return to challenge</a>
    </div>
<?php endif; ?>

<article class="card thread-detail">
    <header class="thread-detail-head">
        <h1><?= e($thread->title) ?></h1>
        <div class="thread-card-meta">
            <span class="avatar"><?= e(strtoupper(substr($author?->username ?? '?', 0, 1))) ?></span>
            <div>
                <?php if ($author): ?>
                    <a href="<?= e(url('/profile/' . $author->username)) ?>"><?= e($author->username) ?></a>
                    <div class="muted small">
                        <?= e($author->primaryRoleLabel()) ?>
                        <?php if ($author->year_level): ?> · Year <?= (int) $author->year_level ?><?php endif; ?>
                        · <?= e(time_ago($thread->created_at)) ?>
                        · <?= (int) $thread->views ?> views
                        <?php if ($thread->updated_at !== $thread->created_at): ?> · Edited<?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($thread->status === 'solved'): ?><span class="pill solved">✓ Solved</span><?php endif; ?>
            <?php if ($thread->is_pinned): ?><span class="pill pin">📌 Pinned</span><?php endif; ?>
            <?php if ($thread->is_locked): ?><span class="pill lock">🔒 Locked</span><?php endif; ?>
        </div>
    </header>

    <div class="content-body"><?= markdown($thread->content) ?></div>

    <?php if ($tags !== []): ?>
        <div class="tag-row">
            <?php foreach ($tags as $tag): ?>
                <?php \App\Core\View::partial('components/tag', ['tag' => $tag]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="action-bar">
        <?php if (Auth::check()): ?>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/vote')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="vote_type" value="up">
                <button class="btn<?= $userVote === 'up' ? ' is-active' : '' ?>" type="submit" aria-label="Upvote">↑ <?= (int) $score ?></button>
            </form>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/vote')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="vote_type" value="down">
                <button class="btn<?= $userVote === 'down' ? ' is-active' : '' ?>" type="submit" aria-label="Downvote">↓</button>
            </form>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/bookmark')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn" type="submit"><?= $bookmarked ? '★ Bookmarked' : '☆ Bookmark' ?></button>
            </form>
            <button type="button" class="btn" data-report data-type="thread" data-id="<?= (int) $thread->id ?>">Report</button>
        <?php else: ?>
            <span class="muted">↑ <?= (int) $score ?> · <a href="<?= e(url('/login')) ?>">Login</a> to vote</span>
        <?php endif; ?>

        <?php if ($canManageThread): ?>
            <a class="btn" href="<?= e(url('/thread/' . $thread->id . '/edit')) ?>">Edit</a>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/delete')) ?>" class="inline-form" onsubmit="return confirm('Remove this discussion?');">
                <?= csrf_field() ?>
                <button class="btn btn-danger" type="submit">Delete</button>
            </form>
        <?php endif; ?>

        <?php if ($canModerate): ?>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/pin')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="<?= $thread->is_pinned ? 'unpin' : 'pin' ?>">
                <button class="btn" type="submit"><?= $thread->is_pinned ? 'Unpin' : 'Pin' ?></button>
            </form>
            <form method="post" action="<?= e(url('/thread/' . $thread->id . '/lock')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="<?= $thread->is_locked ? 'unlock' : 'lock' ?>">
                <button class="btn" type="submit"><?= $thread->is_locked ? 'Unlock' : 'Lock' ?></button>
            </form>
        <?php endif; ?>
    </div>
</article>

<?php if ($thread->is_locked): ?>
    <div class="card lock-notice">🔒 This discussion is locked. New replies are disabled.</div>
<?php endif; ?>

<section class="replies-section">
    <h2><?= count(array_filter($replies, static fn($r) => empty($r['deleted_at']))) ?> Replies</h2>

    <?php foreach ($replies as $r): ?>
        <?php
        $isDeleted = !empty($r['deleted_at']);
        $rv = $replyVotes[(int) $r['id']]['vote_type'] ?? null;
        ?>
        <article class="card reply-card<?= !empty($r['is_best_answer']) ? ' is-best' : '' ?>" id="reply-<?= (int) $r['id'] ?>">
            <?php if (!empty($r['is_best_answer'])): ?>
                <div class="pill solved">✓ Best Answer</div>
            <?php endif; ?>

            <div class="thread-card-meta">
                <span class="avatar sm"><?= e(strtoupper(substr((string) $r['username'], 0, 1))) ?></span>
                <div>
                    <a href="<?= e(url('/profile/' . $r['username'])) ?>"><?= e((string) $r['username']) ?></a>
                    <div class="muted small"><?= e(time_ago((string) $r['created_at'])) ?></div>
                </div>
            </div>

            <?php if ($isDeleted): ?>
                <p class="muted">[This reply has been removed.]</p>
            <?php else: ?>
                <div class="content-body"><?= markdown((string) $r['content']) ?></div>
                <div class="action-bar">
                    <?php if (Auth::check()): ?>
                        <form method="post" action="<?= e(url('/reply/' . $r['id'] . '/vote')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="vote_type" value="up">
                            <input type="hidden" name="redirect" value="<?= e('/thread/' . $thread->id) ?>">
                            <button class="btn<?= $rv === 'up' ? ' is-active' : '' ?>" type="submit">↑ <?= (int) ($r['score'] ?? 0) ?></button>
                        </form>
                        <form method="post" action="<?= e(url('/reply/' . $r['id'] . '/vote')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="vote_type" value="down">
                            <input type="hidden" name="redirect" value="<?= e('/thread/' . $thread->id) ?>">
                            <button class="btn<?= $rv === 'down' ? ' is-active' : '' ?>" type="submit">↓</button>
                        </form>
                        <button type="button" class="btn" data-report data-type="reply" data-id="<?= (int) $r['id'] ?>">Report</button>
                    <?php else: ?>
                        <span class="muted">↑ <?= (int) ($r['score'] ?? 0) ?></span>
                    <?php endif; ?>

                    <?php if (Auth::check() && Auth::canManage((int) $r['user_id'])): ?>
                        <details>
                            <summary class="btn">Edit</summary>
                            <form method="post" action="<?= e(url('/reply/' . $r['id'] . '/edit')) ?>" class="edit-reply-form">
                                <?= csrf_field() ?>
                                <textarea name="content" rows="4" required minlength="2"><?= e((string) $r['content']) ?></textarea>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </form>
                        </details>
                        <form method="post" action="<?= e(url('/reply/' . $r['id'] . '/delete')) ?>" class="inline-form" onsubmit="return confirm('Remove this reply?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($canManageThread && empty($r['is_best_answer'])): ?>
                        <form method="post" action="<?= e(url('/thread/' . $thread->id . '/best-answer')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="reply_id" value="<?= (int) $r['id'] ?>">
                            <input type="hidden" name="action" value="mark">
                            <button class="btn btn-primary" type="submit">Mark Best Answer</button>
                        </form>
                    <?php elseif ($canManageThread && !empty($r['is_best_answer'])): ?>
                        <form method="post" action="<?= e(url('/thread/' . $thread->id . '/best-answer')) ?>" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="reply_id" value="<?= (int) $r['id'] ?>">
                            <input type="hidden" name="action" value="unmark">
                            <button class="btn" type="submit">Unmark Best Answer</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>

<?php if (Auth::check() && !$thread->is_locked && $thread->deleted_at === null): ?>
    <div class="card">
        <h2>Write a reply</h2>
        <?php \App\Core\View::partial('components/form-errors'); ?>
        <form method="post" action="<?= e(url('/thread/' . $thread->id . '/reply')) ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="content">Reply</label>
                <textarea id="content" name="content" rows="6" required minlength="2" maxlength="20000"
                          placeholder="Share a helpful answer. Markdown supported. Mention users with @username."></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Post Reply</button>
        </form>
    </div>
<?php elseif (!Auth::check()): ?>
    <p class="muted"><a href="<?= e(url('/login')) ?>">Log in</a> to reply.</p>
<?php endif; ?>
