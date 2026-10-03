<?php
/**
 * @var array<string, mixed> $article
 * @var list<array<string, mixed>> $versions
 */
?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/knowledge')) ?>">Knowledge Base</a>
    <span>/</span>
    <a href="<?= e(url('/knowledge/article/' . $article['slug'])) ?>"><?= e((string) $article['title']) ?></a>
    <span>/</span>
    <span>History</span>
</nav>

<section class="page-header">
    <div>
        <h1>Version History</h1>
        <p class="muted"><?= e((string) $article['title']) ?></p>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/knowledge/edit/' . (int) $article['id'])) ?>">Edit Article</a>
    </div>
</section>

<section class="card">
    <?php if ($versions === []): ?>
        <p class="muted">No version history yet.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Version</th>
                    <th scope="col">Summary</th>
                    <th scope="col">Editor</th>
                    <th scope="col">Date</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($versions as $v): ?>
                    <tr>
                        <td>v<?= (int) $v['version'] ?></td>
                        <td><?= e((string) ($v['change_summary'] ?? '—')) ?></td>
                        <td class="muted">@<?= e((string) ($v['editor_username'] ?? 'unknown')) ?></td>
                        <td class="muted"><?= e(time_ago((string) $v['created_at'])) ?></td>
                        <td>
                            <a class="btn btn-sm" href="<?= e(url('/knowledge/' . (int) $article['id'] . '/version/' . (int) $v['version'])) ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
