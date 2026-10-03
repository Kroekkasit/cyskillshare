<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class ErrorHandler
{
    public static function register(): void
    {
        $debug = (bool) config('app.debug', false);

        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleException(Throwable $e): void
    {
        self::log(sprintf(
            '[%s] %s in %s:%d',
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));
        self::log($e->getTraceAsString());

        http_response_code(500);

        if (config('app.debug')) {
            echo '<h1>Application Error</h1>';
            echo '<pre>' . e((string) $e) . '</pre>';
            return;
        }

        if (class_exists(View::class) && is_file(view_path('pages/errors/500.php'))) {
            View::render('pages/errors/500', [
                'title' => 'Server Error',
                'message' => 'Something went wrong. Please try again later.',
            ]);
            return;
        }

        echo 'An unexpected error occurred.';
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }

    public static function log(string $message): void
    {
        $dir = storage_path('logs');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($dir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }
}
