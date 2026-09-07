<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;

final class SalleValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (
            !isset($data['nom']) ||
            !v::stringType()->notEmpty()->validate($data['nom'])
        ) {
            $errors['nom'] = 'Le nom de la salle est obligatoire.';
        }

        if (
            !isset($data['batiment']) ||
            !v::stringType()->notEmpty()->validate($data['batiment'])
        ) {
            $errors['batiment'] = 'Le bâtiment est obligatoire.';
        }

        if (
            !isset($data['capacite']) ||
            !v::intVal()->positive()->validate($data['capacite'])
        ) {
            $errors['capacite'] = 'La capacité doit être un entier positif.';
        }

        if (
            !isset($data['type']) ||
            !v::in([
                'cours',
                'informatique',
                'laboratoire',
                'amphitheatre',
                'reunion',
            ])->validate($data['type'])
        ) {
            $errors['type'] = 'Le type de salle est invalide.';
        }

        return $errors;
    }
}
