<?php

namespace App\Domain\Entities;

use App\Core\Exceptions\ValidationException;
use App\Core\ValueObjects\Email;
use App\Core\ValueObjects\Password;
use App\Core\ValueObjects\UUID;

class User
{
    private UUID $id;
    private Email $email;
    private Password $password;
    private string $name;
    private \DateTime $createdAt;

    public function __construct(
        UUID $id,
        Email $email,
        Password $password,
        string $name,
        ?\DateTime $createdAt = null
    ) {
        if (empty($name) || strlen($name) < 3) {
            throw new ValidationException(null, 'Name must be at least 3 characters long', 401);
        }

        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->createdAt = $createdAt ?? new \DateTime();
    }
    public function getId(): UUID
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
          'id' => $this->id->__toString(),
          'name' => $this->name,
          'email' => $this->email->__toString(),
          'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
