<?php
/** @var list<array<string,mixed>> $items */
use App\Core\Auth;
?>
<section class="page-header">
    <div>
        <h1>Recruitment</h1>
        <p class="muted">Open posts from students looking for teammates and collaborators.</p>
    </div>
    <?php if (Auth::check()): ?>
        <a class="btn btn-primary" href="<?= e(url('/recruitment/create')) ?>">+ Post recruitment</a>
    <?php endif; ?>
</section>

<?php if ($items === []): ?>
<div class="card"><p class="muted">No open recruitment posts.</p></div>
<?php else: ?>
<div class="recruitment-list">
    <?php foreach ($items as $r): ?>
        <article class="card recruitment-card">
            <h3><?= e((string) $r['title']) ?></h3>
            <p class="muted">
                @<?= e((string) $r['username']) ?>
                · Looking for: <?= e(ucfirst((string) ($r['looking_for'] ?? 'general'))) ?>
                <?php if (!empty($r['group_name'])): ?>
                    · Group: <a href="<?= e(url('/groups/' . ($r['group_slug'] ?? ''))) ?>"><?= e((string) $r['group_name']) ?></a>
                <?php endif; ?>
            </p>
            <p><?= nl2br(e(mb_strimwidth((string) $r['description'], 0, 300, '…'))) ?></p>
            <?php if (Auth::check() && (int) Auth::id() !== (int) $r['creator_id']): ?>
                <form method="post" action="<?= e(url('/recruitment/' . (int) $r['id'] . '/apply')) ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="message-<?= (int) $r['id'] ?>">Application message</label>
                        <textarea id="message-<?= (int) $r['id'] ?>" name="message" rows="2" maxlength="2000"></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                </form>
            <?php elseif (!Auth::check()): ?>
                <a class="btn btn-sm" href="<?= e(url('/login')) ?>">Login to apply</a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
