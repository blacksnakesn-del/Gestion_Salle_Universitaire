<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Database\Eloquent\Collection;

final class SalleRepository
{
    
    public function findAll(): Collection
    {
        return Salle::all();
    }

    
    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    
    public function findActive(): Collection
    {
        return Salle::where('active', true)->get();
    }
}
