<?php
/**
 * @var list<array<string, mixed>> $challenges
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>Manage Challenges</h1>
        <p class="muted">Create, edit, publish, and archive Cyber Arena challenges.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/arena/admin/challenges/new')) ?>">+ New Challenge</a>
        <a class="btn" href="<?= e(url('/arena/admin/events')) ?>">Manage Events</a>
    </div>
</section>

<?php if ($challenges === []): ?>
    <div class="empty-state card">
        <h2>No challenges yet</h2>
        <p class="muted">Create your first challenge to get started.</p>
        <a class="btn btn-primary" href="<?= e(url('/arena/admin/challenges/new')) ?>">+ New Challenge</a>
    </div>
<?php else: ?>
    <div class="card">
        <table class="leaderboard-table admin-table">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Category</th>
                    <th scope="col">Difficulty</th>
                    <th scope="col">Points</th>
                    <th scope="col">Status</th>
                    <th scope="col">Solves</th>
                    <th scope="col">Author</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($challenges as $row): ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/arena/challenges/' . (int) $row['id'])) ?>"><?= e($row['title']) ?></a>
                            <?php if (!empty($row['is_featured'])): ?>
                                <span class="pill featured">★</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($row['category_name'] ?? '') ?></td>
                        <td><span class="difficulty-badge difficulty-<?= e($row['difficulty'] ?? 'easy') ?>"><?= e(ucfirst($row['difficulty'] ?? '')) ?></span></td>
                        <td><?= (int) ($row['points'] ?? 0) ?></td>
                        <td><?= e(ucfirst($row['status'] ?? '')) ?></td>
                        <td><?= (int) ($row['solve_count'] ?? 0) ?></td>
                        <td><?= e($row['author_name'] ?? '') ?></td>
                        <td>
                            <a class="btn" href="<?= e(url('/arena/admin/challenges/' . (int) $row['id'] . '/edit')) ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
