<?php

declare(strict_types=1);

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Controller\AuthController;
use FastRoute\RouteCollector;

return static function (RouteCollector $r): void {
    $r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);
    $r->addRoute('GET', '/register', [AuthController::class, 'showRegister']);
    $r->addRoute('POST', '/register', [AuthController::class, 'register']);
    $r->addRoute('POST', '/logout', [AuthController::class, 'logout']);

    // Accueil : on conserve une seule entrée publique et on redirige vers les salles.
    $r->addRoute('GET', '/', static function (): void {
        header('Location: /salles');
        exit;
    });

    // Salles
    $r->addRoute('GET', '/salles', [SalleController::class, 'index']);
    $r->addRoute('GET', '/salles/create', [SalleController::class, 'create']);
    $r->addRoute('POST', '/salles', [SalleController::class, 'store']);
    $r->addRoute('GET', '/salles/{id:\\d+}', [SalleController::class, 'show']);
    $r->addRoute('GET', '/salles/{id:\\d+}/edit', [SalleController::class, 'edit']);
    $r->addRoute('POST', '/salles/{id:\\d+}/edit', [SalleController::class, 'update']);

    // Réservations
    $r->addRoute('GET', '/reservations', [ReservationController::class, 'index']);
    $r->addRoute('GET', '/reservations/create', [ReservationController::class, 'create']);
    $r->addRoute('POST', '/reservations', [ReservationController::class, 'store']);
    $r->addRoute('GET', '/reservations/{id:\\d+}', [ReservationController::class, 'show']);
    $r->addRoute('POST', '/reservations/{id:\\d+}/cancel', [ReservationController::class, 'cancel']);
};
