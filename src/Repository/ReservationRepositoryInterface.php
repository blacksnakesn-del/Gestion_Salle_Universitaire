<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\ReservationDTO;
use App\Model\Reservation;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;

interface ReservationRepositoryInterface
{
   
    public function findAll(): Collection;

    public function findById(int $id): ?Reservation;

    public function hasOverlap(
        int $salleId,
        DateTimeImmutable $dateDebut,
        DateTimeImmutable $dateFin
    ): bool;

    
    public function findBySalleId(int $salleId): Collection;

    public function create(ReservationDTO $dto): Reservation;

    public function cancel(Reservation $reservation): Reservation;
}