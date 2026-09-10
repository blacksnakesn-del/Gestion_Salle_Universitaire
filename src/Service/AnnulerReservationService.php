<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepository;
use RuntimeException;

final class AnnulerReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository
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

        $reservation->statut = 'annulée';
        $reservation->save();
    }
}