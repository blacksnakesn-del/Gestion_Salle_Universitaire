<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\SalleDTO;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Security\Csrf;
use App\Validation\SalleValidator;

final class SalleController
{
    public function __construct(
        private SalleRepositoryInterface $salleRepository,
        private ReservationRepositoryInterface $reservationRepository,
        private SalleValidator $validator
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->findAll();
        $title = 'Salles';
        $get = $_GET ?? [];
        $success = $get['success'] ?? null;

        renderView('Salle/index', compact('salles', 'title', 'success'));
    }

    public function show(int $id): void
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            renderView('error/404');
            return;
        }

        $reservations = $this->reservationRepository->findBySalleId($id);
        $title = 'Salle ' . $salle->nom;

        $get = $_GET ?? [];
        $success = $get['success'] ?? null;
        $error = $get['error'] ?? null;

        renderView('Salle/show', compact('salle', 'reservations', 'title', 'success', 'error'));
    }

    public function create(): void
    {
        $title = 'Nouvelle salle';
        $errors = [];
        $old = [
            'nom' => '',
            'batiment' => '',
            'capacite' => '',
            'type' => '',
            'active' => '1',
        ];
        $formAction = '/salles';
        $formMethodLabel = 'Créer la salle';
        $csrfToken = Csrf::token();

        renderView('Salle/form', compact('title', 'errors', 'old', 'formAction', 'formMethodLabel', 'csrfToken'));
    }

    public function store(): void
    {
        $post = $_POST ?? [];
        Csrf::verify($post['_csrf_token'] ?? null);
        $data = $post;
        $validation = $this->validator->validate($data);
        $errors = $validation->getErrors();
        $old = $data;

        if (!$validation->isValid()) {
            $title = 'Nouvelle salle';
            $formAction = '/salles';
            $formMethodLabel = 'Créer la salle';
            $csrfToken = Csrf::token();
            renderView('Salle/form', compact('title', 'errors', 'old', 'formAction', 'formMethodLabel', 'csrfToken'));
            return;
        }

        $dto = new SalleDTO(
            nom: trim((string) ($data['nom'] ?? '')),
            batiment: trim((string) ($data['batiment'] ?? '')),
            capacite: (int) ($data['capacite'] ?? 0),
            type: (string) ($data['type'] ?? ''),
            active: isset($data['active']) && in_array((string) $data['active'], ['1', 'true'], true),
        );

        $this->salleRepository->create($dto);

        header('Location: /salles?success=' . rawurlencode('Salle créée avec succès.'));
        exit;
    }

    public function edit(int $id): void
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            renderView('error/404');
            return;
        }

        $title = 'Modifier ' . $salle->nom;
        $errors = [];
        $old = [
            'nom' => $salle->nom,
            'batiment' => $salle->batiment,
            'capacite' => (string) $salle->capacite,
            'type' => $salle->type,
            'active' => $salle->active ? '1' : '0',
        ];
        $formAction = '/salles/' . $salle->id . '/edit';
        $formMethodLabel = 'Enregistrer les modifications';
        $csrfToken = Csrf::token();

        renderView('Salle/form', compact('title', 'errors', 'old', 'formAction', 'formMethodLabel', 'csrfToken'));
    }

    public function update(int $id): void
    {
        $post = $_POST ?? [];
        Csrf::verify($post['_csrf_token'] ?? null);
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            renderView('error/404');
            return;
        }

        $data = $post;
        $validation = $this->validator->validate($data);
        $errors = $validation->getErrors();
        $old = $data;

        if (!$validation->isValid()) {
            $title = 'Modifier ' . $salle->nom;
            $formAction = '/salles/' . $id . '/edit';
            $formMethodLabel = 'Enregistrer les modifications';
            $csrfToken = Csrf::token();
            renderView('Salle/form', compact('title', 'errors', 'old', 'formAction', 'formMethodLabel', 'csrfToken'));
            return;
        }

        $dto = new SalleDTO(
            nom: trim((string) ($data['nom'] ?? '')),
            batiment: trim((string) ($data['batiment'] ?? '')),
            capacite: (int) ($data['capacite'] ?? 0),
            type: (string) ($data['type'] ?? ''),
            active: isset($data['active']) && in_array((string) $data['active'], ['1', 'true'], true),
        );

        $this->salleRepository->update($id, $dto);

        header('Location: /salles/' . $id . '?success=' . rawurlencode('Salle modifiée avec succès.'));
        exit;
    }
}
