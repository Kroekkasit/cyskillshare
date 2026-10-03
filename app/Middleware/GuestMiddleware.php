<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;

final class GuestMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (Auth::check()) {
            redirect('/');
        }

        return $next($request);
    }
}
