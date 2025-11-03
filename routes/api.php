<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\TodoController;

// Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'loginAccessToken']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('/me', [ApiProfileController::class, 'me']);
    Route::put('/profile', [ApiProfileController::class, 'update']);
    Route::post('/profile/avatar', [ApiProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [ApiProfileController::class, 'removeAvatar']);

    // Todos
    Route::get('/todos', [TodoController::class, 'index']);
    Route::post('/todos', [TodoController::class, 'store']);
    Route::get('/todos/{todo}', [TodoController::class, 'show']);
    Route::put('/todos/{todo}', [TodoController::class, 'update']);
    Route::delete('/todos/{todo}', [TodoController::class, 'destroy']);
});
