<?php
/**
 * @var string $q
 * @var array{threads: list<array<string, mixed>>, users: list<array<string, mixed>>, tags: list<array<string, mixed>>} $results
 */
?>
<section class="page-header">
    <div>
        <h1>Search</h1>
        <p class="muted">Find discussions, people, and tags.</p>
    </div>
</section>

<form class="card search-form" method="get" action="<?= e(url('/search')) ?>">
    <div class="form-group">
        <label for="q">Query</label>
        <input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="sql injection, nmap, @username…">
    </div>
    <button class="btn btn-primary" type="submit">Search</button>
</form>

<?php if ($q === ''): ?>
    <p class="muted">Enter a keyword to search.</p>
<?php elseif ($results['threads'] === [] && $results['users'] === [] && $results['tags'] === []): ?>
    <div class="empty-state card">
        <h2>No results found</h2>
        <p class="muted">Try a different keyword or tag.</p>
    </div>
<?php else: ?>
    <div class="card">
        <h2>Discussions</h2>
        <?php if ($results['threads'] === []): ?>
            <p class="muted">No matching discussions.</p>
        <?php else: ?>
            <ul class="result-list">
                <?php foreach ($results['threads'] as $t): ?>
                    <li>
                        <a href="<?= e(url('/thread/' . $t['id'])) ?>"><?= e((string) $t['title']) ?></a>
                        <div class="muted small"><?= e((string) $t['channel_name']) ?> · <?= e((string) $t['username']) ?> · <?= e(time_ago((string) $t['created_at'])) ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Users</h2>
        <?php if ($results['users'] === []): ?>
            <p class="muted">No matching users.</p>
        <?php else: ?>
            <ul class="result-list">
                <?php foreach ($results['users'] as $u): ?>
                    <li>
                        <a href="<?= e(url('/profile/' . $u['username'])) ?>">@<?= e((string) $u['username']) ?></a>
                        <span class="muted"><?= e((string) ($u['full_name'] ?? '')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Tags</h2>
        <?php if ($results['tags'] === []): ?>
            <p class="muted">No matching tags.</p>
        <?php else: ?>
            <div class="tag-row">
                <?php foreach ($results['tags'] as $tag): ?>
                    <a class="tag-pill" href="<?= e(url('/tag/' . $tag['slug'])) ?>">#<?= e((string) $tag['slug']) ?> (<?= (int) $tag['thread_count'] ?>)</a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
