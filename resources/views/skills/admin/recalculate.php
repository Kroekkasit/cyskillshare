<?php
/**
 * Recalculate skill progress form.
 */
?>
<?php \App\Core\View::partial('skills/partials/skill-nav'); ?>

<section class="page-header">
    <div>
        <h1>Recalculate Progress</h1>
        <p class="muted">Rebuild user_skills cache from accepted evidence. Use after changing requirements.</p>
    </div>
    <a class="btn" href="<?= e(url('/admin/skills')) ?>">← Back to Skills</a>
</section>

<div class="card">
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form class="admin-form" method="post" action="<?= e(url('/admin/skills/recalculate')) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="user_id">User ID (optional)</label>
            <input type="number" id="user_id" name="user_id" min="1" placeholder="Leave empty to recalculate all users">
            <p class="muted small">Specify a user ID to recalculate only that user, or leave blank for a global recalculation.</p>
        </div>
        <div class="hero-actions">
            <button class="btn btn-primary" type="submit" onclick="return confirm('Run recalculation? This may take a moment.')">
                Run Recalculation
            </button>
        </div>
    </form>
</div>
