<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;
use App\DTO\ReservationDTO;

final class ReservationRepository
{
     
    public function findAll(): Collection
    {
        return Reservation::all();
    }

    
    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    
    public function hasOverlap(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin
    ): bool {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $dateFin)
            ->where('date_fin', '>', $dateDebut)
            ->exists();
    }

    
    public function findBySalleId(int $salleId): Collection
    {
        return Reservation::where('salle_id', $salleId)->get();
    }

    public function create(ReservationDTO $dto): Reservation
    {
        return Reservation::create([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => $dto->statut,
        ]);
    }
}
