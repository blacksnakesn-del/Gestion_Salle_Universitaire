<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

interface SalleRepositoryInterface
{
    
    public function findAll(): Collection;

    public function findById(int $id): ?Salle;

    public function findActive(): Collection;

    public function save(Salle $salle): Salle;
}