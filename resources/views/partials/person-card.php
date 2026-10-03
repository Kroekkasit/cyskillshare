<?php
/**
 * @var array<string, mixed> $person
 */
use App\Core\Auth;
?>
<article class="person-card card collab-card">
    <header class="person-card-head">
        <div class="avatar"><?= e(strtoupper(substr((string) ($person['username'] ?? '?'), 0, 1))) ?></div>
        <div>
            <h3><a href="<?= e(url('/profile/' . ($person['username'] ?? ''))) ?>">@<?= e((string) ($person['username'] ?? '')) ?></a></h3>
            <?php if (!empty($person['full_name'])): ?>
                <p class="muted small"><?= e((string) $person['full_name']) ?></p>
            <?php endif; ?>
        </div>
    </header>
    <?php if (!empty($person['bio'])): ?>
        <p class="person-card-bio muted"><?= e(mb_strimwidth((string) $person['bio'], 0, 120, '…')) ?></p>
    <?php endif; ?>
    <?php if (!empty($person['skills']) && is_array($person['skills'])): ?>
        <div class="tag-row">
            <?php foreach (array_slice($person['skills'], 0, 4) as $sk): ?>
                <a class="skill-chip tag-pill" href="<?= e(url('/skills/' . $sk['slug'])) ?>"><?= e((string) $sk['name']) ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($person['reasons'])): ?>
        <?php \App\Core\View::partial('partials/reason-list', ['reasons' => $person['reasons']]); ?>
    <?php endif; ?>
    <div class="person-card-actions">
        <?php if (!empty($person['is_mentor'])): ?>
            <a class="btn btn-sm" href="<?= e(url('/mentors/' . ($person['username'] ?? ''))) ?>">Mentor profile</a>
        <?php endif; ?>
        <?php if (Auth::check() && (int) Auth::id() !== (int) ($person['id'] ?? 0)): ?>
            <form method="post" action="<?= e(url('/people/' . ($person['username'] ?? '') . '/block')) ?>" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="<?= e(request_path()) ?>">
                <button class="btn btn-sm" type="submit">Block</button>
            </form>
        <?php endif; ?>
    </div>
</article>
