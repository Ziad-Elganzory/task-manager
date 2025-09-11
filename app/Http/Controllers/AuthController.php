<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateEmailException;
use App\Exceptions\InvalidCredentialsException;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function register(RegisterUserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = $this->userService->registerUser($validatedData);

            return response()->json([
                'message' => 'User registered successfully!',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            ['user' => $user, 'token' => $token] = $this->userService->getUserToken($validatedData);
            return response()->json([
                'message' => 'Login successful!',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $this->userService->userLogout($user);

            return response()->json([
                'message' => 'Logged out successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
