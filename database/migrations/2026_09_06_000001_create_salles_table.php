<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return function (Capsule $capsule): void {
    $schema = $capsule->schema();

    $schema->create('salles', function ($table): void {
        $table->id();

        $table->string('nom');

        $table->string('batiment');

        $table->unsignedInteger('capacite');

        $table->enum('type', [
            'cours',
            'informatique',
            'laboratoire',
            'amphitheatre',
            'reunion',
        ]);

        $table->boolean('active')->default(true);

        $table->timestamps();
    });
};
