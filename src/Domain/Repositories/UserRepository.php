<?php

namespace App\Domain\Repositories;

use App\Core\ValueObjects\Email;
use App\Core\ValueObjects\UUID;
use App\Domain\Entities\User;

interface UserRepository
{
    public function save(User $user): void;
    public function findById(UUID $id): ?User;
    public function findByEmail(Email $email): ?User;
}
