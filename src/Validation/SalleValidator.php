<?php

namespace App\Validation;

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

        if ($nom === '') {
            $errors['nom'] = 'Le nom de la salle est obligatoire.';
        } elseif (mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 100 caractères.';
        } else {
            $acceptedData['nom'] = $nom;
        }

        $batiment = trim((string) ($data['batiment'] ?? ''));

        if ($batiment === '') {
            $errors['batiment'] = 'Le bâtiment est obligatoire.';
        } elseif (mb_strlen($batiment) < 2 || mb_strlen($batiment) > 100) {
            $errors['batiment'] = 'Le bâtiment doit contenir entre 2 et 100 caractères.';
        } else {
            $acceptedData['batiment'] = $batiment;
        }

        $capacite = filter_var(
            $data['capacite'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($capacite === false || $capacite === null) {
            $errors['capacite'] = 'La capacité doit être un nombre entier.';
        } elseif ($capacite <= 0) {
            $errors['capacite'] = 'La capacité doit être supérieure à zéro.';
        } else {
            $acceptedData['capacite'] = $capacite;
        }

        $type = trim((string) ($data['type'] ?? ''));

        if (!in_array($type, self::TYPES_AUTORISES, true)) {
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