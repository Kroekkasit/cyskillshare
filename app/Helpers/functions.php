<?php

declare(strict_types=1);

/**
 * Global helper functions for CySkillShare.
 */

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return base_path('resources/views' . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : ''));
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return \App\Core\Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return \App\Core\Config::get($key, $default);
    }
}

if (!function_exists('e')) {
    /**
     * Escape a value for safe HTML output (XSS protection).
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $base . ($path === '/' ? '' : $path);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        $location = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : url($path);

        header('Location: ' . $location);
        exit;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        static $old = null;
        if ($old === null) {
            $old = \App\Core\Session::getFlash('_old_input', []);
            if (!is_array($old)) {
                $old = [];
            }
        }
        return $old[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \App\Core\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('now')) {
    function now(): string
    {
        return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
    }
}

if (!function_exists('time_ago')) {
    function time_ago(string $datetime): string
    {
        try {
            $then = new DateTimeImmutable($datetime);
        } catch (Exception) {
            return $datetime;
        }

        $diff = (new DateTimeImmutable('now'))->getTimestamp() - $then->getTimestamp();
        if ($diff < 60) {
            return 'just now';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return $m . ' min ago';
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return $h . 'h ago';
        }
        if ($diff < 604800) {
            $d = (int) floor($diff / 86400);
            return $d . 'd ago';
        }
        return $then->format('M j, Y');
    }
}

if (!function_exists('markdown')) {
    function markdown(string $content): string
    {
        return \App\Core\ContentFormatter::render($content);
    }
}

if (!function_exists('excerpt')) {
    function excerpt(string $content, int $length = 180): string
    {
        return \App\Core\ContentFormatter::excerpt($content, $length);
    }
}

if (!function_exists('request_path')) {
    function request_path(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = is_string($path) ? rawurldecode($path) : '/';
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
