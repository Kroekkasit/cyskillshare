<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CommunityController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\ThreadController;
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

$router->get('/community', [CommunityController::class, 'index']);
$router->get('/community/{channel}', [CommunityController::class, 'show']);

$router->get('/thread/{id}', [ThreadController::class, 'show']);
$router->post('/thread/create', [ThreadController::class, 'create'], [AuthMiddleware::class, CsrfMiddleware::class]);
$router->post('/thread/{id}/reply', [ThreadController::class, 'reply'], [AuthMiddleware::class, CsrfMiddleware::class]);

$router->get('/u/{username}', [ProfileController::class, 'show']);
$router->get('/admin/demo', [ProfileController::class, 'adminDemo'], [AuthMiddleware::class]);
