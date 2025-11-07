<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Cliente\OrdenController;

Route::get('/', function () {
    return view('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas administrativas (requieren auth; AdminController valida rol)
    Route::get('/admin/users', [\App\Http\Controllers\Admin\AdminController::class, 'usuarios'])->name('admin.users');
    Route::post('/admin/users/{user}/role', [\App\Http\Controllers\Admin\AdminController::class, 'updateUserRole'])->name('admin.users.updateRole');
});

require __DIR__.'/auth.php';
