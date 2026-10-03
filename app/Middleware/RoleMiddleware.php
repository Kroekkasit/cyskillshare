<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;

final class RoleMiddleware
{
    /** @var list<string> */
    private array $roles;

    /**
     * @param list<string> $roles
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle(Request $request, callable $next): mixed
    {
        Auth::requireLogin();

        if (!Auth::hasAnyRole($this->roles)) {
            http_response_code(403);
            View::render('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.',
            ]);
            exit;
        }

        return $next($request);
    }
}
