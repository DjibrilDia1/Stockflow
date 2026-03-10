<?php

use App\Enums\UserRole;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route d'accueil qui redirige vers la page de connexion
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'canResetPassword' => Route::has('password.request'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->get('/dashboard', function () {
    return match (request()->user()->role) {
        UserRole::GESTIONNAIRE => redirect()->route('gestionnaire.dashboard'),
        UserRole::RESPONSABLE => redirect()->route('responsable.dashboard'),
        UserRole::DEMANDEUR => redirect()->route('demandeur.dashboard'),
        default => abort(403, 'Role not supported.'),
    };
})->name('dashboard');


// Groupe pour le rôle GESTIONNAIRE
Route::middleware(['auth', 'role:gestionnaire'])->prefix('gestionnaire')->name('gestionnaire.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Articles CRUD
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::put('/articles/{item}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{item}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    // Catégories CRUD
    Route::post('/categories', [\App\Http\Controllers\CategorieController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\CategorieController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\CategorieController::class, 'destroy'])->name('categories.destroy');

    // Mouvements CRUD
    Route::get('/mouvements', [MouvementStockController::class, 'index'])->name('mouvements.index');
    Route::post('/mouvements', [MouvementStockController::class, 'store'])->name('mouvements.store');
    Route::delete('/mouvements/{stockMovement}', [MouvementStockController::class, 'destroy'])->name('mouvements.destroy');

    // Demandes
    Route::get('/demandes', [\App\Http\Controllers\DemandeSortieController::class, 'index'])->name('demandes.index');
    Route::post('/demandes/{demande}/validate', [\App\Http\Controllers\DemandeSortieController::class, 'validateRequest'])->name('demandes.validate');
    Route::delete('/demandes/{withdrawRequest}', [\App\Http\Controllers\DemandeSortieController::class, 'destroy'])->name('demandes.destroy');


    Route::get('/rapports', [ReportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/export', [ReportController::class, 'exportMovements'])->name('rapports.export');

    // Utilisateurs CRUD
    Route::get('/utilisateurs', [\App\Http\Controllers\UserController::class, 'index'])->name('utilisateurs.index');
    Route::post('/utilisateurs', [\App\Http\Controllers\UserController::class, 'store'])->name('utilisateurs.store');
    Route::put('/utilisateurs/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('utilisateurs.update');
    Route::delete('/utilisateurs/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('utilisateurs.destroy');

    Route::get('/services-fournisseurs', [\App\Http\Controllers\ServiceFournisseurController::class, 'index'])->name('services-fournisseurs.index');
    Route::post('/services', [\App\Http\Controllers\ServiceFournisseurController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [\App\Http\Controllers\ServiceFournisseurController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [\App\Http\Controllers\ServiceFournisseurController::class, 'destroyService'])->name('services.destroy');
    Route::post('/fournisseurs', [\App\Http\Controllers\ServiceFournisseurController::class, 'storeFournisseur'])->name('fournisseurs.store');
    Route::put('/fournisseurs/{fournisseur}', [\App\Http\Controllers\ServiceFournisseurController::class, 'updateFournisseur'])->name('fournisseurs.update');
    Route::delete('/fournisseurs/{fournisseur}', [\App\Http\Controllers\ServiceFournisseurController::class, 'destroyFournisseur'])->name('fournisseurs.destroy');
});

// Groupe pour le rôle RESPONSABLE
Route::middleware(['auth', 'role:responsable'])->prefix('responsable')->name('responsable.')->group(function () {
    Route::get('/dashboard', function () {
        // Cette route rendra `resources/js/Pages/Responsable/Dashboard.vue`
        return Inertia::render('Responsable/Dashboard');
    })->name('dashboard');

    Route::get('/rapports', [ReportController::class, 'index'])->name('rapports.index');
    Route::get('/rapports/export', [ReportController::class, 'exportMovements'])->name('rapports.export');
});

// Groupe pour le rôle DEMANDEUR
Route::middleware(['auth', 'role:demandeur'])->prefix('demandeur')->name('demandeur.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'demandeurDashboard'])->name('dashboard');

    Route::get('/demandes', [\App\Http\Controllers\DemandeSortieController::class, 'demandeurIndex'])->name('demandes.index');
    Route::post('/demandes', [\App\Http\Controllers\DemandeSortieController::class, 'demandeurStore'])->name('demandes.store');

    Route::get('/articles', [ArticleController::class, 'demandeurIndex'])->name('articles.index');
});

// Routes de profil générales
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
