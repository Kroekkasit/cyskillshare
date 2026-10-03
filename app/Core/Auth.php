<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Services\ActivityLogService;

/**
 * Server-side authentication & authorization facade.
 * Never trust role/user_id from the browser.
 */
final class Auth
{
    private const USER_ID_KEY = 'user_id';
    private const ROLES_KEY = 'user_roles';

    public static function check(): bool
    {
        return self::id() !== null;
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function id(): ?int
    {
        $id = Session::get(self::USER_ID_KEY);
        return is_int($id) ? $id : (is_numeric($id) ? (int) $id : null);
    }

    public static function user(): ?User
    {
        $id = self::id();
        if ($id === null) {
            return null;
        }

        return User::find($id);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect('/login');
        }
    }

    /**
     * @return list<string>
     */
    public static function roles(): array
    {
        $roles = Session::get(self::ROLES_KEY, []);
        return is_array($roles) ? array_values(array_map('strval', $roles)) : [];
    }

    public static function hasRole(string $role): bool
    {
        return in_array($role, self::roles(), true);
    }

    /**
     * @param list<string> $roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (self::hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    public static function requireRole(string $role): void
    {
        self::requireLogin();
        if (!self::hasRole($role)) {
            http_response_code(403);
            View::render('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.',
            ]);
            exit;
        }
    }

    /**
     * @param list<string> $roles
     */
    public static function requireAnyRole(array $roles): void
    {
        self::requireLogin();
        if (!self::hasAnyRole($roles)) {
            http_response_code(403);
            View::render('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.',
            ]);
            exit;
        }
    }

    /**
     * Ownership check with moderator/admin override.
     *
     * @param list<string> $elevatedRoles
     */
    public static function canManage(int $ownerId, array $elevatedRoles = ['moderator', 'admin']): bool
    {
        if (!self::check()) {
            return false;
        }

        if (self::id() === $ownerId) {
            return true;
        }

        return self::hasAnyRole($elevatedRoles);
    }

    public static function requireOwnership(int $ownerId, array $elevatedRoles = ['moderator', 'admin']): void
    {
        self::requireLogin();
        if (!self::canManage($ownerId, $elevatedRoles)) {
            http_response_code(403);
            View::render('pages/errors/403', [
                'title' => 'Forbidden',
                'message' => 'You can only manage your own content.',
            ]);
            exit;
        }
    }

    public static function attempt(string $login, string $password): bool
    {
        $user = User::findByLogin($login);
        if ($user === null) {
            ActivityLogService::log(null, 'failed_login', 'user', null, [
                'login' => $login,
            ]);
            return false;
        }

        if ($user->status !== 'active') {
            ActivityLogService::log($user->id, 'failed_login', 'user', $user->id, [
                'reason' => 'inactive_status',
                'status' => $user->status,
            ]);
            return false;
        }

        if (!password_verify($password, $user->password_hash)) {
            ActivityLogService::log($user->id, 'failed_login', 'user', $user->id, [
                'reason' => 'invalid_password',
            ]);
            return false;
        }

        self::login($user);
        return true;
    }

    public static function login(User $user): void
    {
        Session::regenerate(true);
        Session::set(self::USER_ID_KEY, $user->id);
        Session::set(self::ROLES_KEY, $user->roleNames());

        User::touchLastLogin($user->id);
        ActivityLogService::log($user->id, 'login', 'user', $user->id);
    }

    public static function logout(): void
    {
        $userId = self::id();
        if ($userId !== null) {
            ActivityLogService::log($userId, 'logout', 'user', $userId);
        }

        Session::remove(self::USER_ID_KEY);
        Session::remove(self::ROLES_KEY);
        Csrf::regenerate();
        Session::destroy();
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function refreshRoles(): void
    {
        $user = self::user();
        if ($user !== null) {
            Session::set(self::ROLES_KEY, $user->roleNames());
        }
    }
}
