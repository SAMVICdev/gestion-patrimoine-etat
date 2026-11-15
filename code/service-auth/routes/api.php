<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1');
Route::post('/password/reset', [AuthController::class, 'reset'])->middleware('throttle:5,1');

Route::middleware(['auth:sanctum', 'role:administrateur_national'])->group(function () {
    Route::post('/register', [AuthController::class, 'store']);
    Route::put('/users/{user}', [AuthController::class, 'update']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
