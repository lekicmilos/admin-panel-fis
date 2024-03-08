<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use mysql_xdevapi\Table;

class Zaposleni extends Model
{
    use HasFactory;

    protected $table = 'zaposleni';

    protected $fillable = [
        'ime',
        'prezime',
        "srednje_slovo",
        "email",
        "pol",
        "fis_broj",
        "u_penziji",
        "datum_penzije",
    ];

    public function angazovanje(): BelongsToMany
    {
        return $this->belongsToMany(Katedra::class,
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

    public function zvanja(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'izbor_u_zvanje',
            'zvanje_id',
            'zaposleni_id')
            ->withPivot('datum_od', 'datum_do');
    }
}
