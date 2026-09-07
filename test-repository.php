<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Repository\SalleRepository;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = require __DIR__ . '/config/database.php';
$database();

$repository = new SalleRepository();

echo "=== TOUTES LES SALLES ===" . PHP_EOL;

$salles = $repository->findAll();

foreach ($salles as $salle) {
    echo "- {$salle->id} : {$salle->nom} ({$salle->capacite} places)" . PHP_EOL;
}

echo PHP_EOL;

echo "=== SALLES ACTIVES ===" . PHP_EOL;

$sallesActives = $repository->findActive();

foreach ($sallesActives as $salle) {
    echo "- {$salle->nom}" . PHP_EOL;
}

echo PHP_EOL;

echo "=== RECHERCHE SALLE ID 1 ===" . PHP_EOL;

$salle = $repository->findById(1);

if ($salle !== null) {
    echo "Salle trouvée : {$salle->nom}" . PHP_EOL;
} else {
    echo "Salle introuvable." . PHP_EOL;
}
