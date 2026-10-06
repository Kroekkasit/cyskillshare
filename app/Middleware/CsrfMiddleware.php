<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;
use App\Services\ActivityLogService;

/**
 * Reject state-changing requests without a valid CSRF token.
 */
final class CsrfMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (!Csrf::validateRequest()) {
                ActivityLogService::log(Auth::id(), 'csrf_rejected', 'request', null, [
                    'path' => $request->path(),
                    'method' => $request->method(),
                ]);
                http_response_code(419);
                Session::flash('error', 'Invalid or missing CSRF token. Please try again.');
                $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                redirect($referer);
            }
        }

        return $next($request);
    }
}
