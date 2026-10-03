<?php ?>
<div class="card">
    <h1>Register</h1>
    <p class="muted">Join the KKU cybersecurity community.</p>
    <?php \App\Core\View::partial('components/form-errors'); ?>
    <form method="post" action="<?= e(url('/register')) ?>" autocomplete="on">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" required minlength="3" maxlength="50"
                   pattern="[A-Za-z0-9_]+" value="<?= e((string) old('username')) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required maxlength="191"
                   value="<?= e((string) old('email')) ?>">
        </div>
        <div class="form-group">
            <label for="full_name">Full name (optional)</label>
            <input id="full_name" name="full_name" type="text" maxlength="150"
                   value="<?= e((string) old('full_name')) ?>">
        </div>
        <div class="form-group">
            <label for="student_id">Student ID (optional)</label>
            <input id="student_id" name="student_id" type="text" maxlength="50"
                   value="<?= e((string) old('student_id')) ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8" maxlength="255">
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8">
        </div>
        <button class="btn btn-primary btn-block" type="submit">Create account</button>
    </form>
    <p class="muted" style="margin-top:1rem;">Already have an account?
        <a href="<?= e(url('/login')) ?>">Login</a></p>
</div>
