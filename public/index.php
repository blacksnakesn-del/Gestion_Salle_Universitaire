<?php

declare(strict_types=1);

use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\ReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use Dotenv\Dotenv;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Initialisation Eloquent / base de données.
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
if (is_file(dirname(__DIR__) . '/.env')) {
    $dotenv->load();
}

$database = require dirname(__DIR__) . '/config/database.php';
$database();

// Construction manuelle des dépendances.
// Elle sera centralisée dans PHP-DI lors de la Partie 11.
$salleRepository = new SalleRepository();
$reservationRepository = new ReservationRepository();
$salleValidator = new SalleValidator();
$reservationValidator = new ReservationValidator();
$reservationService = new ReservationService(
    $salleRepository,
    $reservationRepository
);
$annulerReservationService = new AnnulerReservationService(
    $reservationRepository
);

$salleController = new SalleController(
    $salleRepository,
    $salleValidator
);
$reservationController = new ReservationController(
    $reservationRepository,
    $salleRepository,
    $reservationValidator,
    $reservationService,
    $annulerReservationService
);

// FastRoute conserve les handlers sous forme [Classe, méthode].
// Le dispatch ci-dessous résout ensuite ces handlers vers les instances construites.
$dispatcher = simpleDispatcher(static function (RouteCollector $r): void {
    $routes = require dirname(__DIR__) . '/routes/web.php';
    $routes($r);
});

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) && $path !== '' ? $path : '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$routeInfo = $dispatcher->dispatch($method, $path);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        require dirname(__DIR__) . '/templates/error/404.php';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('Allow: ' . implode(', ', $allowedMethods));
        http_response_code(405);
        require dirname(__DIR__) . '/templates/error/405.php';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        if ($handler instanceof Closure) {
            $handler(...array_values($vars));
            break;
        }

        [$controllerClass, $action] = $handler;

        $controller = match ($controllerClass) {
            SalleController::class => $salleController,
            ReservationController::class => $reservationController,
            default => throw new RuntimeException('Controller non configuré : ' . $controllerClass),
        };

        $controller->{$action}(...array_values($vars));
        break;

    default:
        http_response_code(500);
        echo 'Erreur interne du routeur.';
        break;
}
