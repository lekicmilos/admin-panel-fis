<?php

use App\Http\Controllers\KatedraController;
use App\Http\Controllers\ZvanjeController;
use App\Livewire\KatedraForm;
use App\Livewire\KatedraIndex;
use App\Livewire\ZvanjeIndex;
use App\Livewire\KatedraTable;
use App\Livewire\ZvanjeForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/zvanje', ZvanjeIndex::class)->name('zvanje.index');
Route::get('/zvanje/create', ZvanjeForm::class)->name('zvanje.create');
Route::post('/zvanje', [ZvanjeController::class, 'store'])->name('zvanje.store');
Route::get('/zvanje/{zvanje_id}/edit', ZvanjeForm::class)->name('zvanje.edit');
Route::put('/zvanje/{zvanje}', [ZvanjeController::class, 'update'])->name('zvanje.update');
Route::delete('/zvanje/{zvanje}', [ZvanjeController::class, 'destroy'])->name('zvanje.destroy');

Route::get('/katedra', KatedraIndex::class)->name('katedra.index');
//Route::get('/katedra', [KatedraController::class, 'index'])->name('katedra.index');
Route::get('/katedra/create', KatedraForm::class)->name('katedra.create');
Route::post('/katedra', [KatedraController::class, 'store'])->name('katedra.store');
Route::get('/katedra/{katedra_id}/edit', KatedraForm::class)->name('katedra.edit');
Route::put('/katedra/{katedra_id}', [KatedraController::class, 'update'])->name('katedra.update');
Route::delete('/katedra/{katedra_id}', [KatedraController::class, 'delete'])->name('katedra.destroy');
Route::get('/katedra/search', [KatedraController::class, 'search'])->name('katedra.search');
