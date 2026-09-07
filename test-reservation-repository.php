<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Model\Reservation;
use App\Repository\ReservationRepository;
use DateTimeImmutable;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = require __DIR__ . '/config/database.php';
$database();

$repository = new ReservationRepository();

/*
 * Création d'une réservation de test.
 */
$reservation = Reservation::create([
    'salle_id' => 1,
    'responsable' => 'Test Repository',
    'email' => 'test@example.com',
    'motif' => 'Test du chevauchement',
    'date_debut' => '2026-09-10 09:00:00',
    'date_fin' => '2026-09-10 11:00:00',
    'statut' => 'confirmée',
]);

echo "Réservation de test créée avec l'ID : {$reservation->id}" . PHP_EOL;

echo PHP_EOL;
echo "=== TEST 1 : CHEVAUCHEMENT ===" . PHP_EOL;

$overlap = $repository->hasOverlap(
    1,
    new DateTimeImmutable('2026-09-10 10:00:00'),
    new DateTimeImmutable('2026-09-10 12:00:00')
);

echo $overlap
    ? "Chevauchement détecté." . PHP_EOL
    : "Aucun chevauchement." . PHP_EOL;

echo PHP_EOL;
echo "=== TEST 2 : RESERVATION ADJACENTE ===" . PHP_EOL;

$overlap = $repository->hasOverlap(
    1,
    new DateTimeImmutable('2026-09-10 11:00:00'),
    new DateTimeImmutable('2026-09-10 13:00:00')
);

echo $overlap
    ? "Chevauchement détecté." . PHP_EOL
    : "Aucun chevauchement." . PHP_EOL;
