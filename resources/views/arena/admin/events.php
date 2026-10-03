<?php
/**
 * @var list<array<string, mixed>> $events
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>Manage Events</h1>
        <p class="muted">Create and manage Cyber Arena events and competitions.</p>
    </div>
    <div class="hero-actions">
        <a class="btn btn-primary" href="<?= e(url('/arena/admin/events/new')) ?>">+ New Event</a>
        <a class="btn" href="<?= e(url('/arena/admin/challenges')) ?>">Manage Challenges</a>
    </div>
</section>

<?php if ($events === []): ?>
    <div class="empty-state card">
        <h2>No events yet</h2>
        <p class="muted">Create your first event to group challenges together.</p>
        <a class="btn btn-primary" href="<?= e(url('/arena/admin/events/new')) ?>">+ New Event</a>
    </div>
<?php else: ?>
    <div class="card">
        <table class="leaderboard-table admin-table">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Status</th>
                    <th scope="col">Visibility</th>
                    <th scope="col">Start</th>
                    <th scope="col">End</th>
                    <th scope="col">Challenges</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $row): ?>
                    <tr>
                        <td><a href="<?= e(url('/arena/events/' . (int) $row['id'])) ?>"><?= e($row['name']) ?></a></td>
                        <td><?= e(ucfirst($row['event_type'] ?? '')) ?></td>
                        <td><?= e(ucfirst($row['status'] ?? '')) ?></td>
                        <td><?= e(ucfirst($row['visibility'] ?? '')) ?></td>
                        <td><?= e(date('M j, Y', strtotime($row['start_at'] ?? 'now'))) ?></td>
                        <td><?= e(date('M j, Y', strtotime($row['end_at'] ?? 'now'))) ?></td>
                        <td><?= (int) ($row['challenge_count'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
