<?php
/**
 * @var list<array<string, mixed>> $pending
 */
?>
<section class="page-header">
    <div>
        <h1>Knowledge Review Queue</h1>
        <p class="muted">Articles awaiting instructor or moderator approval.</p>
    </div>
</section>

<section class="card">
    <?php if ($pending === []): ?>
        <p class="muted">No articles pending review.</p>
    <?php else: ?>
        <?php foreach ($pending as $item): ?>
            <article class="knowledge-review-item">
                <header>
                    <h2><?= e((string) $item['title']) ?></h2>
                    <p class="muted">by @<?= e((string) $item['username']) ?> · updated <?= e(time_ago((string) $item['updated_at'])) ?></p>
                </header>
                <div class="hero-actions">
                    <a class="btn" href="<?= e(url('/knowledge/edit/' . (int) $item['id'])) ?>">Review Content</a>
                </div>
                <form method="post" action="<?= e(url('/admin/knowledge/' . (int) $item['id'] . '/review')) ?>" class="knowledge-review-form">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="note-<?= (int) $item['id'] ?>">Review Note</label>
                        <textarea id="note-<?= (int) $item['id'] ?>" name="review_note" rows="2" maxlength="2000" placeholder="Optional feedback for the author"></textarea>
                    </div>
                    <div class="hero-actions">
                        <button class="btn btn-primary" type="submit" name="status" value="approved">Approve</button>
                        <button class="btn" type="submit" name="status" value="changes_requested">Request Changes</button>
                        <button class="btn btn-danger" type="submit" name="status" value="rejected">Reject</button>
                    </div>
                </form>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
