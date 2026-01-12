<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientController; // <--- Pon esto ARRIBA del todo del archivo
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CalendarController; // <--- AGREGAR ARRIBA
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    // ... otras rutas ...

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');

    // AGREGAR ESTA LÍNEA:
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

    // AGREGAR ESTA LÍNEA (Ruta paramétrica: {id} es el número del cliente)
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');

    // --- NUEVAS RUTAS PARA EDITAR Y BORRAR ---
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // ... dentro del middleware auth ...
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');

    // NUEVAS RUTAS:
    Route::get('/loans/{id}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/installments/{id}/pay', [LoanController::class, 'registerPayment'])->name('installments.pay');

    Route::get('/loans/{id}/pdf', [LoanController::class, 'downloadPdf'])->name('loans.pdf');

    // ... dentro del middleware auth ...
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
