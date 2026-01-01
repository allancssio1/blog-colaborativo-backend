<?php

namespace App\Core\Exceptions;

class ValidationException extends DomainException
{
    private array $errors;

    public function __construct(?array $errors, string $message = 'Validation failed', int $statusCode = 422)
    {
        parent::__construct($message, $statusCode);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
