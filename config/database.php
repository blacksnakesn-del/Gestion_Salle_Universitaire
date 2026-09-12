<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager;

return function (): Manager {
    $capsule = new Manager();

    $env = static function (string $key, string $default = ''): string {
        $value = $_ENV[$key] ?? getenv($key);

        return is_string($value) && $value !== '' ? $value : $default;
    };

    $capsule->addConnection([
        'driver' => $env('DB_DRIVER', 'mysql'),
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => $env('DB_PORT', '3306'),
        'database' => $env('DB_DATABASE'),
        'username' => $env('DB_USERNAME'),
        'password' => $env('DB_PASSWORD'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ]);

    $capsule->setAsGlobal();

    $capsule->bootEloquent();

    return $capsule;
};