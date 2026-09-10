<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\SalleDTO;
use App\Repository\SalleRepository;
use App\Security\Csrf;
use App\Validation\SalleValidator;

final class SalleController
{
    public function __construct(
        private SalleRepository $salleRepository,
        private SalleValidator $validator
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->findAll();
        $title = 'Salles';
        $success = $_GET['success'] ?? null;

        require __DIR__ . '/../../templates/Salle/index.php';
    }

    public function show(int $id): void
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            require __DIR__ . '/../../templates/error/404.php';
            return;
        }

        $reservations = $salle->reservations()
            ->orderByDesc('date_debut')
            ->get();
        $title = 'Salle ' . $salle->nom;

        $success = $_GET['success'] ?? null;
        $error = $_GET['error'] ?? null;

        require __DIR__ . '/../../templates/Salle/show.php';
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

        require __DIR__ . '/../../templates/Salle/form.php';
    }

    public function store(): void
    {
        Csrf::verify($_POST['_csrf_token'] ?? null);
        $data = $_POST;
        $validation = $this->validator->validate($data);
        $errors = $validation->getErrors();
        $old = $data;

        if (!$validation->isValid()) {
            $title = 'Nouvelle salle';
            $formAction = '/salles';
            $formMethodLabel = 'Créer la salle';
            $csrfToken = Csrf::token();
            require __DIR__ . '/../../templates/Salle/form.php';
            return;
        }

        $dto = new SalleDTO(
            nom: trim((string) $data['nom']),
            batiment: trim((string) $data['batiment']),
            capacite: (int) $data['capacite'],
            type: (string) $data['type'],
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
            require __DIR__ . '/../../templates/error/404.php';
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

        require __DIR__ . '/../../templates/Salle/form.php';
    }

    public function update(int $id): void
    {
        Csrf::verify($_POST['_csrf_token'] ?? null);
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            require __DIR__ . '/../../templates/error/404.php';
            return;
        }

        $data = $_POST;
        $validation = $this->validator->validate($data);
        $errors = $validation->getErrors();
        $old = $data;

        if (!$validation->isValid()) {
            $title = 'Modifier ' . $salle->nom;
            $formAction = '/salles/' . $id . '/edit';
            $formMethodLabel = 'Enregistrer les modifications';
            $csrfToken = Csrf::token();
            require __DIR__ . '/../../templates/Salle/form.php';
            return;
        }

        $dto = new SalleDTO(
            nom: trim((string) $data['nom']),
            batiment: trim((string) $data['batiment']),
            capacite: (int) $data['capacite'],
            type: (string) $data['type'],
            active: isset($data['active']) && in_array((string) $data['active'], ['1', 'true'], true),
        );

        $this->salleRepository->update($id, $dto);

        header('Location: /salles/' . $id . '?success=' . rawurlencode('Salle modifiée avec succès.'));
        exit;
    }
}
