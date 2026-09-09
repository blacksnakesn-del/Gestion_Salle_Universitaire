<?php

namespace App\Validation;

final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $errors = [];
        $acceptedData = [];

        
        $salleId = filter_var(
            $data['salle_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($salleId === false || $salleId === null || $salleId <= 0) {
            $errors['salle_id'] = 'La salle sélectionnée est invalide.';
        } else {
            $acceptedData['salle_id'] = $salleId;
        }

        
        $responsable = trim((string) ($data['responsable'] ?? ''));

        if ($responsable === '') {
            $errors['responsable'] = 'Le responsable est obligatoire.';
        } elseif (
            mb_strlen($responsable) < 2 ||
            mb_strlen($responsable) > 120
        ) {
            $errors['responsable'] =
                'Le responsable doit contenir entre 2 et 120 caractères.';
        } else {
            $acceptedData['responsable'] = $responsable;
        }

        $email = trim((string) ($data['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L’adresse email est invalide.';
        } else {
            $acceptedData['email'] = $email;
        }

        $motif = trim((string) ($data['motif'] ?? ''));

        if ($motif === '') {
            $errors['motif'] = 'Le motif est obligatoire.';
        } elseif (mb_strlen($motif) < 5 || mb_strlen($motif) > 255) {
            $errors['motif'] =
                'Le motif doit contenir entre 5 et 255 caractères.';
        } else {
            $acceptedData['motif'] = $motif;
        }

   
        $dateDebut = trim((string) ($data['date_debut'] ?? ''));

        if ($dateDebut === '') {
            $errors['date_debut'] = 'La date de début est obligatoire.';
        } else {
            $parsedDateDebut = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $dateDebut
            );

            $dateErrors = \DateTimeImmutable::getLastErrors();

            if (
                $parsedDateDebut === false ||
                ($dateErrors !== false &&
                    ($dateErrors['warning_count'] > 0 ||
                     $dateErrors['error_count'] > 0))
            ) {
                $errors['date_debut'] = 'La date de début est invalide.';
            } else {
                $acceptedData['date_debut'] = $parsedDateDebut;
            }
        }

    
        $dateFin = trim((string) ($data['date_fin'] ?? ''));

        if ($dateFin === '') {
            $errors['date_fin'] = 'La date de fin est obligatoire.';
        } else {
            $parsedDateFin = \DateTimeImmutable::createFromFormat(
                'Y-m-d H:i',
                $dateFin
            );

            $dateErrors = \DateTimeImmutable::getLastErrors();

            if (
                $parsedDateFin === false ||
                ($dateErrors !== false &&
                    ($dateErrors['warning_count'] > 0 ||
                     $dateErrors['error_count'] > 0))
            ) {
                $errors['date_fin'] = 'La date de fin est invalide.';
            } else {
                $acceptedData['date_fin'] = $parsedDateFin;
            }
        }


        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            acceptedData: $acceptedData
        );
    }
}