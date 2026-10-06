<?php
use App\Core\Auth;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$links = [
    ['href' => '/admin', 'label' => 'Dashboard', 'match' => '/admin', 'exact' => true, 'roles' => ['admin', 'instructor', 'moderator', 'mentor']],
    ['href' => '/admin/activity', 'label' => 'Activity Logs', 'match' => '/admin/activity', 'exact' => false, 'roles' => ['admin']],
    ['href' => '/admin/users', 'label' => 'Users', 'match' => '/admin/users', 'exact' => false, 'roles' => ['admin']],
    ['href' => '/admin/system', 'label' => 'System', 'match' => '/admin/system', 'exact' => false, 'roles' => ['admin']],
    ['href' => '/moderation/reports', 'label' => 'Moderation', 'match' => '/moderation', 'exact' => false, 'roles' => ['moderator', 'admin']],
    ['href' => '/admin/skills', 'label' => 'Skills', 'match' => '/admin/skills', 'exact' => false, 'roles' => ['instructor', 'admin']],
    ['href' => '/arena/admin/challenges', 'label' => 'Arena', 'match' => '/arena/admin', 'exact' => false, 'roles' => ['instructor', 'admin']],
    ['href' => '/admin/labs', 'label' => 'Labs', 'match' => '/admin/labs', 'exact' => false, 'roles' => ['instructor', 'admin']],
    ['href' => '/admin/collaboration', 'label' => 'Collaboration', 'match' => '/admin/collaboration', 'exact' => false, 'roles' => ['instructor', 'admin']],
];
?>
<nav class="admin-subnav" aria-label="Admin">
    <?php foreach ($links as $link): ?>
        <?php if (!Auth::hasAnyRole($link['roles'])) {
            continue;
        } ?>
        <?php
        $active = !empty($link['exact'])
            ? ($path === $link['match'] || $path === $link['match'] . '/')
            : str_starts_with($path, $link['match']);
        ?>
        <a class="<?= $active ? 'is-active' : '' ?>" href="<?= e(url($link['href'])) ?>"><?= e($link['label']) ?></a>
    <?php endforeach; ?>
</nav>
