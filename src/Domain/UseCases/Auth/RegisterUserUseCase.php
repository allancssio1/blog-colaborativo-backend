<?php

namespace App\Domain\UseCases\Auth;

use App\Core\Exceptions\DomainException;
use App\Core\ValueObjects\Email;
use App\Core\ValueObjects\Password;
use App\Core\ValueObjects\UUID;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;

class RegisterUserUseCase
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function execute(string $name, string $email, string $password): User
    {
        $emailUser = new Email($email);
        if ($this->userRepository->findByEmail($emailUser) !== null) {
            throw new DomainException('User already exists', 409);
        }

        $passwordUser = new Password($password);
        $user = new User(
            new UUID(null),
            $emailUser,
            $passwordUser,
            $name
        );

        $this->userRepository->save($user);
        return $user;
    }
}
