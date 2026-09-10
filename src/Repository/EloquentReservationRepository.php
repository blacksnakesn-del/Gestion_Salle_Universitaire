<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\ReservationDTO;
use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;

final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function findAll(): Collection
    {
        return Reservation::query()->with('salle')->orderBy('date_debut')->get();
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::query()->with('salle')->find($id);
    }

    public function hasOverlap(int $salleId, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): bool
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $dateFin)
            ->where('date_fin', '>', $dateDebut)
            ->exists();
    }

    public function findBySalleId(int $salleId): Collection
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->with('salle')
            ->orderBy('date_debut')
            ->get();
    }

    public function create(ReservationDTO $dto): Reservation
    {
        return Reservation::query()->create([
            'salle_id' => $dto->salleId,
            'responsable' => $dto->responsable,
            'email' => $dto->email,
            'motif' => $dto->motif,
            'date_debut' => $dto->dateDebut,
            'date_fin' => $dto->dateFin,
            'statut' => $dto->statut,
        ]);
    }

    public function cancel(Reservation $reservation): Reservation
    {
        $reservation->statut = 'annulée';
        $reservation->save();

        return $reservation;
    }
}