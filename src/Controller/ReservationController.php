<?php

namespace App\Controller;

use App\Repository\ReservationRepository;

final class ReservationController
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository
    ) {
    }

    public function index(): void
    {
        $reservations = $this->reservationRepository->findAll();

        require __DIR__ . '/../../templates/reservation/index.php';
    }

    public function show(int $id): void
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            http_response_code(404);
            require __DIR__ . '/../../templates/errors/404.php';
            return;
        }

        require __DIR__ . '/../../templates/reservation/show.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];

        require __DIR__ . '/../../templates/reservation/form.php';
    }
}