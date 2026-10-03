<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\BookmarkController;
use App\Controllers\CommunityController;
use App\Controllers\HomeController;
use App\Controllers\ModerationController;
use App\Controllers\NotificationController;
use App\Controllers\ProfileController;
use App\Controllers\ReplyController;
use App\Controllers\ReportController;
use App\Controllers\SearchController;
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

$router->get('/profile/{username}', [ProfileController::class, 'show']);
$router->post('/profile/{username}', [ProfileController::class, 'update'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->get('/u/{username}', [ProfileController::class, 'show']);
$router->get('/admin/demo', [ProfileController::class, 'adminDemo'], [AuthMiddleware::class]);
