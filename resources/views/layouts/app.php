<?php
/** @var string $content */
/** @var string $title */

use App\Core\Auth;
use App\Models\Notification;

$path = request_path();
$user = Auth::user();
$unread = $user ? Notification::unreadCount($user->id) : 0;
$channelsGrouped = $channelsGrouped ?? null;
$activeChannel = $activeChannel ?? null;
$isCommunity = is_array($channelsGrouped);
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
<div class="app-shell<?= $isCommunity ? ' app-shell-community' : '' ?>">
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
                    <div class="role"><?= e($user->primaryRoleLabel()) ?></div>
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
            <a class="<?= str_starts_with($path, '/community') || str_starts_with($path, '/thread') || str_starts_with($path, '/tag') || str_starts_with($path, '/search') ? 'is-active' : '' ?>"
               href="<?= e(url('/community')) ?>">
                <span class="icon">#</span> Community
            </a>
            <a class="<?= str_starts_with($path, '/arena') ? 'is-active' : '' ?>"
               href="<?= e(url('/arena')) ?>">
                <span class="icon">⚔</span> Arena
            </a>
            <a class="<?= str_starts_with($path, '/skills') || str_starts_with($path, '/admin/skills') ? 'is-active' : '' ?>"
               href="<?= e(url('/skills')) ?>">
                <span class="icon">◈</span> Skills
            </a>
            <a class="<?= str_starts_with($path, '/portfolio') || str_starts_with($path, '/projects') || str_starts_with($path, '/dashboard/portfolio') || str_starts_with($path, '/settings/portfolio') || str_starts_with($path, '/admin/portfolio') ? 'is-active' : '' ?>"
               href="<?= e(url('/projects')) ?>">
                <span class="icon">◆</span> Portfolio
            </a>
            <?php if ($user): ?>
                <a class="<?= str_starts_with($path, '/portfolio/' . $user->username) ? 'is-active' : '' ?>"
                   href="<?= e(url('/portfolio/' . $user->username)) ?>">
                    <span class="icon">▣</span> My Showcase
                </a>
                <a class="<?= str_starts_with($path, '/dashboard/portfolio') ? 'is-active' : '' ?>"
                   href="<?= e(url('/dashboard/portfolio')) ?>">
                    <span class="icon">▤</span> Portfolio Dashboard
                </a>
                <a class="<?= str_starts_with($path, '/notifications') ? 'is-active' : '' ?>" href="<?= e(url('/notifications')) ?>">
                    <span class="icon">🔔</span> Notifications
                    <?php if ($unread > 0): ?><span class="badge"><?= (int) $unread ?></span><?php endif; ?>
                </a>
                <a class="<?= str_starts_with($path, '/community/bookmarks') ? 'is-active' : '' ?>" href="<?= e(url('/community/bookmarks')) ?>">
                    <span class="icon">★</span> Bookmarks
                </a>
                <a class="<?= str_starts_with($path, '/profile/') || str_starts_with($path, '/u/') ? 'is-active' : '' ?>"
                   href="<?= e(url('/profile/' . $user->username)) ?>">
                    <span class="icon">◎</span> Profile
                </a>
                <?php if (Auth::hasAnyRole(['moderator', 'admin'])): ?>
                    <a class="<?= str_starts_with($path, '/moderation') ? 'is-active' : '' ?>" href="<?= e(url('/moderation/reports')) ?>">
                        <span class="icon">⚑</span> Moderation
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
                <?php if ($isCommunity): ?>
                    <button type="button" class="btn" data-channel-drawer-toggle aria-expanded="false">Channels</button>
                <?php endif; ?>
                <?php \App\Core\View::partial('components/theme-toggle'); ?>
                <a class="btn btn-ghost" href="<?= e(url('/community')) ?>">Community</a>
                <?php if ($user): ?>
                    <a class="btn btn-ghost" href="<?= e(url('/notifications')) ?>">🔔<?= $unread > 0 ? ' ' . (int) $unread : '' ?></a>
                <?php else: ?>
                    <a class="btn btn-primary" href="<?= e(url('/login')) ?>">Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="content-panel<?= $isCommunity ? ' content-panel-community' : '' ?>">
            <?php if ($isCommunity): ?>
                <div class="community-layout">
                    <?php \App\Core\View::partial('components/channel-sidebar', [
                        'channelsGrouped' => $channelsGrouped,
                        'activeChannel' => $activeChannel,
                    ]); ?>
                    <div class="community-main">
                        <?php \App\Core\View::partial('components/flash-message'); ?>
                        <?= $content ?>
                    </div>
                </div>
            <?php else: ?>
                <?php \App\Core\View::partial('components/flash-message'); ?>
                <?= $content ?>
            <?php endif; ?>
            <footer class="site-footer">
                <p>CySkillShare — College of Computing, Khon Kaen University</p>
            </footer>
        </div>
    </div>
</div>

<dialog id="report-dialog" class="modal-dialog">
    <form method="post" action="<?= e(url('/report')) ?>" class="card modal-card">
        <?= csrf_field() ?>
        <input type="hidden" name="target_type" id="report-target-type" value="">
        <input type="hidden" name="target_id" id="report-target-id" value="">
        <input type="hidden" name="redirect" value="<?= e($path) ?>">
        <h2>Report Content</h2>
        <div class="form-group">
            <label for="report-reason">Reason</label>
            <select id="report-reason" name="reason" required>
                <option value="spam">Spam</option>
                <option value="harassment">Harassment</option>
                <option value="malicious_content">Malicious Content</option>
                <option value="incorrect_dangerous">Incorrect / Dangerous Information</option>
                <option value="academic_misconduct">Academic Misconduct</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="report-description">Additional information</label>
            <textarea id="report-description" name="description" rows="3" maxlength="2000"></textarea>
        </div>
        <div class="hero-actions">
            <button class="btn btn-primary" type="submit">Submit Report</button>
            <button class="btn" type="button" data-close-report>Cancel</button>
        </div>
    </form>
</dialog>

<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
