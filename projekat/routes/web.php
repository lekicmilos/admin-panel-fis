<?php

use App\Http\Controllers\ZvanjeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/zvanje', [ZvanjeController::class, 'index'])->name('zvanje.index');
Route::get('/zvanje/create', [ZvanjeController::class, 'create'])->name('zvanje.create');
Route::post('/zvanje', [ZvanjeController::class, 'store'])->name('zvanje.store');
Route::get('/zvanje/{zvanje}/edit', [ZvanjeController::class, 'edit'])->name('zvanje.edit');
Route::put('/zvanje/{zvanje}/update', [ZvanjeController::class, 'update'])->name('zvanje.update');
Route::delete('/zvanje/{zvanje}/destroy', [ZvanjeController::class, 'destroy'])->name('zvanje.destroy');