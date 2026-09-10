<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\SalleDTO;
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

    public function create(SalleDTO $dto): Salle
    {
        return Salle::query()->create([
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

    public function findForUpdate(int $id): ?Salle
    {
        return Salle::query()->whereKey($id)->lockForUpdate()->first();
    }
}