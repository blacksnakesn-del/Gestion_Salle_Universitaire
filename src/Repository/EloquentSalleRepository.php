<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    
    public function findAll(): Collection
    {
        return Salle::query()
            ->orderBy('nom')
            ->get();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }

    public function findActive(): Collection
    {
        return Salle::query()
            ->where('active', true)
            ->orderBy('nom')
            ->get();
    }

    public function save(Salle $salle): Salle
    {
        $salle->save();

        return $salle;
    }
}