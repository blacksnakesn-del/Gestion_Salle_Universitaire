<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Exception\ReservationIntrouvableException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use DateTimeImmutable;
use RuntimeException;

final class ReservationService
{
    public function __construct(
        private SalleRepositoryInterface $salles,
        private ReservationRepositoryInterface $reservations
    ) {
    }

    public function createReservation(
        ReservationDTO $dto
    ): Reservation {
        // 1. Vérifier que la salle existe
        $salle = $this->salles->findById($dto->salleId);

        if ($salle === null) {
            throw new ReservationIntrouvableException(
                'La salle demandée n’existe pas.'
            );
        }

        // 2. Vérifier que la salle est active
        if (!$salle->active) {
            throw new SalleIndisponibleException(
                'Cette salle est inactive et ne peut pas être réservée.'
            );
        }

        // 3. Vérifier que le début précède la fin
        if ($dto->dateDebut >= $dto->dateFin) {
            throw new RuntimeException(
                'La date de début doit être antérieure à la date de fin.'
            );
        }

        // 4. Vérifier la durée maximale
        $duree = $dto->dateFin->getTimestamp()
            - $dto->dateDebut->getTimestamp();

        if ($duree > 4 * 60 * 60) {
            throw new RuntimeException(
                'La réservation ne peut pas dépasser 4 heures.'
            );
        }

        // 5. Vérifier que la réservation est future
        $maintenant = new DateTimeImmutable();

        if ($dto->dateDebut <= $maintenant) {
            throw new RuntimeException(
                'La réservation doit commencer dans le futur.'
            );
        }

        // 6. Vérifier les chevauchements
        if (
            $this->reservations->hasOverlap(
                $dto->salleId,
                $dto->dateDebut,
                $dto->dateFin
            )
        ) {
            throw new SalleIndisponibleException(
                'Cette salle est déjà réservée sur cette période.'
            );
        }

        // 7. Créer et enregistrer la réservation
        return $this->reservations->create($dto);
    }

    public function cancelReservation(int $id): Reservation
    {
        $reservation = $this->reservations->findById($id);

        if ($reservation === null) {
            throw new ReservationIntrouvableException(
                'La réservation demandée n’existe pas.'
            );
        }

        return $this->reservations->cancel($reservation);
    }
}