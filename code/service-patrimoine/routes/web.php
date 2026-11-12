<?php

use App\Features\Biens\Http\Controllers\BienWebController;
use App\Features\Categories\Http\Controllers\CategorieWebController;
use App\Features\Services\Http\Controllers\ServiceWebController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tableau-de-bord');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');
    Route::resource('biens', BienWebController::class)->except('destroy');
    Route::post('/biens/{bien}/archiver', [BienWebController::class, 'archive'])->name('biens.archive');
    Route::get('/categories', [CategorieWebController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategorieWebController::class, 'store'])->name('categories.store');
    Route::put('/categories/{categorie}', [CategorieWebController::class, 'update'])->name('categories.update');
    Route::get('/services', [ServiceWebController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceWebController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceWebController::class, 'update'])->name('services.update');
});
