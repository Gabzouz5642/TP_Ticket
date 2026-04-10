<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Accueil & Dashboard
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

// 2. Gestion des Tickets
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

// 3. Gestion des Projets (session pour l'instant)
Route::get('/projets/nouveau', [ProjectController::class, 'create'])->name('projets.create');
Route::post('/projets', [ProjectController::class, 'store'])->name('projets.store');

// 4. Authentification (Optionnel pour le moment)
Route::get('/login', function() {
    return view('auth.login');
})->name('login');
Route::post('/login', function() {
    return redirect()->route('dashboard');
})->name('login.post');
