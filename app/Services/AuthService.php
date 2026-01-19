<?php

namespace App\Services;

use App\DTOs\AuthResponseData;
use App\DTOs\LoginData;
use App\DTOs\RegisterData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function register(RegisterData $data): AuthResponseData
    {
        $user = $this->userRepository->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return new AuthResponseData($user, $token);
    }

    public function login(LoginData $data): AuthResponseData
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new AuthResponseData($user, $token);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
