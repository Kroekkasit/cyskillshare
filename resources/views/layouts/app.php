<?php
/** @var string $content */
/** @var string $title */

use App\Core\Auth;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'CySkillShare') ?></title>
    <?php \App\Core\View::partial('components/theme-boot'); ?>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand-lockup" href="<?= e(url('/')) ?>">
            <span class="brand-mark">Cy</span>
            <span class="brand-text">
                <strong>CySkillShare</strong>
                <span>KKU · CoC</span>
            </span>
        </a>

        <?php if ($user): ?>
            <div class="sidebar-user">
                <div class="avatar"><?= e(strtoupper(substr($user->username, 0, 1))) ?></div>
                <div class="meta">
                    <div class="name"><?= e($user->username) ?></div>
                    <div class="role"><?= e(implode(' · ', $user->roleNames())) ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="sidebar-user">
                <div class="avatar">?</div>
                <div class="meta">
                    <div class="name">Guest</div>
                    <div class="role">Sign in to join</div>
                </div>
            </div>
        <?php endif; ?>

        <nav class="side-nav" aria-label="Primary">
            <a class="<?= $path === '/' ? 'is-active' : '' ?>" href="<?= e(url('/')) ?>">
                <span class="icon">⌂</span> Home
            </a>
            <a class="<?= str_starts_with((string) $path, '/community') || str_starts_with((string) $path, '/thread') ? 'is-active' : '' ?>"
               href="<?= e(url('/community')) ?>">
                <span class="icon">#</span> Community
            </a>
            <?php if ($user): ?>
                <a class="<?= str_starts_with((string) $path, '/u/') ? 'is-active' : '' ?>"
                   href="<?= e(url('/u/' . $user->username)) ?>">
                    <span class="icon">◎</span> Profile
                </a>
                <?php if (Auth::hasRole('admin')): ?>
                    <a class="<?= str_starts_with((string) $path, '/admin') ? 'is-active' : '' ?>"
                       href="<?= e(url('/admin/demo')) ?>">
                        <span class="icon">⚑</span> Admin
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= e(url('/login')) ?>"><span class="icon">→</span> Login</a>
                <a href="<?= e(url('/register')) ?>"><span class="icon">+</span> Register</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <?php \App\Core\View::partial('components/theme-toggle'); ?>
            <?php if ($user): ?>
                <form action="<?= e(url('/logout')) ?>" method="post">
                    <?= csrf_field() ?>
                    <button class="btn btn-block" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="btn btn-primary btn-block" href="<?= e(url('/register')) ?>">+ Join CySkillShare</a>
            <?php endif; ?>
        </div>
    </aside>

    <div class="main-pane">
        <header class="mobile-bar">
            <a class="brand-lockup" href="<?= e(url('/')) ?>">
                <span class="brand-mark">Cy</span>
                <span class="brand-text"><strong>CySkillShare</strong></span>
            </a>
            <div class="mobile-nav">
                <?php \App\Core\View::partial('components/theme-toggle'); ?>
                <a class="btn btn-ghost" href="<?= e(url('/community')) ?>">Community</a>
                <?php if ($user): ?>
                    <form action="<?= e(url('/logout')) ?>" method="post" style="display:inline;">
                        <?= csrf_field() ?>
                        <button class="btn" type="submit">Logout</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="content-panel">
            <?php \App\Core\View::partial('components/flash-message'); ?>
            <?= $content ?>
            <footer class="site-footer">
                <p>CySkillShare — College of Computing, Khon Kaen University</p>
            </footer>
        </div>
    </div>
</div>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
