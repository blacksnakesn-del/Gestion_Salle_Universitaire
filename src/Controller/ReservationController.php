<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ReservationDTO;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use App\Service\AnnulerReservationService;
use App\Service\ReservationService;
use App\Validation\ReservationValidator;
use DateTimeImmutable;
use Throwable;

final class ReservationController
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private SalleRepository $salleRepository,
        private ReservationValidator $validator,
        private ReservationService $reservationService,
        private AnnulerReservationService $annulerReservationService
    ) {
    }

    public function index(): void
    {
        $salleId = filter_input(INPUT_GET, 'salle_id', FILTER_VALIDATE_INT);

        if ($salleId !== false && $salleId !== null && $salleId > 0) {
            $reservations = $this->reservationRepository->findBySalleId($salleId);
            $selectedSalle = $this->salleRepository->findById($salleId);
        } else {
            $reservations = $this->reservationRepository->findAll();
            $selectedSalle = null;
        }

        $salles = $this->salleRepository->findAll();
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;
        $title = 'Réservations';

        require __DIR__ . '/../../templates/reservation/index.php';
    }

    public function show(int $id): void
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            http_response_code(404);
            require __DIR__ . '/../../templates/error/404.php';
            return;
        }

        $title = 'Réservation #' . $reservation->id;

        require __DIR__ . '/../../templates/reservation/show.php';
    }

    public function create(): void
    {
        $title = 'Nouvelle réservation';
        $salles = $this->salleRepository->findActive();
        $errors = [];
        $serviceError = null;
        $old = [
            'salle_id' => '',
            'responsable' => '',
            'email' => '',
            'motif' => '',
            'date_debut' => '',
            'date_fin' => '',
        ];

        require __DIR__ . '/../../templates/reservation/form.php';
    }

    public function store(): void
    {
        $data = $_POST;

        // datetime-local produit Y-m-d\TH:i ; le validator travaille en Y-m-d H:i:s.
        $data['date_debut'] = $this->normalizeDateInput($data['date_debut'] ?? '');
        $data['date_fin'] = $this->normalizeDateInput($data['date_fin'] ?? '');

        $errors = $this->validator->validate($data);
        $old = $_POST;

        if ($errors !== []) {
            $title = 'Nouvelle réservation';
            $salles = $this->salleRepository->findActive();
            $serviceError = null;
            require __DIR__ . '/../../templates/reservation/form.php';
            return;
        }

        try {
            $dto = new ReservationDTO(
                salleId: (int) $data['salle_id'],
                responsable: trim((string) $data['responsable']),
                email: trim((string) $data['email']),
                motif: trim((string) $data['motif']),
                dateDebut: new DateTimeImmutable((string) $data['date_debut']),
                dateFin: new DateTimeImmutable((string) $data['date_fin']),
            );

            $reservation = $this->reservationService->createReservation($dto);

            header('Location: /reservations/' . $reservation->id . '?success=' . rawurlencode('Réservation créée avec succès.'));
            exit;
        } catch (Throwable $e) {
            $title = 'Nouvelle réservation';
            $salles = $this->salleRepository->findActive();
            $serviceError = $e->getMessage();
            require __DIR__ . '/../../templates/reservation/form.php';
        }
    }

    public function cancel(int $id): void
    {
        try {
            $this->annulerReservationService->cancel($id);

            header('Location: /reservations/' . $id . '?success=' . rawurlencode('Réservation annulée avec succès.'));
            exit;
        } catch (Throwable $e) {
            header('Location: /reservations/' . $id . '?error=' . rawurlencode($e->getMessage()));
            exit;
        }
    }

    private function normalizeDateInput(mixed $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $value = str_replace('T', ' ', $value);

        if (strlen($value) === 16) {
            return $value . ':00';
        }

        return $value;
    }
}
