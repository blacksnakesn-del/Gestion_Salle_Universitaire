<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;

return function (Capsule $capsule): void {
    $schema = $capsule->schema();

    if ($schema->hasTable('users')) {
        return;
    }

    $schema->create('users', function ($table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });
};
