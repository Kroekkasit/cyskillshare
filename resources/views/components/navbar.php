<?php

use App\Core\Auth;
?>
<header class="navbar">
    <div class="inner">
        <a class="brand" href="<?= e(url('/')) ?>">CySkillShare</a>
        <nav class="nav-links">
            <a href="<?= e(url('/')) ?>">Home</a>
            <a href="<?= e(url('/community')) ?>">Community</a>
            <?php if (Auth::check()): ?>
                <a href="<?= e(url('/u/' . Auth::user()?->username)) ?>">@<?= e(Auth::user()?->username ?? '') ?></a>
                <?php if (Auth::hasRole('admin')): ?>
                    <a href="<?= e(url('/admin/demo')) ?>">Admin</a>
                <?php endif; ?>
                <form action="<?= e(url('/logout')) ?>" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <button class="btn" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('/login')) ?>">Login</a>
                <a class="btn btn-primary" href="<?= e(url('/register')) ?>">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
