<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\ImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Авторизация и регистрация
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Закрытые методы
    Route::middleware('auth:sanctum')->group(function () {

        //выход из системы
        Route::post('logout', [AuthController::class, 'logout']);
    
        // Роуты для работы с изображениями
        Route::apiResource('images', ImageController::class)
            ->except(['update']);
            
    });
});
