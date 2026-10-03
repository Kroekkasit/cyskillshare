<?php

declare(strict_types=1);

/**
 * Application bootstrap.
 */

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = BASE_PATH . '/app/' . $relative . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

require_once BASE_PATH . '/app/Helpers/functions.php';

\App\Core\Env::load(BASE_PATH . '/.env');
\App\Core\Config::load(BASE_PATH . '/config');

date_default_timezone_set((string) config('app.timezone', 'Asia/Bangkok'));

\App\Core\ErrorHandler::register();
\App\Core\Session::start();

$app = new \App\Core\App();
$router = $app->router();

require BASE_PATH . '/routes/web.php';

return $app;
