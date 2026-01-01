<?php

namespace App\Core\ValueObjects;

class Password
{
    private string $hashed;

    public function __construct(string $value, bool $isHashed = false)
    {
        if ($isHashed) {
            $this->hashed = $value;
        } else {
            $this->hashed = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
        }

    }

    public function getHashed(): string
    {
        return $this->hashed;
    }

    public function verify(string $plain): bool
    {
        return password_verify($plain, $this->hashed);
    }

}
