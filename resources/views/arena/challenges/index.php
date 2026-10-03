<?php
/**
 * @var list<array<string, mixed>> $challenges
 * @var int $total
 * @var int $page
 * @var int $perPage
 * @var array<string, string> $filters
 * @var list<\App\Models\ChallengeCategory> $categories
 */
use App\Core\Auth;

$sorts = [
    'newest' => 'Newest',
    'oldest' => 'Oldest',
    'points' => 'Points',
    'most_solved' => 'Most Solved',
    'least_solved' => 'Least Solved',
    'difficulty' => 'Difficulty',
];
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<section class="page-header">
    <div>
        <h1>Challenges</h1>
        <p class="muted">Filter and browse published Cyber Arena challenges.</p>
    </div>
</section>

<form class="card arena-filters" method="get" action="<?= e(url('/arena/challenges')) ?>">
    <div class="filter-row">
        <div class="form-group">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Title, tags, category…">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat->slug) ?>"<?= ($filters['category'] ?? '') === $cat->slug ? ' selected' : '' ?>>
                        <?= e($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <select id="difficulty" name="difficulty">
                <option value="">All difficulties</option>
                <?php foreach (['easy', 'medium', 'hard', 'expert'] as $d): ?>
                    <option value="<?= e($d) ?>"<?= ($filters['difficulty'] ?? '') === $d ? ' selected' : '' ?>>
                        <?= e(ucfirst($d)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (Auth::check()): ?>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="all"<?= ($filters['solve_status'] ?? 'all') === 'all' ? ' selected' : '' ?>>All</option>
                <option value="solved"<?= ($filters['solve_status'] ?? '') === 'solved' ? ' selected' : '' ?>>Solved</option>
                <option value="unsolved"<?= ($filters['solve_status'] ?? '') === 'unsolved' ? ' selected' : '' ?>>Unsolved</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="sort">Sort</label>
            <select id="sort" name="sort">
                <?php foreach ($sorts as $key => $label): ?>
                    <option value="<?= e($key) ?>"<?= ($filters['sort'] ?? 'newest') === $key ? ' selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Apply Filters</button>
</form>

<?php if ($challenges === []): ?>
    <div class="empty-state card">
        <h2>No challenges found</h2>
        <p class="muted">Try adjusting your filters or check back later for new content.</p>
        <a class="btn" href="<?= e(url('/arena/challenges')) ?>">Clear Filters</a>
    </div>
<?php else: ?>
    <div class="challenge-grid">
        <?php foreach ($challenges as $challenge): ?>
            <?php \App\Core\View::partial('arena/partials/challenge-card', ['challenge' => $challenge]); ?>
        <?php endforeach; ?>
    </div>
    <?php \App\Core\View::partial('components/pagination', [
        'page' => $page,
        'total' => $total,
        'perPage' => $perPage,
        'baseUrl' => '/arena/challenges',
        'query' => array_filter([
            'search' => $filters['search'] ?? '',
            'category' => $filters['category'] ?? '',
            'difficulty' => $filters['difficulty'] ?? '',
            'status' => $filters['solve_status'] ?? '',
            'sort' => $filters['sort'] ?? '',
        ], static fn(string $v): bool => $v !== ''),
    ]); ?>
<?php endif; ?>
