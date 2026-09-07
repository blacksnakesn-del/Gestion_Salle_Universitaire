<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;

final class ReservationRepository
{
    /**
     * Récupère toutes les réservations.
     */
    public function findAll(): Collection
    {
        return Reservation::all();
    }

    /**
     * Recherche une réservation par son identifiant.
     */
    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    /**
     * Vérifie si une salle possède déjà une réservation
     * confirmée sur la période demandée.
     */
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

    /**
     * Récupère les réservations d'une salle.
     */
    public function findBySalleId(int $salleId): Collection
    {
        return Reservation::where('salle_id', $salleId)->get();
    }
}
