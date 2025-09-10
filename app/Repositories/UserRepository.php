<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(protected User $userModel){}

    public function create(array $data): User
    {
        return $this->userModel->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }

    public function isEmailUnique(string $email): bool
    {
        return $this->userModel->where('email', $email)->doesntExist();
    }
}
