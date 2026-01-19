<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\LoginRequest;
use App\Http\Requests\Api\v1\RegisterRequest;
use App\Http\Resources\Api\v1\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * @group Авторизация
 */
class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {
    }

    /**
     * register
     * @unauthenticated
     * @apiResource App\Http\Resources\Api\v1\AuthResource
     * @apiResourceModel App\Models\User
     */
    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->toDTO());

        return new AuthResource($result->user, $result->token);
    }

    /**
     * login
     * @unauthenticated
     * @apiResource App\Http\Resources\Api\v1\AuthResource
     * @apiResourceModel App\Models\User
     */
    public function login(LoginRequest $request): AuthResource
    {
        $result = $this->authService->login($request->toDTO());

        return new AuthResource($result->user, $result->token);
    }

    /**
     * logout
     * @authenticated
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Успешно вышли из системы']);
    }
}
