<?php

namespace App\Controller;

use App\Repository\SalleRepository;

final class SalleController
{
    public function __construct(
        private readonly SalleRepository $salleRepository
    ) {
    }

    public function index(): void
    {
        $salles = $this->salleRepository->findAll();

        require __DIR__ . '/../../templates/salle/index.php';
    }

    public function show(int $id): void
    {
        $salle = $this->salleRepository->findById($id);

        if ($salle === null) {
            http_response_code(404);
            require __DIR__ . '/../../templates/errors/404.php';
            return;
        }

        require __DIR__ . '/../../templates/salle/show.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];

        require __DIR__ . '/../../templates/salle/form.php';
    }
}