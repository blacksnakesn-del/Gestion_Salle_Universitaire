<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return function (Capsule $capsule): void {
    $schema = $capsule->schema();

    $schema->create('reservations', function ($table): void {
        $table->id();

        $table->foreignId('salle_id')
            ->constrained('salles')
            ->cascadeOnDelete();

        $table->string('responsable');

        $table->string('email');

        $table->string('motif');

        $table->dateTime('date_debut');

        $table->dateTime('date_fin');

        $table->enum('statut', [
            'confirmée',
            'annulée',
        ])->default('confirmée');

        $table->timestamps();
    });
};
