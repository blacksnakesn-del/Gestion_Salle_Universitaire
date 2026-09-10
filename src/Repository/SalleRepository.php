<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\SalleDTO;
use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

final class SalleRepository
{
    
    public function findAll(): Collection
    {
        return Salle::all();
    }

    
    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    
    public function findActive(): Collection
    {
        return Salle::where('active', true)->get();
    }

    public function create(SalleDTO $dto): Salle
    {
        return Salle::create([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);
    }

    public function update(int $id, SalleDTO $dto): Salle
    {
        $salle = $this->findById($id);

        if ($salle === null) {
            throw new \RuntimeException('La salle demandée est introuvable.');
        }

        $salle->fill([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);
        $salle->save();

        return $salle;
    }
}
