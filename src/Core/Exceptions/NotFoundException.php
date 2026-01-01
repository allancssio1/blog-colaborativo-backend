<?php

namespace App\Core\Exceptions;

class NotFoundException extends DomainException
{
    public function __construct(string $message = 'Resource not found', int $statusCode = 404)
    {
        parent::__construct($message, $statusCode);
    }
}
