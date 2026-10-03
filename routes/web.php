<?php

declare(strict_types=1);

use App\Controllers\ArenaAdminController;
use App\Controllers\ArenaController;
use App\Controllers\ArenaEventController;
use App\Controllers\ArenaLeaderboardController;
use App\Controllers\ArenaProgressController;
use App\Controllers\AuthController;
use App\Controllers\BookmarkController;
use App\Controllers\ChallengeController;
use App\Controllers\CommunityController;
use App\Controllers\HomeController;
use App\Controllers\ModerationController;
use App\Controllers\NotificationController;
use App\Controllers\PortfolioAdminController;
use App\Controllers\PortfolioController;
use App\Controllers\PortfolioSettingsController;
use App\Controllers\ProfileController;
use App\Controllers\ProjectController;
use App\Controllers\ProjectMediaController;
use App\Controllers\ProjectVerificationController;
use App\Controllers\ReplyController;
use App\Controllers\ReportController;
use App\Controllers\SearchController;
use App\Controllers\SkillAdminController;
use App\Controllers\SkillController;
use App\Controllers\SkillEvidenceController;
use App\Controllers\SkillVerificationController;
use App\Controllers\ThreadController;
use App\Controllers\VoteController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Community — static paths BEFORE {channel}
$router->get('/community', [CommunityController::class, 'index']);
$router->get('/community/new', [CommunityController::class, 'createForm'], [AuthMiddleware::class]);
$router->post('/community/new', [CommunityController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/community/bookmarks', [CommunityController::class, 'bookmarks'], [AuthMiddleware::class]);
$router->get('/community/{channel}', [CommunityController::class, 'show']);

$router->get('/thread/{id}', [ThreadController::class, 'show']);
$router->get('/thread/{id}/edit', [ThreadController::class, 'editForm'], [AuthMiddleware::class]);
$router->post('/thread/{id}/edit', [ThreadController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/delete', [ThreadController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/reply', [ThreadController::class, 'reply'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/best-answer', [ThreadController::class, 'bestAnswer'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/pin', [ThreadController::class, 'pin'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/lock', [ThreadController::class, 'lock'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/vote', [VoteController::class, 'voteThread'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/bookmark', [BookmarkController::class, 'toggle'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Legacy create route kept for compatibility
$router->post('/thread/create', [CommunityController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->post('/reply/{id}/edit', [ReplyController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/reply/{id}/delete', [ReplyController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/reply/{id}/vote', [VoteController::class, 'voteReply'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/tag/{slug}', [SearchController::class, 'tag']);
$router->get('/search', [SearchController::class, 'index']);

$router->get('/notifications', [NotificationController::class, 'index'], [AuthMiddleware::class]);
$router->post('/notifications/read', [NotificationController::class, 'markRead'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->post('/report', [ReportController::class, 'store'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/moderation/reports', [ModerationController::class, 'reports'], [AuthMiddleware::class]);
$router->post('/moderation/reports/{id}/resolve', [ModerationController::class, 'resolve'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/moderation/reports/{id}/dismiss', [ModerationController::class, 'dismiss'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Skill Tree — static paths before {slug}
$router->get('/skills', [SkillController::class, 'index']);
$router->get('/skills/evidence', [SkillEvidenceController::class, 'index'], [AuthMiddleware::class]);
$router->get('/skills/verification', [SkillVerificationController::class, 'index'], [AuthMiddleware::class]);
$router->post('/skills/verification/{id}/accept', [SkillVerificationController::class, 'accept'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/skills/verification/{id}/reject', [SkillVerificationController::class, 'reject'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/skills/privacy', [SkillController::class, 'privacy'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/skills/evidence/manual', [SkillEvidenceController::class, 'submitManual'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/skills/{slug}', [SkillController::class, 'show']);

// Skill admin
$router->get('/admin/skills', [SkillAdminController::class, 'index'], [AuthMiddleware::class]);
$router->get('/admin/skills/create', [SkillAdminController::class, 'createForm'], [AuthMiddleware::class]);
$router->post('/admin/skills/create', [SkillAdminController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/skills/recalculate', [SkillAdminController::class, 'recalculateForm'], [AuthMiddleware::class]);
$router->post('/admin/skills/recalculate', [SkillAdminController::class, 'recalculate'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/skills/{id}/edit', [SkillAdminController::class, 'editForm'], [AuthMiddleware::class]);
$router->post('/admin/skills/{id}/edit', [SkillAdminController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/skills/{id}/requirements', [SkillAdminController::class, 'requirementsForm'], [AuthMiddleware::class]);
$router->post('/admin/skills/{id}/requirements', [SkillAdminController::class, 'addRequirement'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/skills/{id}/prerequisites', [SkillAdminController::class, 'addPrerequisite'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/skills/requirements/{id}/delete', [SkillAdminController::class, 'deleteRequirement'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/profile/{username}', [ProfileController::class, 'show']);
$router->post('/profile/{username}', [ProfileController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/u/{username}', [ProfileController::class, 'show']);
$router->get('/admin/demo', [ProfileController::class, 'adminDemo'], [AuthMiddleware::class]);

// Cyber Arena — static paths before parameterized routes
$router->get('/arena', [ArenaController::class, 'index']);
$router->get('/arena/challenges', [ChallengeController::class, 'index']);
$router->get('/arena/challenges/{id}', [ChallengeController::class, 'show']);
$router->post('/arena/challenges/{id}/submit', [ChallengeController::class, 'submit'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/challenges/{id}/hints/{hintId}/reveal', [ChallengeController::class, 'revealHint'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/arena/challenges/{id}/files/{fileId}', [ChallengeController::class, 'download']);
$router->get('/arena/challenges/{id}/download/{fileId}', [ChallengeController::class, 'download']);
$router->post('/arena/challenges/{id}/discuss', [ChallengeController::class, 'discuss'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/arena/categories/{slug}', [ChallengeController::class, 'category']);
$router->get('/arena/progress', [ArenaProgressController::class, 'index'], [AuthMiddleware::class]);
$router->get('/arena/leaderboard', [ArenaLeaderboardController::class, 'index']);
$router->get('/arena/events', [ArenaEventController::class, 'index']);
$router->get('/arena/events/{id}', [ArenaEventController::class, 'show']);

// Arena admin
$router->get('/arena/admin/challenges', [ArenaAdminController::class, 'challenges'], [AuthMiddleware::class]);
$router->get('/arena/admin/challenges/new', [ArenaAdminController::class, 'createForm'], [AuthMiddleware::class]);
$router->post('/arena/admin/challenges', [ArenaAdminController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/arena/admin/challenges/{id}/edit', [ArenaAdminController::class, 'editForm'], [AuthMiddleware::class]);
$router->post('/arena/admin/challenges/{id}', [ArenaAdminController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/challenges/{id}/publish', [ArenaAdminController::class, 'publish'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/challenges/{id}/archive', [ArenaAdminController::class, 'archive'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/challenges/{id}/hints', [ArenaAdminController::class, 'addHint'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/hints/{id}', [ArenaAdminController::class, 'editHint'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/hints/{id}/delete', [ArenaAdminController::class, 'deleteHint'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/challenges/{id}/files', [ArenaAdminController::class, 'uploadFile'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/arena/admin/files/{id}/delete', [ArenaAdminController::class, 'deleteFile'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/arena/admin/events', [ArenaAdminController::class, 'events'], [AuthMiddleware::class]);
$router->get('/arena/admin/events/new', [ArenaAdminController::class, 'createEventForm'], [AuthMiddleware::class]);
$router->post('/arena/admin/events', [ArenaAdminController::class, 'createEvent'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Portfolio — static paths before parameterized routes
$router->get('/portfolio/{username}/resume', [PortfolioController::class, 'resume']);
$router->get('/portfolio/{username}', [PortfolioController::class, 'show']);
$router->get('/dashboard/portfolio', [PortfolioController::class, 'dashboard'], [AuthMiddleware::class]);

$router->get('/settings/portfolio', [PortfolioSettingsController::class, 'editForm'], [AuthMiddleware::class]);
$router->post('/settings/portfolio', [PortfolioSettingsController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/education', [PortfolioSettingsController::class, 'addEducation'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/education/{id}/delete', [PortfolioSettingsController::class, 'deleteEducation'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/experience', [PortfolioSettingsController::class, 'addExperience'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/experience/{id}/delete', [PortfolioSettingsController::class, 'deleteExperience'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/certifications', [PortfolioSettingsController::class, 'addCertification'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/settings/portfolio/certifications/{id}/delete', [PortfolioSettingsController::class, 'deleteCertification'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/projects', [ProjectController::class, 'index']);
$router->get('/projects/create', [ProjectController::class, 'createForm'], [AuthMiddleware::class]);
$router->post('/projects/create', [ProjectController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/projects/images/{id}', [ProjectMediaController::class, 'show']);
$router->get('/projects/edit/{id}', [ProjectController::class, 'editForm'], [AuthMiddleware::class]);
$router->post('/projects/edit/{id}', [ProjectController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/publish', [ProjectController::class, 'publish'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/archive', [ProjectController::class, 'archive'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/feature', [ProjectController::class, 'feature'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/verification', [ProjectController::class, 'requestVerification'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/reaction', [ProjectController::class, 'react'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/{id}/images', [ProjectMediaController::class, 'upload'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/projects/images/{id}/delete', [ProjectMediaController::class, 'delete'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/projects/{username}/{slug}', [ProjectController::class, 'show']);

$router->get('/admin/portfolio', [PortfolioAdminController::class, 'index'], [AuthMiddleware::class]);
$router->post('/admin/portfolio/{id}/feature', [PortfolioAdminController::class, 'featureOverride'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/admin/projects/verification', [ProjectVerificationController::class, 'index'], [AuthMiddleware::class]);
$router->post('/admin/projects/verification/{id}/accept', [ProjectVerificationController::class, 'accept'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/projects/verification/{id}/reject', [ProjectVerificationController::class, 'reject'], [AuthMiddleware::class, CsrfMiddleware::class]);
