<?php

use App\Http\Controllers\KatedraController;
use App\Http\Controllers\ZvanjeController;
use App\Livewire\KatedraForm;
use App\Livewire\KatedraIndex;
use App\Livewire\ZaposleniForm;
use App\Livewire\ZaposleniIndex;
use App\Livewire\ZvanjeIndex;
use App\Livewire\KatedraTable;
use App\Livewire\ZvanjeForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/zvanje', ZvanjeIndex::class)->name('zvanje.index');
Route::get('/zvanje/create', ZvanjeForm::class)->name('zvanje.create');
Route::get('/zvanje/{zvanje_id}/edit', ZvanjeForm::class)->name('zvanje.edit');

Route::get('/katedra', KatedraIndex::class)->name('katedra.index');
Route::get('/katedra/create', KatedraForm::class)->name('katedra.create');
Route::get('/katedra/{katedra_id}/edit', KatedraForm::class)->name('katedra.edit');

Route::get('/zaposleni', ZaposleniIndex::class)->name('zaposleni.index');
Route::get('/zaposleni/create', ZaposleniForm::class)->name('zaposleni.create');
Route::get('/zaposleni/{zaposleni_id}/edit', ZaposleniForm::class)->name('zaposleni.edit');
