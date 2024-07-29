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


    public function dodajPoziciju(Pozicija $pozicija, $id_zap, $datum_od, $datum_do)
    {
        // da li neko moze da bude sef/zamenik na vise katedra??

        // nadji prethodne pozicije zaposlenog na katedri
        $prethodna = $this->pozicija()
            ->wherePivot('pozicija', $pozicija)
            ->where('zaposleni_id', $id_zap)
            ->get();

        foreach ($prethodna as $pozicije) {
            $pr_id = $pozicije->pivot->id;
            $pr_datum_od = $pozicije->pivot->datum_od;
            $pr_datum_do = $pozicije->pivot->datum_do;

            // ako je zaposleni vec drzao tu poziciju na toj katedri u tom periodu
            // obrisati takve pozicije
            if (!(($pr_datum_od < $datum_od && $pr_datum_do < $datum_od) ||
                ($datum_do && $pr_datum_od > $datum_do))) {
                DB::table('pozicija_na_katedri')->delete($pr_id);
            }
        }

        // dodaj novu poziciju u tabelu
        $this->pozicija()->attach($id_zap, ['pozicija' => $pozicija, 'datum_od' => $datum_od, 'datum_do' => $datum_do]);


        // nadji ko je ranije drzao tu poziciju i zatvori ih
        $prethodne = $this->pozicija()
            ->wherePivot('pozicija', $pozicija)
            ->whereNot('zaposleni_id', $id_zap)
            ->get();

        foreach ($prethodne as $poz)
        {
            $pr_id = $poz->pivot->id;
            $pr_datum_od = $poz->pivot->datum_od;
            $pr_datum_do = $poz->pivot->datum_do;

            // ako je angazovanje pocelo pre pocetka novog, zatvori ga pomocu datuma do
            if ($pr_datum_od < $datum_od /*&& (is_null($pr_datum_do) || $pr_datum_do > $zap->datum_do)*/)
            {
                $datum_pocetka_novog = Carbon::createFromFormat('Y-m-d', $datum_od);
                $datum_zavrsetka_starog = $datum_pocetka_novog->subDay();
                DB::table('pozicija_na_katedri')->where('id', $pr_id)->update(['datum_do' => $datum_zavrsetka_starog]);
            }
            // ako je angazovanje pocelo pre zavrsetka novog, promeniti pocetak starog angazovanja
            elseif ($datum_do && $pr_datum_od < $datum_do && (is_null($pr_datum_do) || $pr_datum_do > $datum_do))
            {
                $datum_zavrsetka_novog = Carbon::createFromFormat('Y-m-d', $datum_do);
                $datum_pocetka_starog = $datum_zavrsetka_novog->addDay();
                DB::table('pozicija_na_katedri')->where('id', $pr_id)->update(['datum_od' => $datum_pocetka_starog]);
            }
            // ako je angazovanje pocelo u trajanju novog, obrisati ga
            elseif (/*$pr_datum_od >= $zap->datum_od && */((is_null($pr_datum_do) || is_null($datum_do) || $pr_datum_do < $datum_do))) {
                DB::table('pozicija_na_katedri')->delete($pr_id);
            }
            // angazovanje se ne poklapa sa novim angazovanjem tako da ne moramo raditi nista

        }
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
