<?php

namespace App\Validation;

use Respect\Validation\Validator as v;

final class SalleValidator implements ValidatorInterface
{
    private const TYPES_AUTORISES = [
        'cours',
        'informatique',
        'laboratoire',
        'amphitheatre',
        'reunion',
    ];

    public function validate(array $data): ValidationResult
    {
        $errors = [];
        $acceptedData = [];

       
        $nom = trim((string) ($data['nom'] ?? ''));

        if (!v::stringType()->notEmpty()->length(2, 100)->validate($nom)) {
            $errors['nom'] = 'Le nom de la salle est obligatoire.';
        } elseif (!v::stringType()->length(2, 100)->validate($nom)) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères.';
        } else {
            $acceptedData['nom'] = $nom;
        }

        $batiment = trim((string) ($data['batiment'] ?? ''));

        if (!v::stringType()->notEmpty()->length(2, 100)->validate($batiment)) {
            $errors['batiment'] = 'Le bâtiment est obligatoire.';
        } elseif (!v::stringType()->length(2, 100)->validate($batiment)) {
            $errors['batiment'] = 'Le bâtiment doit contenir entre 2 et 100 caractères.';
        } else {
            $acceptedData['batiment'] = $batiment;
        }

        $capacite = filter_var(
            $data['capacite'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!v::intVal()->between(1, 1000)->validate($capacite)) {
            $errors['capacite'] = 'La capacité doit être un nombre entier.';
        } else {
            $acceptedData['capacite'] = $capacite;
        }

        $type = trim((string) ($data['type'] ?? ''));

        if (!v::in(self::TYPES_AUTORISES)->validate($type)) {
            $errors['type'] = 'Le type de salle sélectionné est invalide.';
        } else {
            $acceptedData['type'] = $type;
        }

        $active = $data['active'] ?? true;

        if (!is_bool($active) && !in_array($active, [0, 1, '0', '1'], true)) {
            $errors['active'] = 'Le statut actif de la salle est invalide.';
        } else {
            $acceptedData['active'] = filter_var(
                $active,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            acceptedData: $acceptedData
        );
    }
}