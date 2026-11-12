<?php

use App\Features\Biens\Http\Controllers\BienController;
use App\Features\Categories\Http\Controllers\CategorieController;
use App\Features\Services\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('biens', BienController::class)->middleware('auth:sanctum');
Route::apiResource('categories', CategorieController::class)
    ->parameters(['categories' => 'categorie'])
    ->except('destroy')
    ->middleware('auth:sanctum');
Route::apiResource('services', ServiceController::class)->except('destroy')->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
