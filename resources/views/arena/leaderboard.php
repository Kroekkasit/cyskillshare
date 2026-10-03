<?php
/**
 * @var string $period
 * @var list<array{rank:int,username:string,solved:int,points:int}> $leaderboard
 */
$periods = [
    'all' => 'All Time',
    'month' => 'This Month',
    'semester' => 'This Semester',
];
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>Leaderboard</h1>
        <p class="muted">Top Arena performers ranked by points, solves, and earliest achievement.</p>
    </div>
</section>

<div class="filter-bar" role="navigation" aria-label="Leaderboard period">
    <?php foreach ($periods as $key => $label): ?>
        <a class="filter-chip<?= $period === $key ? ' is-active' : '' ?>"
           href="<?= e(url('/arena/leaderboard?period=' . $key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</div>

<?php if ($leaderboard === []): ?>
    <div class="empty-state card">
        <h2>No rankings yet</h2>
        <p class="muted">Be the first to solve a challenge and claim the top spot!</p>
        <a class="btn btn-primary" href="<?= e(url('/arena/challenges')) ?>">Start Solving</a>
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
