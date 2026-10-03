<?php
/**
 * @var int $page
 * @var int $total
 * @var int $perPage
 * @var string $baseUrl
 * @var array<string, scalar> $query
 */
$pages = max(1, (int) ceil($total / max(1, $perPage)));
if ($pages <= 1) {
    return;
}
$query = $query ?? [];
?>
<nav class="pagination" aria-label="Pagination">
    <?php for ($i = 1; $i <= min($pages, 12); $i++): ?>
        <?php
        $q = http_build_query(array_merge($query, ['page' => $i]));
        $href = url($baseUrl . ($q !== '' ? '?' . $q : ''));
        ?>
        <?php if ($i === $page): ?>
            <span class="page is-current" aria-current="page"><?= $i ?></span>
        <?php else: ?>
            <a class="page" href="<?= e($href) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</nav>
