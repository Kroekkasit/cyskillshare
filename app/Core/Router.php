<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var list<array{methods: list<string>, pattern: string, handler: callable|array{0: class-string, 1: string}, middleware: list<string|callable>}> */
    private array $routes = [];

    /**
     * @param callable|array{0: class-string, 1: string} $handler
     * @param list<string|callable> $middleware
     */
    public function get(string $uri, callable|array $handler, array $middleware = []): void
    {
        $this->add(['GET'], $uri, $handler, $middleware);
    }

    /**
     * @param callable|array{0: class-string, 1: string} $handler
     * @param list<string|callable> $middleware
     */
    public function post(string $uri, callable|array $handler, array $middleware = []): void
    {
        $this->add(['POST'], $uri, $handler, $middleware);
    }

    /**
     * @param list<string> $methods
     * @param callable|array{0: class-string, 1: string} $handler
     * @param list<string|callable> $middleware
     */
    public function add(array $methods, string $uri, callable|array $handler, array $middleware = []): void
    {
        $pattern = $this->compile($uri);
        $this->routes[] = [
            'methods' => array_map('strtoupper', $methods),
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    private function compile(string $uri): string
    {
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') {
            return '#^/$#';
        }

        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $uri);
        return '#^' . $regex . '$#';
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if (!in_array($request->method(), $route['methods'], true)) {
                continue;
            }

            if (!preg_match($route['pattern'], $request->path(), $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            $request->setParams($params);

            $this->runMiddleware($route['middleware'], $request, function () use ($route, $request) {
                $this->invoke($route['handler'], $request);
            });
            return;
        }

        http_response_code(404);
        View::render('pages/errors/404', [
            'title' => 'Page Not Found',
            'message' => 'The page you requested could not be found.',
        ]);
    }

    /**
     * @param list<string|callable> $middleware
     */
    private function runMiddleware(array $middleware, Request $request, callable $next): void
    {
        $pipeline = array_reduce(
            array_reverse($middleware),
            function (callable $next, string|callable $mw) {
                return function (Request $request) use ($mw, $next) {
                    if (is_string($mw)) {
                        $instance = new $mw();
                        if (!method_exists($instance, 'handle')) {
                            throw new \RuntimeException("Middleware {$mw} must implement handle().");
                        }
                        return $instance->handle($request, $next);
                    }
                    return $mw($request, $next);
                };
            },
            $next
        );

        $pipeline($request);
    }

    /**
     * @param callable|array{0: class-string, 1: string} $handler
     */
    private function invoke(callable|array $handler, Request $request): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $controller->{$method}($request);
            return;
        }

        $handler($request);
    }
}
