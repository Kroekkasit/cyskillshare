<?php
/**
 * @var list<array<string, mixed>> $labs
 */
?>
<section class="page-header">
    <div>
        <h1>Manage Labs</h1>
        <p class="muted">Create, edit, publish, and feature Cyber Labs.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/admin/labs/create')) ?>">+ New Lab</a>
        <form method="post" action="<?= e(url('/admin/labs/sweep')) ?>" class="inline-form">
            <?= csrf_field() ?>
            <button class="btn" type="submit">Sweep Expired</button>
        </form>
    </div>
</section>

<?php if ($labs === []): ?>
    <div class="empty-state card">
        <h2>No labs yet</h2>
        <a class="btn btn-primary" href="<?= e(url('/admin/labs/create')) ?>">+ New Lab</a>
    </div>
<?php else: ?>
    <div class="card">
        <table class="leaderboard-table admin-table">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Status</th>
                    <th scope="col">Difficulty</th>
                    <th scope="col">Starts</th>
                    <th scope="col">Completions</th>
                    <th scope="col">Author</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($labs as $row): ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/labs/' . $row['slug'])) ?>"><?= e((string) $row['title']) ?></a>
                            <?php if (!empty($row['featured'])): ?>
                                <span class="pill featured">★</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(ucfirst((string) ($row['status'] ?? ''))) ?></td>
                        <td><span class="difficulty-badge difficulty-<?= e((string) ($row['difficulty'] ?? 'beginner')) ?>"><?= e(ucfirst((string) ($row['difficulty'] ?? ''))) ?></span></td>
                        <td><?= (int) ($row['start_count'] ?? 0) ?></td>
                        <td><?= (int) ($row['completion_count'] ?? 0) ?></td>
                        <td><?= e((string) ($row['username'] ?? '')) ?></td>
                        <td>
                            <a class="btn btn-sm" href="<?= e(url('/admin/labs/' . (int) $row['id'] . '/edit')) ?>">Edit</a>
                            <a class="btn btn-sm" href="<?= e(url('/admin/labs/' . (int) $row['id'] . '/tasks')) ?>">Tasks</a>
                            <form method="post" action="<?= e(url('/admin/labs/' . (int) $row['id'] . '/feature')) ?>" class="inline-form">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm" type="submit"><?= !empty($row['featured']) ? 'Unfeature' : 'Feature' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
