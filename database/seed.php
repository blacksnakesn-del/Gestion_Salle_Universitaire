<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
if (is_file(__DIR__ . '/../.env')) {
    $dotenv->load();
}

$database = require __DIR__ . '/../config/database.php';

$capsule = $database();

$salles = [
    [
        'nom' => 'Salle B12',
        'batiment' => 'B',
        'capacite' => 40,
        'type' => 'cours',
        'active' => true,
    ],
    [
        'nom' => 'Salle Informatique 1',
        'batiment' => 'A',
        'capacite' => 30,
        'type' => 'informatique',
        'active' => true,
    ],
    [
        'nom' => 'Laboratoire 1',
        'batiment' => 'C',
        'capacite' => 25,
        'type' => 'laboratoire',
        'active' => true,
    ],
    [
        'nom' => 'Amphithéâtre A',
        'batiment' => 'A',
        'capacite' => 200,
        'type' => 'amphitheatre',
        'active' => true,
    ],
    [
        'nom' => 'Salle Réunion 1',
        'batiment' => 'D',
        'capacite' => 15,
        'type' => 'reunion',
        'active' => true,
    ],
    [
        'nom' => 'Salle B13',
        'batiment' => 'B',
        'capacite' => 35,
        'type' => 'cours',
        'active' => false,
    ],
];

$capsule->table('salles')->insert($salles);

echo "Données initiales insérées avec succès." . PHP_EOL;
