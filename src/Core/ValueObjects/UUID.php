<?php

namespace App\Core\ValueObjects;

use App\Core\Exceptions\ValidationException;
use Ramsey\Uuid\Uuid;

class UUID
{
    private string $value;

    public function __construct(?string $value)
    {
        if ($value === null) {
            $value = $this->generateUUID();
        }

        if (!$this->isValidUUID($value)) {
            throw new ValidationException(null, 'Invalid UUID', 401);
        }

        $this->value = $value;
    }


    private function generateUUID(): string
    {
        return  Uuid::uuid4();
    }

    private function isValidUUID(string $uuid): bool
    {
        return Uuid::isValid($uuid);
    }

    public function equals(UUID $other)
    {
        return $this->value === $other->__toString();

    }

    public function __toString(): string
    {
        return $this->value;
    }
}
