<?php

declare(strict_types=1);

if (!function_exists('renderView')) {
    function renderView(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__, 2) . '/templates/' . trim($view, '/') . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException('Vue introuvable : ' . $view);
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }
}