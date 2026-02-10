<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemStockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\WithdrawRequestLineController;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'canResetPassword' => Route::has('password.request'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboardtest', function () {
    return Inertia::render('DashboardTest');
})->name('dashboardtest'); // <--- Ajoutez ceci !

Route::get('/articles', function () {
    return Inertia::render('Articles'); // Correspond à Pages/Articles.vue
})->name('articles.index');

Route::get('/mouvements', function () {
    return Inertia::render('Mouvements'); // Assure-toi que le fichier s'appelle Mouvements.vue
})->name('mouvements.index');

Route::get('/demandes', function () {
    return Inertia::render('Demandes'); // Le nom de ton fichier .vue (ex: Pages/Demandes.vue)
})->name('demandes.index');

Route::get('/rapports', function () {
    return Inertia::render('Rapports'); // Le nom de ton fichier .vue (ex: Pages/Rapports.vue)
})->name('rapports.index');

Route::get('/utilisateurs', function () {
    return Inertia::render('Utilisateurs'); // Le nom de ton fichier .vue (ex: Pages/Utilisateurs.vue)
})->name('utilisateurs.index');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('services', ServiceController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('items', ItemController::class);
    Route::resource('item-stocks', ItemStockController::class);
    Route::resource('stock-movements', StockMovementController::class);
    Route::resource('withdraw-requests', WithdrawRequestController::class);
    Route::resource('withdraw-request-lines', WithdrawRequestLineController::class);
});

require __DIR__.'/auth.php';
