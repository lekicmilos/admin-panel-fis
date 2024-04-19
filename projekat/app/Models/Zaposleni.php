<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use mysql_xdevapi\Table;

class Zaposleni extends Model
{
    use HasFactory;

    private const table_name = 'zaposleni';
    protected $table = self::table_name;

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

    public function punoIme()
    {
        return $this->ime.' '.$this->srednje_slovo.'. '.$this->prezime;
    }

    public function angazovanje(): BelongsToMany
    {
        return $this->belongsToMany(Katedra::class,
            'angazovanje_na_katedri',
            'zaposleni_id',
            'katedra_id')
            ->withPivot('id', 'datum_od', 'datum_do')
            ->withTimestamps();
    }

    public function pozicija(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'pozicija_na_katedri',
            'zaposleni_id',
            'katedra_id')
            ->withPivot('pozicija', 'datum_od', 'datum_do')
            ->withTimestamps();
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
