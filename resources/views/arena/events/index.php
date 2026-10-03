<?php
/**
 * @var list<\App\Models\ArenaEvent> $events
 */
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>Events</h1>
        <p class="muted">Practice sessions, CTFs, competitions, and workshops.</p>
    </div>
</section>

<?php if ($events === []): ?>
    <div class="empty-state card">
        <h2>No events scheduled</h2>
        <p class="muted">Check back later for upcoming Cyber Arena events.</p>
    </div>
<?php else: ?>
    <div class="challenge-grid">
        <?php foreach ($events as $event): ?>
            <article class="card challenge-card">
                <header class="challenge-card-head">
                    <h3><a href="<?= e(url('/arena/events/' . $event->id)) ?>"><?= e($event->name) ?></a></h3>
                    <span class="pill"><?= e(ucfirst($event->status)) ?></span>
                </header>
                <p class="muted"><?= e(ucfirst($event->event_type)) ?> · <?= e(ucfirst($event->visibility)) ?></p>
                <p class="muted small">
                    <?= e(date('M j, Y', strtotime($event->start_at))) ?>
                    — <?= e(date('M j, Y', strtotime($event->end_at))) ?>
                </p>
                <p class="challenge-card-excerpt muted"><?= e(mb_strimwidth(strip_tags($event->description), 0, 140, '…')) ?></p>
                <a class="btn btn-primary" href="<?= e(url('/arena/events/' . $event->id)) ?>">View Event</a>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
