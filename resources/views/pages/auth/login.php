<?php ?>
<div class="card">
    <h1>Login</h1>
    <p class="muted">Welcome back to CySkillShare.</p>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/login')) ?>" autocomplete="on">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="login">Username or email</label>
            <input id="login" name="login" type="text" required maxlength="191"
                   value="<?= e((string) old('login')) ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8" maxlength="255">
        </div>
        <button class="btn btn-primary btn-block" type="submit">Login</button>
    </form>
    <p class="muted" style="margin-top:1rem;">No account?
        <a href="<?= e(url('/register')) ?>">Register</a></p>
</div>
