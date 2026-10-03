<?php
/**
 * @var bool|null $isArena
 */
use App\Core\Auth;
use App\Services\ChallengeService;

$path = request_path();
?>
<nav class="arena-nav" aria-label="Cyber Arena">
    <a class="<?= str_starts_with($path, '/arena/challenges') || str_starts_with($path, '/arena/categories') ? 'is-active' : '' ?>"
       href="<?= e(url('/arena/challenges')) ?>">Challenges</a>
    <a class="<?= str_starts_with($path, '/arena/categories') ? 'is-active' : '' ?>"
       href="<?= e(url('/arena/challenges')) ?>">Categories</a>
    <a class="<?= str_starts_with($path, '/arena/events') ? 'is-active' : '' ?>"
       href="<?= e(url('/arena/events')) ?>">Events</a>
    <a class="<?= str_starts_with($path, '/arena/leaderboard') ? 'is-active' : '' ?>"
       href="<?= e(url('/arena/leaderboard')) ?>">Leaderboard</a>
    <?php if (Auth::check()): ?>
        <a class="<?= str_starts_with($path, '/arena/progress') ? 'is-active' : '' ?>"
           href="<?= e(url('/arena/progress')) ?>">My Progress</a>
    <?php endif; ?>
    <?php if (ChallengeService::canManageArena()): ?>
        <a class="<?= str_starts_with($path, '/arena/admin') ? 'is-active' : '' ?>"
           href="<?= e(url('/arena/admin/challenges')) ?>">Admin</a>
    <?php endif; ?>
</nav>
