<?php
/**
 * @var \App\Models\ArenaEvent $event
 * @var list<array<string, mixed>> $challenges
 * @var list<array{rank:int,username:string,solved:int,points:int}> $leaderboard
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/arena')) ?>">Cyber Arena</a>
    <span>/</span>
    <a href="<?= e(url('/arena/events')) ?>">Events</a>
</nav>

<article class="card">
    <header class="challenge-detail-head">
        <h1><?= e($event->name) ?></h1>
        <div class="challenge-detail-meta">
            <span class="pill"><?= e(ucfirst($event->status)) ?></span>
            <span class="pill"><?= e(ucfirst($event->event_type)) ?></span>
        </div>
    </header>
    <p class="muted">
        <?= e(date('M j, Y g:i A', strtotime($event->start_at))) ?>
        — <?= e(date('M j, Y g:i A', strtotime($event->end_at))) ?>
    </p>
    <div class="content-body"><?= markdown($event->description) ?></div>
</article>

<section class="arena-section">
    <h2>Event Challenges</h2>
    <?php if ($challenges === []): ?>
        <div class="empty-state card">
            <p class="muted">No challenges assigned to this event yet.</p>
        </div>
    <?php else: ?>
        <div class="challenge-grid">
            <?php foreach ($challenges as $item): ?>
                <?php \App\Core\View::partial('arena/partials/challenge-card', ['challenge' => $item]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="arena-section">
    <h2>Event Leaderboard</h2>
    <?php if ($leaderboard === []): ?>
        <div class="empty-state card">
            <p class="muted">No solves recorded for this event yet.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th scope="col">Rank</th>
                        <th scope="col">Player</th>
                        <th scope="col">Solved</th>
                        <th scope="col">Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaderboard as $row): ?>
                        <tr>
                            <td class="rank-cell">#<?= (int) $row['rank'] ?></td>
                            <td><a href="<?= e(url('/profile/' . $row['username'])) ?>"><?= e($row['username']) ?></a></td>
                            <td><?= (int) $row['solved'] ?></td>
                            <td><?= (int) $row['points'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
