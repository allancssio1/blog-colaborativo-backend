<?php

namespace App\Infra\Database\Repositories;

use App\Core\ValueObjects\Email;
use App\Core\ValueObjects\Password;
use App\Core\ValueObjects\UUID;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Infra\Database\Connection;

class MySQLUserRepository implements UserRepository
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function save(User $user): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO users (id, name, email, password, created_at)
             VALUES (:id, :name, :email, :password, :created_at)
             ON DUPLICATE KEY UPDATE name = :name, email = :email'
        );

        $stmt->execute([
            ':id' => $user->getId()->__toString(),
            ':name' => $user->getName(),
            ':emal' => $user->getEmail()->__toString(),
            ':password' => $user->getPassword()->getHashed(),
            ':created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function findById(UUID $id): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id->__toString()]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email->__toString()]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    private function hydrate(array $row): User
    {
        return new User(
            new UUID($row['id']),
            new Email($row['email']),
            new Password($row['password'], true),
            $row['name'],
            new \DateTime($row['created_at'])
        );
    }
}
