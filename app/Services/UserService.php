<?php

namespace App\Services;

use App\Exceptions\DuplicateEmailException;
use App\Exceptions\InvalidCredentialsException;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected UserRepository $userRepository){}

    public function registerUser(array $userData)
    {
        if(! $this->userRepository->isEmailUnique($userData['email'])) {
            throw new DuplicateEmailException();
        }

        $user = $this->userRepository->create($userData);

        return $user;
    }

    public function getUserToken(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if(!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException;
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function userLogout($user)
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}
