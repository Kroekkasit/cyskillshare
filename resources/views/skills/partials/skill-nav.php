<?php
/**
 * @var bool|null $isSkills
 */
use App\Core\Auth;
use App\Services\SkillEvidenceService;
use App\Services\SkillService;

$path = request_path();
?>
<nav class="skill-nav" aria-label="Skill Tree">
    <a class="<?= $path === '/skills' ? 'is-active' : '' ?>"
       href="<?= e(url('/skills')) ?>">Skills</a>
    <?php if (Auth::check()): ?>
        <a class="<?= str_starts_with($path, '/skills/evidence') ? 'is-active' : '' ?>"
           href="<?= e(url('/skills/evidence')) ?>">My Evidence</a>
    <?php endif; ?>
    <?php if (SkillEvidenceService::canVerify()): ?>
        <a class="<?= str_starts_with($path, '/skills/verification') ? 'is-active' : '' ?>"
           href="<?= e(url('/skills/verification')) ?>">Verification</a>
    <?php endif; ?>
    <?php if (SkillService::canManage()): ?>
        <a class="<?= str_starts_with($path, '/admin/skills') ? 'is-active' : '' ?>"
           href="<?= e(url('/admin/skills')) ?>">Admin</a>
    <?php endif; ?>
</nav>
