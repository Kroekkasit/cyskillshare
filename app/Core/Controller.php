<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    /**
     * @param array<string, mixed> $old
     */
    protected function withErrors(array $errors, array $old = []): void
    {
        Session::flash('errors', $errors);
        if ($old !== []) {
            Session::flash('_old_input', $old);
        }
    }

    protected function withSuccess(string $message): void
    {
        Session::flash('success', $message);
    }

    protected function withError(string $message): void
    {
        Session::flash('error', $message);
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::validateRequest()) {
            http_response_code(419);
            Session::flash('error', 'Invalid or missing CSRF token. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            redirect($referer);
        }
    }
}
