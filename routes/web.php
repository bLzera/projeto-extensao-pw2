<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProducerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Buyer\FavoritesPageController;

use App\Http\Controllers\Producer\DashboardController;
use App\Http\Controllers\Producer\ProductController as DashboardProductController;
use App\Http\Controllers\Producer\SetupController;
use App\Http\Controllers\Producer\ProfileController as ProducerProfileController;

use Illuminate\Support\Facades\Route;

// Catálogo público
// Os ->name() no final das rotas definem um alias pra rota
// Esse alias pode ser referido em qualquer outro lugar do app
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produtos/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/produtores', [ProducerController::class, 'index'])->name('producers.index');
Route::get('/produtores/{producer:slug}', [ProducerController::class, 'show'])->name('producers.show');

// Setup de perfil — auth + verified, sem middleware de perfil (evita loop)
// Middlewares 'auth' e 'verified' padrão do Breeze
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/setup', [SetupController::class, 'create'])->name('producer.setup');
    Route::post('/setup', [SetupController::class, 'store'])->name('producer.setup.store');
});

// Dashboard e perfil — auth + verified + perfil completo
// Middleware 'producer.profile' alias do nosso middleware customizado
Route::middleware(['auth', 'verified', 'producer.profile'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [ProducerProfileController::class, 'edit'])->name('producer.profile.edit');
    Route::patch('/dashboard/profile', [ProducerProfileController::class, 'update'])->name('producer.profile.update');

    Route::get('/dashboard/produtos/criar', [DashboardProductController::class, 'create'])->name('producer.products.create');
    Route::post('/dashboard/produtos', [DashboardProductController::class, 'store'])->name('producer.products.store');
    Route::get('/dashboard/produtos/{product}/editar', [DashboardProductController::class, 'edit'])->name('producer.products.edit');
    Route::put('/dashboard/produtos/{product}', [DashboardProductController::class, 'update'])->name('producer.products.update');
    Route::delete('/dashboard/produtos/{product}', [DashboardProductController::class, 'destroy'])->name('producer.products.destroy');
    Route::patch('/dashboard/produtos/{product}/disponibilidade', [DashboardProductController::class, 'toggleAvailability'])->name('producer.products.toggle');
    Route::patch('/dashboard/produtos/{product}/destaque', [DashboardProductController::class, 'toggleFeatured'])->name('producer.products.toggleFeatured');
});

// Perfil do usuário (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Comprador — favoritos e avaliações
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/favoritos/{product}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/meus-favoritos', [FavoritesPageController::class, 'index'])->name('buyer.favorites');
    Route::post('/produtores/{producer}/avaliar', [RatingController::class, 'upsert'])->name('ratings.upsert');
});

require __DIR__.'/auth.php';
