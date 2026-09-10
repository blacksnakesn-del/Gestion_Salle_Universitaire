<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ReservationDTO;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Security\Csrf;
use App\Service\AnnulerReservationService;
use App\Service\ReservationService;
use App\Validation\ReservationValidator;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class ReservationController
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private SalleRepositoryInterface $salleRepository,
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

        renderView('reservation/index', compact('reservations', 'salles', 'selectedSalle', 'success', 'error', 'title'));
    }

    public function show(int $id): void
    {
        $reservation = $this->reservationRepository->findById($id);

        if ($reservation === null) {
            http_response_code(404);
            renderView('error/404');
            return;
        }

        $title = 'Réservation #' . $reservation->id;
        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;
        $csrfToken = Csrf::token();

        renderView('reservation/show', compact('reservation', 'title', 'success', 'error', 'csrfToken'));
    }

    public function create(): void
    {
        $title = 'Nouvelle réservation';
        $salles = $this->salleRepository->findActive();
        $errors = [];
        $serviceError = null;
        $csrfToken = Csrf::token();
        $selectedSalleId = filter_input(INPUT_GET, 'salle_id', FILTER_VALIDATE_INT);
        $old = [
            'salle_id' => $selectedSalleId !== false && $selectedSalleId !== null && $selectedSalleId > 0
                ? (string) $selectedSalleId
                : '',
            'responsable' => '',
            'email' => '',
            'motif' => '',
            'date_debut' => '',
            'date_fin' => '',
        ];

        renderView('reservation/form', compact('title', 'salles', 'errors', 'serviceError', 'csrfToken', 'old'));
    }

    public function store(): void
    {
        $data = $_POST;

        // datetime-local produit Y-m-d\TH:i ; le validator travaille en Y-m-d H:i:s.
        $data['date_debut'] = $this->normalizeDateInput($data['date_debut'] ?? '');
        $data['date_fin'] = $this->normalizeDateInput($data['date_fin'] ?? '');

        $validation = $this->validator->validate($data);
        $errors = $validation->getErrors();
        $old = $_POST;

        if (!$validation->isValid()) {
            $title = 'Nouvelle réservation';
            $salles = $this->salleRepository->findActive();
            $serviceError = null;
            $csrfToken = Csrf::token();
            renderView('reservation/form', compact('title', 'salles', 'errors', 'serviceError', 'csrfToken', 'old'));
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
            error_log((string) $e);
            $title = 'Nouvelle réservation';
            $salles = $this->salleRepository->findActive();
            $csrfToken = Csrf::token();
            $serviceError = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Une erreur est survenue lors de la création de la réservation.';
            renderView('reservation/form', compact('title', 'salles', 'errors', 'serviceError', 'csrfToken', 'old'));
        }
    }

    public function cancel(int $id): void
    {
        try {
            Csrf::verify($_POST['_csrf_token'] ?? null);
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
