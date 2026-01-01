<?php

namespace App\Domain\UseCases\Auth;

use App\Core\Exceptions\DomainException;
use App\Core\ValueObjects\Email;
use App\Domain\Repositories\UserRepository;

class LoginUserUseCase
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function execute(string $email, string $password): array
    {
        $emailUser = new Email($email);
        $user = $this->userRepository->findByEmail($emailUser);

        if ($user === null || !$user->getPassword()->verify($password)) {
            throw new DomainException('Invalid credentials', 401);
        }

        return [
            'id' => $user->getId()->__toString(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->__toString(),
        ];
    }
}
