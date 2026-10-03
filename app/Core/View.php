<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/app'): void
    {
        $viewFile = view_path(str_replace('.', '/', $view) . '.php');
        if (!is_file($viewFile)) {
            throw new RuntimeException("View [{$view}] not found.");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = view_path(str_replace('.', '/', $layout) . '.php');
        if (!is_file($layoutFile)) {
            throw new RuntimeException("Layout [{$layout}] not found.");
        }

        require $layoutFile;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function partial(string $view, array $data = []): void
    {
        $viewFile = view_path(str_replace('.', '/', $view) . '.php');
        if (!is_file($viewFile)) {
            throw new RuntimeException("Partial [{$view}] not found.");
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }
}
