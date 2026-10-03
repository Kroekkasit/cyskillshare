<?php
/**
 * @var list<array<string, mixed>> $tree
 * @var string $search
 * @var string $categoryFilter
 * @var list<\App\Models\SkillCategory> $categories
 * @var array<int, \App\Models\SkillLevel> $levels
 * @var array<string, int>|null $stats
 */
use App\Core\Auth;

$renderNodes = static function (array $nodes, array $childrenMap, int $depth = 0) use (&$renderNodes): void {
    foreach ($nodes as $node) {
        /** @var \App\Models\Skill $skill */
        $skill = $node['skill'];
        $indent = $depth > 0 ? ' skill-node-child' : '';
        ?>
        <li class="skill-node<?= $indent ?>">
            <a class="skill-node-link" href="<?= e(url('/skills/' . $skill->slug)) ?>">
                <span class="skill-level-symbol" title="<?= e($node['level_name']) ?>"><?= e($node['symbol']) ?></span>
                <span class="skill-node-name"><?= e($skill->name) ?></span>
                <?php if ($skill->is_gated): ?>
                    <span class="pill lock">Gated</span>
                <?php endif; ?>
            </a>
            <?php if (Auth::check() && (int) $node['level'] > 0): ?>
                <span class="skill-node-level muted"><?= e($node['level_name']) ?></span>
            <?php endif; ?>
        </li>
        <?php
        $children = $childrenMap[$skill->id] ?? [];
        if ($children !== []) {
            echo '<ul class="skill-node-children">';
            $renderNodes($children, $childrenMap, $depth + 1);
            echo '</ul>';
        }
    }
};
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>Skill Tree</h1>
        <p class="muted">Track your cybersecurity skills through evidence from Arena challenges, community contributions, and verified work.</p>
    </div>
    <?php if (Auth::check() && isset($stats)): ?>
        <div class="skill-stats-summary">
            <span><strong><?= (int) $stats['started'] ?></strong> started</span>
            <span><strong><?= (int) $stats['demonstrated'] ?></strong> demonstrated</span>
        </div>
    <?php endif; ?>
</section>

<div class="skill-legend card">
    <h2 class="skill-legend-title">Level Legend</h2>
    <ul class="skill-legend-list">
        <?php foreach ($levels as $lvl => $def): ?>
            <?php if ($lvl === 0) {
                continue;
            } ?>
            <li>
                <span class="skill-level-symbol"><?= e(\App\Services\SkillTreeService::levelSymbol($lvl)) ?></span>
                <span><?= e($def->name) ?></span>
            </li>
        <?php endforeach; ?>
        <li><span class="skill-level-symbol">○</span><span>Not started</span></li>
    </ul>
</div>

<form class="card skill-filters" method="get" action="<?= e(url('/skills')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" value="<?= e($search) ?>" placeholder="Skill name or keyword">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat->slug) ?>" <?= $categoryFilter === $cat->slug ? 'selected' : '' ?>>
                        <?= e($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group filter-actions">
            <label>&nbsp;</label>
            <button class="btn btn-primary" type="submit">Filter</button>
            <?php if ($search !== '' || $categoryFilter !== ''): ?>
                <a class="btn" href="<?= e(url('/skills')) ?>">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($tree === []): ?>
    <div class="empty-state card">
        <h2>No skills found</h2>
        <p class="muted">Try adjusting your search or category filter.</p>
    </div>
<?php else: ?>
    <div class="skill-tree">
        <?php foreach ($tree as $group): ?>
            <?php
            /** @var \App\Models\SkillCategory $cat */
            $cat = $group['category'];
            $roots = $group['roots'];
            $childrenMap = $group['children_map'];
            if ($roots === [] && ($group['skills'] ?? []) === []) {
                continue;
            }
            ?>
            <details class="skill-category card" open>
                <summary class="skill-category-head">
                    <h2><?= e($cat->name) ?></h2>
                    <?php if ($cat->description): ?>
                        <p class="muted"><?= e($cat->description) ?></p>
                    <?php endif; ?>
                </summary>
                <ul class="skill-node-list">
                    <?php
                    $displayRoots = $roots !== [] ? $roots : $group['skills'];
                    $renderNodes($displayRoots, $childrenMap);
                    ?>
                </ul>
            </details>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
