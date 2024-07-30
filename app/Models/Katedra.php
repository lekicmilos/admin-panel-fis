<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

enum Pozicija: string
{
    case Sef = "Šef katedre";
    case Zamenik = "Zamenik katedre";
}

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
            ->withPivot('id', 'datum_od', 'datum_do')
            ->withTimestamps();
    }

    public function pozicija(): BelongsToMany
    {
        return $this->belongsToMany(Zaposleni::class,
            'pozicija_na_katedri',
            'katedra_id',
            'zaposleni_id')
            ->withPivot('id', 'pozicija', 'datum_od', 'datum_do')
            ->withTimestamps();
    }

    public function sef()
    {
        $sef = $this->trenutnaPozicija(Pozicija::Sef);
        return $sef ? $sef->punoIme() : null;
    }

    public function zamenik()
    {
        $zamenik = $this->trenutnaPozicija(Pozicija::Zamenik);
        return $zamenik ? $zamenik->punoIme() : null;
    }
    public function trenutnaPozicija(Pozicija $pozicija)
    {
        $danas = Carbon::now();
        return $this->pozicija()
            ->wherePivot('pozicija', $pozicija)
            ->wherePivot('datum_od', '<=', $danas)
            ->where(function ($query) use ($danas) {
                $query->whereNull('datum_do')
                    ->orWhere('datum_do', '>=', $danas);
            })
            ->first();
    }
}
