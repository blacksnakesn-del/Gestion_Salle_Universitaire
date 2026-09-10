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

$migrationsPath = __DIR__ . '/migrations';

$files = glob($migrationsPath . '/*.php');

sort($files);

foreach ($files as $file) {
    echo "Migration : " . basename($file) . PHP_EOL;

    $migration = require $file;

    if (!is_callable($migration)) {
        throw new RuntimeException(
            "La migration " . basename($file) . " est invalide."
        );
    }

    $migration($capsule);

    echo "Migration exécutée avec succès." . PHP_EOL;
}

echo PHP_EOL;
echo "Toutes les migrations ont été exécutées." . PHP_EOL;
