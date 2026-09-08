<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ReservationDTO;
use App\Model\Reservation;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use DateTimeImmutable;
use RuntimeException;

final class ReservationService
{
    public function __construct(
        private SalleRepository $salleRepository,
        private ReservationRepository $reservationRepository
    ) {
    }

    public function createReservation(
        ReservationDTO $dto
    ): Reservation {
        // 1. Vérifier que la salle existe
        $salle = $this->salleRepository->findById($dto->salleId);

        if ($salle === null) {
            throw new RuntimeException(
                'La salle demandée n’existe pas.'
            );
        }

        // 2. Vérifier que la salle est active
        if (!$salle->active) {
            throw new RuntimeException(
                'Cette salle est inactive et ne peut pas être réservée.'
            );
        }

        // 3. Vérifier que la date de début est avant la date de fin
        if ($dto->dateDebut >= $dto->dateFin) {
            throw new RuntimeException(
                'La date de début doit être antérieure à la date de fin.'
            );
        }

        // 4. Vérifier que la réservation ne dépasse pas 4 heures
        $duree = $dto->dateFin->getTimestamp()
            - $dto->dateDebut->getTimestamp();

        if ($duree > 4 * 60 * 60) {
            throw new RuntimeException(
                'La réservation ne peut pas dépasser 4 heures.'
            );
        }

        // 5. Vérifier que la réservation commence dans le futur
        $maintenant = new DateTimeImmutable();

        if ($dto->dateDebut <= $maintenant) {
            throw new RuntimeException(
                'La réservation doit commencer dans le futur.'
            );
        }

        // 6. Vérifier les chevauchements
        if (
            $this->reservationRepository->hasOverlap(
                $dto->salleId,
                $dto->dateDebut,
                $dto->dateFin
            )
        ) {
            throw new RuntimeException(
                'Cette salle est déjà réservée sur cette période.'
            );
        }

        // 7. Créer la réservation
        return $this->reservationRepository->create($dto);
    }
}
