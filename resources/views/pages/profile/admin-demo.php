<?php
/** @var \App\Models\User|null $user */
?>
<section class="hero">
    <h1>Admin Demo</h1>
    <p class="subtitle">RBAC gate — only users with the admin role can see this page.</p>
</section>
<div class="card">
    <p>Authenticated as <strong><?= e($user?->username ?? '') ?></strong>.</p>
    <p class="muted">This page exists only to verify authorization in Phase 1.</p>
</div>
