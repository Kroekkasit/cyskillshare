<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private const CREATED_KEY = '_created_at';
    private const ACTIVITY_KEY = '_last_activity';

    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::enforceTimeout();
            return;
        }

        $name = (string) config('app.session.name', 'cyskillshare_session');
        $lifetime = max(60, (int) config('app.session.lifetime', 7200));
        $secure = (bool) config('app.session.secure', false);
        $sameSite = (string) config('app.session.same_site', 'Lax');
        $httpOnly = (bool) config('app.session.http_only', true);

        // Auto-enable Secure when request is HTTPS
        if (!$secure && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
            $secure = true;
        }

        // Align PHP GC with configured idle timeout
        ini_set('session.gc_maxlifetime', (string) $lifetime);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        session_name($name);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);

        session_start();
        self::$started = true;

        self::enforceTimeout();
    }

    /**
     * Idle session timeout: expire after SESSION_LIFETIME seconds without activity.
     */
    private static function enforceTimeout(): void
    {
        $lifetime = max(60, (int) config('app.session.lifetime', 7200));
        $now = time();

        if (!isset($_SESSION[self::CREATED_KEY])) {
            $_SESSION[self::CREATED_KEY] = $now;
        }

        $lastActivity = isset($_SESSION[self::ACTIVITY_KEY])
            ? (int) $_SESSION[self::ACTIVITY_KEY]
            : (int) $_SESSION[self::CREATED_KEY];

        if (isset($_SESSION[self::ACTIVITY_KEY]) && ($now - $lastActivity) > $lifetime) {
            $hadUser = isset($_SESSION['user_id']);
            $userId = $hadUser && is_numeric($_SESSION['user_id'])
                ? (int) $_SESSION['user_id']
                : null;

            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            $_SESSION[self::CREATED_KEY] = $now;
            $_SESSION[self::ACTIVITY_KEY] = $now;

            if ($hadUser) {
                if ($userId !== null && class_exists(\App\Services\ActivityLogService::class)) {
                    \App\Services\ActivityLogService::log($userId, 'session_timeout', 'user', $userId);
                }
                self::flash('error', 'Your session expired due to inactivity. Please log in again.');
            }

            return;
        }

        $_SESSION[self::ACTIVITY_KEY] = $now;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function regenerate(bool $deleteOld = true): void
    {
        session_regenerate_id($deleteOld);
        $_SESSION[self::ACTIVITY_KEY] = time();
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => (bool) $params['secure'],
                'httponly' => (bool) $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        self::$started = false;
    }
}
