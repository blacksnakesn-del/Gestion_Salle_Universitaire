<?php

declare(strict_types=1);

use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager;
use function DI\autowire;
use function DI\factory;
use function FastRoute\simpleDispatcher;

return [
    Manager::class => factory(static function (): Manager {
        if (is_file(dirname(__DIR__) . '/.env')) {
            Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
        }

        $database = require dirname(__DIR__) . '/config/database.php';

        return $database();
    }),
    SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),
    Dispatcher::class => factory(static function (): Dispatcher {
        return simpleDispatcher(static function (RouteCollector $routes): void {
            $definition = require dirname(__DIR__) . '/routes/web.php';
            $definition($routes);
        });
    }),
];