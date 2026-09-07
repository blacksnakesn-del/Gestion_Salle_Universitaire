<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Validation\SalleValidator;
use App\Validation\ReservationValidator;

$salleValidator = new SalleValidator();

$salle = [
    'nom' => 'Salle B12',
    'batiment' => 'B',
    'capacite' => 40,
    'type' => 'cours',
];

$errors = $salleValidator->validate($salle);

echo "=== TEST SALLE ===" . PHP_EOL;

if (empty($errors)) {
    echo "Salle valide." . PHP_EOL;
} else {
    print_r($errors);
}


$reservationValidator = new ReservationValidator();

$reservation = [
    'salle_id' => 1,
    'responsable' => 'Abdou Kebe',
    'email' => 'abdou@example.com',
    'motif' => 'Cours de programmation',
    'date_debut' => '2026-09-10 09:00:00',
    'date_fin' => '2026-09-10 11:00:00',
];

$errors = $reservationValidator->validate($reservation);

echo PHP_EOL;
echo "=== TEST RESERVATION ===" . PHP_EOL;

if (empty($errors)) {
    echo "Réservation valide." . PHP_EOL;
} else {
    print_r($errors);
}
