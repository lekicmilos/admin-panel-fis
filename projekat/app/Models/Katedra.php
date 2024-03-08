<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Katedra extends Model
{
    use HasFactory;

    protected $fillable = [
        'naziv_katedre',
        'aktivna'
    ];

    public function angazovanje(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'angazovanje_na_katedri',
            'zaposleni_id',
            'katedra_id')
            ->withPivot('datum_od', 'datum_do');
    }

    public function pozicije(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'pozicije_na_katedri',
            'zaposleni_id',
            'katedra_id')
            ->withPivot('pozicija', 'datum_od', 'datum_do');
    }

}
