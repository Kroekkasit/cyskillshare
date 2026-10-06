<?php
/**
 * @var list<array{category: array<string, mixed>, skills: list<array<string, mixed>>}> $grouped
 * @var bool $canRecalculate
 */
?>
<?php \App\Core\View::partial('admin/partials/nav'); ?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>Manage Skills</h1>
        <p class="muted">Define skills, categories, requirements, and prerequisites.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/admin/skills/create')) ?>">+ New Skill</a>
        <?php if ($canRecalculate): ?>
            <a class="btn" href="<?= e(url('/admin/skills/recalculate')) ?>">Recalculate Progress</a>
        <?php endif; ?>
    </div>
</section>

<?php if ($grouped === []): ?>
    <div class="empty-state card">
        <h2>No categories yet</h2>
        <p class="muted">Add skill categories in the database, then create skills.</p>
    </div>
<?php else: ?>
    <?php foreach ($grouped as $group): ?>
        <div class="card skill-admin-category">
            <h2><?= e($group['category']['name']) ?></h2>
            <?php if (!empty($group['category']['description'])): ?>
                <p class="muted"><?= e($group['category']['description']) ?></p>
            <?php endif; ?>

            <?php if ($group['skills'] === []): ?>
                <p class="muted">No skills in this category.</p>
            <?php else: ?>
                <table class="leaderboard-table admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Order</th>
                            <th scope="col">Active</th>
                            <th scope="col">Gated</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($group['skills'] as $skill): ?>
                            <tr>
                                <td><?= e($skill['name']) ?></td>
                                <td class="muted"><?= e($skill['slug']) ?></td>
                                <td><?= (int) $skill['display_order'] ?></td>
                                <td><?= !empty($skill['is_active']) ? 'Yes' : 'No' ?></td>
                                <td><?= !empty($skill['is_gated']) ? 'Yes' : 'No' ?></td>
                                <td>
                                    <a class="btn btn-sm" href="<?= e(url('/admin/skills/' . (int) $skill['id'] . '/edit')) ?>">Edit</a>
                                    <a class="btn btn-sm" href="<?= e(url('/admin/skills/' . (int) $skill['id'] . '/requirements')) ?>">Requirements</a>
                                    <a class="btn btn-sm" href="<?= e(url('/skills/' . $skill['slug'])) ?>">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
