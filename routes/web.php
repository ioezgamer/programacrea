<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipanteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/participante/create', [ParticipanteController::class, 'create'])->name('participante.create');
Route::post('/participante/store', [ParticipanteController::class, 'store'])->name('participante.store');
Route::get('/participante', [ParticipanteController::class, 'index'])->name('participante.index');
Route::get('/participante/{participante}', [ParticipanteController::class, 'show'])->name('participante.show');
Route::get('/participante/{participante}/edit', [ParticipanteController::class, 'edit'])->name('participante.edit');
Route::put('/participante/{participante}', [ParticipanteController::class, 'update'])->name('participante.update');
Route::delete('/participante/{participante}', [ParticipanteController::class, 'destroy'])->name('participante.destroy');
Route::get('/asistencia', [AsistenciaController::class, 'create'])->name('asistencia.create');
Route::post('/asistencia', [AsistenciaController::class, 'store'])->name('asistencia.store');
});

require __DIR__.'/auth.php';
