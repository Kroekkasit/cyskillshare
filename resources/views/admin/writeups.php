<?php
/**
 * @var list<array<string, mixed>> $writeups
 */
?>
<section class="page-header">
    <div>
        <h1>Writeup Admin</h1>
        <p class="muted">Manage featured writeups and review recent submissions.</p>
    </div>
</section>

<section class="card">
    <?php if ($writeups === []): ?>
        <p class="muted">No writeups yet.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Status</th>
                    <th scope="col">Updated</th>
                    <th scope="col">Feature</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($writeups as $w): ?>
                    <tr>
                        <td>
                            <?php if (($w['status'] ?? '') === 'published'): ?>
                                <a href="<?= e(url('/writeups/' . $w['username'] . '/' . $w['slug'])) ?>"><?= e((string) $w['title']) ?></a>
                            <?php else: ?>
                                <?= e((string) $w['title']) ?>
                            <?php endif; ?>
                            <?php if (!empty($w['featured'])): ?><span class="pill featured">★</span><?php endif; ?>
                        </td>
                        <td><a href="<?= e(url('/portfolio/' . $w['username'])) ?>">@<?= e((string) $w['username']) ?></a></td>
                        <td><span class="pill"><?= e((string) $w['status']) ?></span></td>
                        <td class="muted"><?= e(time_ago((string) $w['updated_at'])) ?></td>
                        <td>
                            <form method="post" action="<?= e(url('/admin/writeups/' . (int) $w['id'] . '/feature')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <input type="hidden" name="featured" value="<?= !empty($w['featured']) ? '0' : '1' ?>">
                                <button class="btn btn-sm" type="submit"><?= !empty($w['featured']) ? 'Unfeature' : 'Feature' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
