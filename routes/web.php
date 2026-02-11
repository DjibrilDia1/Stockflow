<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
});

require __DIR__.'/auth.php';