<?php

use App\Features\Biens\Http\Controllers\BienWebController;
use App\Features\Categories\Http\Controllers\CategorieWebController;
use App\Features\Inventaires\Http\Controllers\CampagneInventaireWebController;
use App\Features\Ministeres\Http\Controllers\MinistereWebController;
use App\Features\Services\Http\Controllers\ServiceWebController;
use App\Features\Transferts\Http\Controllers\TransfertWebController;
use App\Features\Users\Http\Controllers\UserWebController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RapportController;
use App\Http\Middleware\EnsureCentralAuth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tableau-de-bord');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});

Route::middleware(EnsureCentralAuth::class)->group(function () {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');

    Route::middleware('role:administrateur_national,gestionnaire')->group(function () {
        Route::get('/biens/create', [BienWebController::class, 'create'])->name('biens.create');
        Route::post('/biens', [BienWebController::class, 'store'])->name('biens.store');
        Route::get('/biens/{bien}/edit', [BienWebController::class, 'edit'])->name('biens.edit');
        Route::put('/biens/{bien}', [BienWebController::class, 'update'])->name('biens.update');
        Route::patch('/biens/{bien}', [BienWebController::class, 'update']);
        Route::post('/biens/{bien}/archiver', [BienWebController::class, 'archive'])->name('biens.archive');
        Route::get('/transferts/nouveau', [TransfertWebController::class, 'create'])->name('transferts.create');
        Route::post('/transferts', [TransfertWebController::class, 'store'])->name('transferts.store');
        Route::get('/inventaires/nouveau', [CampagneInventaireWebController::class, 'create'])->name('inventaires.create');
        Route::post('/inventaires', [CampagneInventaireWebController::class, 'store'])->name('inventaires.store');
        Route::post('/inventaires/{inventaire}/cloturer', [CampagneInventaireWebController::class, 'close'])->name('inventaires.close');
        Route::get('/rapports/export', [RapportController::class, 'export'])->name('rapports.export');
    });

    Route::get('/biens', [BienWebController::class, 'index'])->name('biens.index');
    Route::get('/biens/{bien}', [BienWebController::class, 'show'])->name('biens.show');
    Route::get('/biens/{bien}/historique/{historique}/justificatif', [BienWebController::class, 'downloadHistoryEvidence'])
        ->name('biens.historique.justificatif');
    Route::get('/transferts', [TransfertWebController::class, 'index'])->name('transferts.index');
    Route::get('/transferts/{transfert}', [TransfertWebController::class, 'show'])->name('transferts.show');
    Route::get('/transferts/{transfert}/justificatif', [TransfertWebController::class, 'downloadEvidence'])->name('transferts.justificatif');
    Route::get('/inventaires', [CampagneInventaireWebController::class, 'index'])->name('inventaires.index');
    Route::get('/inventaires/{inventaire}', [CampagneInventaireWebController::class, 'show'])->name('inventaires.show');
    Route::post('/inventaires/{inventaire}/constats', [CampagneInventaireWebController::class, 'record'])->name('inventaires.constats');
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/audit', [RapportController::class, 'audit'])->middleware('role:administrateur_national')->name('rapports.audit');
    Route::get('/rapports/audit/{audit}/justificatif', [RapportController::class, 'downloadEvidence'])->middleware('role:administrateur_national')->name('rapports.audit.justificatif');

    Route::middleware('role:administrateur_national,gestionnaire')->group(function () {
        Route::get('/categories', [CategorieWebController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategorieWebController::class, 'store'])->name('categories.store');
        Route::put('/categories/{categorie}', [CategorieWebController::class, 'update'])->name('categories.update');
    });

    Route::middleware('role:administrateur_national')->group(function () {
        Route::get('/services', [ServiceWebController::class, 'index'])->name('services.index');
        Route::post('/services', [ServiceWebController::class, 'store'])->name('services.store');
        Route::put('/services/{service}', [ServiceWebController::class, 'update'])->name('services.update');
        Route::get('/ministeres', [MinistereWebController::class, 'index'])->name('ministeres.index');
        Route::post('/ministeres', [MinistereWebController::class, 'store'])->name('ministeres.store');
        Route::put('/ministeres/{ministere}', [MinistereWebController::class, 'update'])->name('ministeres.update');
        Route::post('/transferts/{transfert}/approuver', [TransfertWebController::class, 'approve'])->name('transferts.approuver');
        Route::post('/transferts/{transfert}/rejeter', [TransfertWebController::class, 'reject'])->name('transferts.rejeter');
        Route::get('/utilisateurs', [UserWebController::class, 'index'])->name('utilisateurs.index');
        Route::post('/utilisateurs', [UserWebController::class, 'store'])->name('utilisateurs.store');
        Route::put('/utilisateurs/{utilisateur}', [UserWebController::class, 'update'])->name('utilisateurs.update');
    });
});
