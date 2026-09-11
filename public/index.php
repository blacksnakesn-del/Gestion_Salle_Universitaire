<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use Illuminate\Database\Capsule\Manager;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');
$container = $builder->build();
$container->get(Manager::class);
$dispatcher = $container->get(Dispatcher::class);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) && $path !== '' ? $path : '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$routeInfo = $dispatcher->dispatch($method, $path);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        renderView('error/404');
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('Allow: ' . implode(', ', $allowedMethods));
        http_response_code(405);
        renderView('error/405');
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if ($handler instanceof Closure) {
            $handler(...array_values($vars));
            break;
        }

        [$controllerClass, $action] = $handler;
        $controller = $container->get($controllerClass);
        $controller->{$action}(...array_values($vars));
        break;

    default:
        http_response_code(500);
        echo 'Erreur interne du routeur.';
        break;
}
