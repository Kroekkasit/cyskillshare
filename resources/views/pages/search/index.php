<?php
/**
 * @var string $q
 * @var array{threads: list<array<string, mixed>>, users: list<array<string, mixed>>, tags: list<array<string, mixed>>, projects: list<array<string, mixed>>, writeups: list<array<string, mixed>>, knowledge: list<array<string, mixed>>, labs: list<array<string, mixed>>} $results
 */
?>
<section class="page-header">
    <div>
        <h1>Search</h1>
        <p class="muted">Find discussions, writeups, knowledge, labs, projects, people, and tags.</p>
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
<?php elseif ($results['threads'] === [] && $results['users'] === [] && $results['tags'] === [] && ($results['projects'] ?? []) === [] && ($results['writeups'] ?? []) === [] && ($results['knowledge'] ?? []) === [] && ($results['labs'] ?? []) === []): ?>
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

    <?php if (($results['projects'] ?? []) !== []): ?>
    <div class="card">
        <h2>Projects</h2>
        <ul class="result-list">
            <?php foreach ($results['projects'] as $p): ?>
                <li>
                    <a href="<?= e(url('/projects/' . $p['username'] . '/' . $p['slug'])) ?>"><?= e((string) $p['title']) ?></a>
                    <div class="muted small">@<?= e((string) $p['username']) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (($results['writeups'] ?? []) !== []): ?>
    <div class="card">
        <h2>Writeups</h2>
        <ul class="result-list">
            <?php foreach ($results['writeups'] as $w): ?>
                <li>
                    <a href="<?= e(url('/writeups/' . $w['username'] . '/' . $w['slug'])) ?>"><?= e((string) $w['title']) ?></a>
                    <div class="muted small">@<?= e((string) $w['username']) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (($results['labs'] ?? []) !== []): ?>
    <div class="card">
        <h2>Cyber Labs</h2>
        <ul class="result-list">
            <?php foreach ($results['labs'] as $lab): ?>
                <li>
                    <a href="<?= e(url('/labs/' . $lab['slug'])) ?>"><?= e((string) $lab['title']) ?></a>
                    <div class="muted small"><?= e((string) ($lab['category_name'] ?? '')) ?> · <?= e((string) ($lab['difficulty'] ?? '')) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (($results['knowledge'] ?? []) !== []): ?>
    <div class="card">
        <h2>Knowledge Articles</h2>
        <ul class="result-list">
            <?php foreach ($results['knowledge'] as $a): ?>
                <li>
                    <a href="<?= e(url('/knowledge/article/' . $a['slug'])) ?>"><?= e((string) $a['title']) ?></a>
                    <div class="muted small">@<?= e((string) $a['username']) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

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
