<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;
use RuntimeException;

final class AnnulerReservationService
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    ) {
    }

    public function cancel(int $id): void
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            throw new ReservationIntrouvableException(
                'La réservation demandée est introuvable.'
            );
        }

        if ($reservation->statut === 'annulée') {
            throw new RuntimeException('Cette réservation est déjà annulée.');
        }

        $this->reservationRepository->cancel($reservation);
    }
}