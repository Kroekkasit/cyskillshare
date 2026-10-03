<?php
/**
 * @var bool|null $isPortfolio
 */
use App\Core\Auth;

$path = request_path();
$user = Auth::user();
?>
<nav class="portfolio-nav" aria-label="Portfolio">
    <a class="<?= str_starts_with($path, '/projects') && !str_starts_with($path, '/projects/edit') ? 'is-active' : '' ?>"
       href="<?= e(url('/projects')) ?>">Discover</a>
    <?php if ($user): ?>
        <a class="<?= str_starts_with($path, '/portfolio/' . $user->username) && !str_contains($path, '/resume') ? 'is-active' : '' ?>"
           href="<?= e(url('/portfolio/' . $user->username)) ?>">My Portfolio</a>
        <a class="<?= str_starts_with($path, '/dashboard/portfolio') ? 'is-active' : '' ?>"
           href="<?= e(url('/dashboard/portfolio')) ?>">Dashboard</a>
        <a class="<?= str_starts_with($path, '/settings/portfolio') ? 'is-active' : '' ?>"
           href="<?= e(url('/settings/portfolio')) ?>">Settings</a>
        <a class="<?= str_starts_with($path, '/projects/create') || str_starts_with($path, '/projects/edit') ? 'is-active' : '' ?>"
           href="<?= e(url('/projects/create')) ?>">New Project</a>
    <?php endif; ?>
    <?php if (Auth::hasAnyRole(['admin', 'instructor', 'mentor'])): ?>
        <a class="<?= str_starts_with($path, '/admin/portfolio') || str_starts_with($path, '/admin/projects/verification') ? 'is-active' : '' ?>"
           href="<?= e(url('/admin/portfolio')) ?>">Admin</a>
    <?php endif; ?>
</nav>
