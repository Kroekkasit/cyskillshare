<?php
/**
 * @var \App\Models\Challenge $challenge
 * @var \App\Models\ChallengeCategory|null $category
 * @var \App\Models\User|null $author
 * @var list<array<string, mixed>> $hints
 * @var array<int, array<string, mixed>> $hintUsage
 * @var list<\App\Models\ChallengeFile> $files
 * @var array{solves:int, attempts:int, solve_rate:int} $stats
 * @var \App\Models\ChallengeSolve|null $solve
 * @var int|null $threadId
 * @var list<array{id:int,name:string,slug:string}> $tags
 */
use App\Core\Auth;
?>
<?php \App\Core\View::partial('arena/partials/arena-nav'); ?>

<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= e(url('/arena')) ?>">Cyber Arena</a>
    <span>/</span>
    <a href="<?= e(url('/arena/challenges')) ?>">Challenges</a>
    <?php if ($category): ?>
        <span>/</span>
        <a href="<?= e(url('/arena/categories/' . $category->slug)) ?>"><?= e($category->name) ?></a>
    <?php endif; ?>
</nav>

<article class="card challenge-detail">
    <header class="challenge-detail-head">
        <h1><?= e($challenge->title) ?></h1>
        <div class="challenge-detail-meta">
            <span class="difficulty-badge difficulty-<?= e($challenge->difficulty) ?>"><?= e(ucfirst($challenge->difficulty)) ?></span>
            <span class="pill"><?= (int) $challenge->points ?> pts</span>
            <?php if ($solve !== null): ?>
                <span class="pill solved">✓ Solved</span>
            <?php endif; ?>
            <?php if ($challenge->status !== 'published'): ?>
                <span class="pill lock"><?= e(ucfirst($challenge->status)) ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="challenge-stats-row muted">
        <span><?= (int) $stats['solves'] ?> solves</span>
        <span><?= (int) $stats['attempts'] ?> attempts</span>
        <span><?= (int) $stats['solve_rate'] ?>% solve rate</span>
        <?php if ($author): ?>
            <span>By <a href="<?= e(url('/profile/' . $author->username)) ?>"><?= e($author->username) ?></a></span>
        <?php endif; ?>
    </div>

    <?php if ($tags !== []): ?>
        <div class="tag-row">
            <?php foreach ($tags as $tag): ?>
                <?php \App\Core\View::partial('components/tag', ['tag' => $tag]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="content-body challenge-description">
        <?= markdown($challenge->description) ?>
    </div>

    <?php if ($files !== []): ?>
    <section class="challenge-section" aria-labelledby="files-heading">
        <h2 id="files-heading">Attachments</h2>
        <ul class="file-list">
            <?php foreach ($files as $file): ?>
                <li>
                    <a href="<?= e(url('/arena/challenges/' . $challenge->id . '/files/' . $file->id)) ?>">
                        <?= e($file->original_name) ?>
                    </a>
                    <span class="muted">(<?= number_format($file->file_size / 1024, 1) ?> KB)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($hints !== []): ?>
    <section class="challenge-section" aria-labelledby="hints-heading">
        <h2 id="hints-heading">Hints</h2>
        <p class="muted small">Hints are revealed in order. Each hint may reduce your score.</p>
        <?php foreach ($hints as $hint): ?>
            <div class="hint-card card">
                <div class="hint-card-head">
                    <strong>Hint #<?= (int) $hint['hint_order'] ?></strong>
                    <?php if ((int) $hint['point_penalty'] > 0): ?>
                        <span class="muted">−<?= (int) $hint['point_penalty'] ?> pts</span>
                    <?php endif; ?>
                </div>
                <?php if ($hint['revealed']): ?>
                    <div class="hint-content"><?= markdown((string) $hint['content']) ?></div>
                    <?php if (isset($hintUsage[$hint['id']])): ?>
                        <p class="muted small">Revealed <?= e(time_ago($hintUsage[$hint['id']]['revealed_at'])) ?></p>
                    <?php endif; ?>
                <?php elseif (Auth::check() && $solve === null): ?>
                    <form method="post" action="<?= e(url('/arena/challenges/' . $challenge->id . '/hints/' . $hint['id'] . '/reveal')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn" type="submit">Reveal Hint</button>
                    </form>
                <?php else: ?>
                    <p class="muted">Log in to reveal hints<?= $solve !== null ? ' (already solved)' : '' ?>.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <section class="challenge-section" aria-labelledby="flag-heading">
        <h2 id="flag-heading">Submit Flag</h2>
        <?php if ($solve !== null): ?>
            <div class="flash flash-success">
                You solved this challenge and earned <?= (int) $solve->points_awarded ?> Arena points
                <?= $solve->hints_used > 0 ? ' (used ' . (int) $solve->hints_used . ' hint(s))' : '' ?>.
            </div>
            <p class="muted">Solved <?= e(time_ago($solve->solved_at)) ?></p>
        <?php elseif (Auth::check() && $challenge->isSolvable()): ?>
            <div class="flag-box">
                <form method="post" action="<?= e(url('/arena/challenges/' . $challenge->id . '/submit')) ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="flag">Flag</label>
                        <input type="text" id="flag" name="flag" required maxlength="500"
                               placeholder="CySkillShare{...}" autocomplete="off" spellcheck="false">
                    </div>
                    <button class="btn btn-primary" type="submit">Submit Flag</button>
                </form>
            </div>
        <?php elseif (!Auth::check()): ?>
            <p class="muted"><a href="<?= e(url('/login')) ?>">Log in</a> to submit a flag.</p>
        <?php else: ?>
            <p class="muted">This challenge is not currently available for submission.</p>
        <?php endif; ?>
    </section>

    <section class="challenge-section" aria-labelledby="discuss-heading">
        <h2 id="discuss-heading">Discussion</h2>
        <p class="muted spoiler-note">⚠ Discussion threads may contain spoilers. Proceed with caution.</p>
        <?php if (Auth::check()): ?>
            <?php if ($threadId !== null): ?>
                <a class="btn" href="<?= e(url('/thread/' . $threadId)) ?>">Open Discussion</a>
            <?php else: ?>
                <form method="post" action="<?= e(url('/arena/challenges/' . $challenge->id . '/discuss')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn" type="submit">Start Discussion</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p class="muted"><a href="<?= e(url('/login')) ?>">Log in</a> to join the discussion.</p>
        <?php endif; ?>
    </section>

    <section class="challenge-section" aria-labelledby="writeups-heading">
        <h2 id="writeups-heading">Writeups</h2>
        <p class="muted">Official writeups — coming soon.</p>
    </section>
</article>
