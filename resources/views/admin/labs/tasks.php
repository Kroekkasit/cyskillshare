<?php
/**
 * @var array<string, mixed> $lab
 * @var list<array<string, mixed>> $tasks
 */
$id = (int) $lab['id'];
?>
<section class="page-header">
    <div>
        <h1>Tasks — <?= e((string) $lab['title']) ?></h1>
        <p class="muted">Add tasks and validation rules. Answers are hashed server-side.</p>
    </div>
    <div class="hero-actions">
        <a class="btn" href="<?= e(url('/admin/labs/' . $id . '/edit')) ?>">← Edit Lab</a>
        <a class="btn" href="<?= e(url('/labs/' . $lab['slug'])) ?>">View Public</a>
    </div>
</section>

<div class="card">
    <h2>Existing Tasks</h2>
    <?php if ($tasks === []): ?>
        <p class="muted">No tasks yet.</p>
    <?php else: ?>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Title</th>
                    <th scope="col">Type</th>
                    <th scope="col">Required</th>
                    <th scope="col">Points</th>
                    <th scope="col">Validation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $t): ?>
                    <tr>
                        <td><?= (int) ($t['display_order'] ?? 0) ?></td>
                        <td><?= e((string) $t['title']) ?></td>
                        <td><?= e((string) ($t['task_type'] ?? '')) ?></td>
                        <td><?= (int) ($t['required'] ?? 0) === 1 ? 'Yes' : 'No' ?></td>
                        <td><?= (int) ($t['points'] ?? 0) ?></td>
                        <td><?= e((string) ($t['validation_type'] ?? '—')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<form class="card lab-task-form" method="post" action="<?= e(url('/admin/labs/' . $id . '/tasks')) ?>">
    <?= csrf_field() ?>
    <h2>Add Task</h2>

    <div class="form-group">
        <label for="title">Title *</label>
        <input id="title" name="title" type="text" required maxlength="200">
    </div>
    <div class="form-group">
        <label for="description">Description *</label>
        <textarea id="description" name="description" rows="4" required></textarea>
    </div>

    <div class="filter-row">
        <div class="form-group">
            <label for="task_type">Task Type</label>
            <select id="task_type" name="task_type">
                <?php foreach (['question', 'flag', 'command_output', 'multiple_choice', 'file_analysis', 'log_analysis', 'configuration', 'investigation', 'report', 'manual_verification'] as $tt): ?>
                    <option value="<?= e($tt) ?>"><?= e(str_replace('_', ' ', ucfirst($tt))) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="display_order">Display Order</label>
            <input id="display_order" name="display_order" type="number" value="0">
        </div>
        <div class="form-group">
            <label for="points">Points</label>
            <input id="points" name="points" type="number" min="0" max="100" value="10">
        </div>
    </div>

    <label class="checkbox-label">
        <input type="checkbox" name="required" value="1" checked>
        Required task
    </label>

    <fieldset class="lab-validation-fieldset">
        <legend>Validation</legend>
        <div class="filter-row">
            <div class="form-group">
                <label for="validation_type">Type</label>
                <select id="validation_type" name="validation_type">
                    <?php foreach (['exact', 'regex', 'flag', 'instance_secret', 'multiple_choice', 'manual'] as $vt): ?>
                        <option value="<?= e($vt) ?>"><?= e($vt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="answer_value">Answer Value</label>
                <input id="answer_value" name="answer_value" type="text" maxlength="500"
                       placeholder="Plaintext — hashed on save">
            </div>
        </div>
    </fieldset>

    <button class="btn btn-primary" type="submit">Add Task</button>
</form>
