<?php
/**
 * @var list<array<string,mixed>> $pendingMentors
 * @var list<array<string,mixed>> $groups
 * @var bool $canVerify
 * @var bool $canManagePlatform
 */
\App\Core\View::partial('admin/partials/nav');
?>
<section class="page-header">
    <h1>Collaboration Admin</h1>
    <p class="muted">Verify mentors and moderate groups.</p>
</section>

<?php if ($canVerify && $pendingMentors !== []): ?>
<div class="card">
    <h2>Pending mentor verification</h2>
    <ul class="result-list">
        <?php foreach ($pendingMentors as $m): ?>
            <li>
                <strong>@<?= e((string) $m['username']) ?></strong>
                <?php if (!empty($m['full_name'])): ?> · <?= e((string) $m['full_name']) ?><?php endif; ?>
                <form method="post" action="<?= e(url('/admin/mentors/' . (int) $m['id'] . '/verify')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="text" name="note" placeholder="Note (optional)" maxlength="500">
                    <button class="btn btn-sm btn-primary" type="submit">Verify</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php elseif ($canVerify): ?>
<div class="card"><p class="muted">No mentors pending verification.</p></div>
<?php endif; ?>

<?php if ($canManagePlatform): ?>
<div class="card">
    <h2>Groups</h2>
    <?php if ($groups === []): ?>
        <p class="muted">No groups.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Owner</th>
                    <th>Members</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $g): ?>
                    <tr>
                        <td><a href="<?= e(url('/groups/' . $g['slug'])) ?>"><?= e((string) $g['name']) ?></a></td>
                        <td><?= e((string) $g['group_type']) ?></td>
                        <td>@<?= e((string) ($g['owner_username'] ?? '')) ?></td>
                        <td><?= (int) ($g['member_count'] ?? 0) ?></td>
                        <td><span class="pill"><?= e((string) $g['status']) ?></span></td>
                        <td>
                            <?php if (($g['status'] ?? '') === 'active'): ?>
                                <form method="post" action="<?= e(url('/admin/groups/' . (int) $g['id'] . '/suspend')) ?>" class="inline-form">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm" type="submit" onclick="return confirm('Suspend this group?')">Suspend</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php endif; ?>
