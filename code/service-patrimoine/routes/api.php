<?php

use App\Features\Biens\Http\Controllers\BienController;
use App\Features\Categories\Http\Controllers\CategorieController;
use App\Features\Services\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('biens', BienController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategorieController::class)
        ->parameters(['categories' => 'categorie'])
        ->only(['index', 'show']);
    Route::apiResource('services', ServiceController::class)->only(['index', 'show']);

    Route::middleware('role:administrateur_national,gestionnaire')->group(function () {
        Route::apiResource('biens', BienController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('categories', CategorieController::class)
            ->parameters(['categories' => 'categorie'])
            ->except(['index', 'show', 'destroy']);
    });

    Route::middleware('role:administrateur_national')->group(function () {
        Route::apiResource('services', ServiceController::class)->except(['index', 'show', 'destroy']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
