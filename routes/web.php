<?php

use App\Http\Controllers\KatedraController;
use App\Http\Controllers\ZvanjeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/zvanje', [ZvanjeController::class, 'index'])->name('zvanje.index');
Route::get('/zvanje/create', [ZvanjeController::class, 'create'])->name('zvanje.create');
Route::post('/zvanje', [ZvanjeController::class, 'store'])->name('zvanje.store');
Route::get('/zvanje/{zvanje}/edit', [ZvanjeController::class, 'edit'])->name('zvanje.edit');
Route::put('/zvanje/{zvanje}', [ZvanjeController::class, 'update'])->name('zvanje.update');
Route::delete('/zvanje/{zvanje}', [ZvanjeController::class, 'destroy'])->name('zvanje.destroy');

Route::get('/katedra', [KatedraController::class, 'index'])->name('katedra.index');
Route::get('/katedra/create', [KatedraController::class, 'create'])->name('katedra.create');
Route::post('/katedra', [KatedraController::class, 'store'])->name('katedra.store');
Route::get('/katedra/{katedra_id}/edit', [KatedraController::class, 'edit'])->name('katedra.edit');
Route::put('/katedra/{katedra_id}', [KatedraController::class, 'update'])->name('katedra.update');
Route::delete('/katedra/{katedra_id}', [KatedraController::class, 'delete'])->name('katedra.destroy');
Route::get('/katedra/search', [KatedraController::class, 'search'])->name('katedra.search');
