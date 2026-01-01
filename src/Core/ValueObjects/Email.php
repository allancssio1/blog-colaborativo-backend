<?php

namespace App\Core\ValueObjects;

use App\Core\Exceptions\ValidationException;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValidEmail($value)) {
            throw new ValidationException(null, 'Invalid email', 401);
        }

        $this->value = strtolower($value);
    }

    private function isValidEmail(string $email): bool
    {
        if (strpos($email, '@') === false) {
            return false;
        }
        return (str_ends_with($email, '.com') || str_ends_with($email, '.br'));
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->__toString();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
