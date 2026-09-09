<?php

namespace App\Validation;

final class ValidationResult
{
    public function __construct(
        private readonly bool $valid,
        private readonly array $errors = [],
        private readonly array $acceptedData = []
    ) {
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getAcceptedData(): array
    {
        return $this->acceptedData;
    }
}