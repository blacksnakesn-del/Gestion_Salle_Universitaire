<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Model\Salle;

// Initialiser la base de données
require __DIR__ . '/config/Database.php';

$salle = Salle::create([
    'nom' => 'Salle Test',
    'batiment' => 'Bâtiment A',
    'capacite' => 30,
    'type' => 'cours',
    'active' => true,
]);

echo 'Salle créée avec ID : ' . $salle->id;