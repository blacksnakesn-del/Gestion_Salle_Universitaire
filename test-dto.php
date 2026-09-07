<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\DTO\SalleDTO;
use App\DTO\ReservationDTO;

$salle = new SalleDTO(
    nom: 'Salle B12',
    batiment: 'B',
    capacite: 40,
    type: 'cours',
);

echo "=== SALLE DTO ===" . PHP_EOL;
echo "Nom : {$salle->nom}" . PHP_EOL;
echo "Bâtiment : {$salle->batiment}" . PHP_EOL;
echo "Capacité : {$salle->capacite}" . PHP_EOL;
echo "Type : {$salle->type}" . PHP_EOL;

$reservation = new ReservationDTO(
    salleId: 1,
    responsable: 'Abdou Kebe',
    email: 'abdou@example.com',
    motif: 'Cours de programmation',
    dateDebut: new DateTimeImmutable('2026-09-10 09:00:00'),
    dateFin: new DateTimeImmutable('2026-09-10 11:00:00'),
);

echo PHP_EOL;
echo "=== RESERVATION DTO ===" . PHP_EOL;
echo "Salle : {$reservation->salleId}" . PHP_EOL;
echo "Responsable : {$reservation->responsable}" . PHP_EOL;
echo "Email : {$reservation->email}" . PHP_EOL;
echo "Motif : {$reservation->motif}" . PHP_EOL;
echo "Début : {$reservation->dateDebut->format('Y-m-d H:i:s')}" . PHP_EOL;
echo "Fin : {$reservation->dateFin->format('Y-m-d H:i:s')}" . PHP_EOL;
echo "Statut : {$reservation->statut}" . PHP_EOL;
