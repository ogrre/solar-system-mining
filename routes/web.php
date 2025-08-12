<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolarSystemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('solar-systems.index');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/solar-systems', [SolarSystemController::class, 'index'])->name('solar-systems.index');
    Route::get('/solar-systems/{solarSystem}', [SolarSystemController::class, 'show'])->name('solar-systems.show');

    Route::get('/solar-systems/{solarSystem}/games/create', [GameController::class, 'create'])->name('games.create');
    Route::post('/solar-systems/{solarSystem}/games', [GameController::class, 'store'])->name('games.store');
    Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
    Route::post('/games/join', [GameController::class, 'join'])->name('games.join');
    Route::post('/games/{game}/join-public', [GameController::class, 'joinPublic'])->name('games.join-public');
    Route::post('/games/{game}/leave', [GameController::class, 'leave'])->name('games.leave');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
