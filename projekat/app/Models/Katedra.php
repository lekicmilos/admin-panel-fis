<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Katedra extends Model
{
    use HasFactory;

    protected $table = 'katedra';

    protected $fillable = [
        'naziv_katedre',
        'aktivna'
    ];

    public function angazovanje(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'angazovanje_na_katedri',
            'katedra_id',
            'zaposleni_id')
            ->withPivot('datum_od', 'datum_do');
    }

    public function pozicije(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'pozicije_na_katedri',
            'katedra_id',
            'zaposleni_id')
            ->withPivot('pozicija', 'datum_od', 'datum_do');
    }

}
