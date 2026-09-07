<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

final class SalleRepository
{
    /**
     * Récupère toutes les salles.
     */
    public function findAll(): Collection
    {
        return Salle::all();
    }

    /**
     * Recherche une salle par son identifiant.
     */
    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    /**
     * Récupère uniquement les salles actives.
     */
    public function findActive(): Collection
    {
        return Salle::where('active', true)->get();
    }
}
