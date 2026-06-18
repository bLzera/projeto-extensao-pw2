<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Producer\DashboardController;
use App\Http\Controllers\Producer\SetupController;
use App\Http\Controllers\Producer\ProfileController as ProducerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/produtores', function () {
    return redirect('/');
})->name('producers.index');

// Setup de perfil — auth + verified, sem middleware de perfil (evita loop)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/setup', [SetupController::class, 'create'])->name('producer.setup');
    Route::post('/setup', [SetupController::class, 'store'])->name('producer.setup.store');
});

// Dashboard e perfil — auth + verified + perfil completo
Route::middleware(['auth', 'verified', 'producer.profile'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [ProducerProfileController::class, 'edit'])->name('producer.profile.edit');
    Route::patch('/dashboard/profile', [ProducerProfileController::class, 'update'])->name('producer.profile.update');
});

// Perfil do usuário (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
