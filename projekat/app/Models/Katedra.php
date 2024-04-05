<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Katedra extends Model
{
    use HasFactory;

    private const table_name = 'katedra';
    protected $table = self::table_name;

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
            ->withPivot('datum_od', 'datum_do')
            ->withTimestamps();
    }

    public function pozicija(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'pozicija_na_katedri',
            'katedra_id',
            'zaposleni_id')
            ->withPivot('pozicija', 'datum_od', 'datum_do')
            ->withTimestamps();
    }

}
