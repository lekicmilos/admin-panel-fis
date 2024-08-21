<?php

namespace App\Services;

use App\DTO\KatedraZaposlenogDTO;
use App\DTO\ZaposleniDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\DTO\ZvanjeZaposlenogDTO;
use App\Models\Katedra;
use App\Models\Zaposleni;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ZaposleniService
{
    public static function toDTO(Zaposleni $zaposleni)
    {
        $katedra = $zaposleni->katedra->first();
        $zvanje = $zaposleni->zvanje->first();

        $katedraArray = $zaposleni->angazovanje->map(function ($item) {
            return new KatedraZaposlenogDTO(
                $item->id,
                $item->naziv_katedre,
                $item->pivot->datum_od,
                $item->pivot->datum_do
            );
        })->toArray();

        $zvanjeArray = $zaposleni->zvanja->map(function ($item) {
            return new ZvanjeZaposlenogDTO(
                $item->id,
                $item->naziv_zvanja,
                $item->pivot->datum_od,
                $item->pivot->datum_do
            );
        })->toArray();

        return new ZaposleniDTO(
            $zaposleni->id,
            $zaposleni->ime,
            $zaposleni->prezime,
            $zaposleni->srednje_slovo,
            $zaposleni->email,
            $zaposleni->pol,
            $zaposleni->fis_broj,
            $zaposleni->u_penziji,
            $zaposleni->datum_penzije,
            $katedra ? new KatedraZaposlenogDTO(
                $katedra->id,
                $katedra->naziv_katedre,
                $katedra->pivot->datum_od,
                $katedra->pivot->datum_do,
            ) : null,
            $zvanje ? new ZvanjeZaposlenogDTO(
                $zvanje->id,
                $zvanje->naziv_zvanja,
                $zvanje->pivot->datum_od,
                $zvanje->pivot->datum_do,
            ) : null,
            $katedraArray,
            $zvanjeArray
        );
    }

    public static function upsert(ZaposleniDTO $zaposleniDTO)
    {
        $zaposleni = Zaposleni::find($zaposleniDTO->id) ?? new Zaposleni();

        $zaposleni->id = $zaposleniDTO->id;
        $zaposleni->ime = $zaposleniDTO->ime;
        $zaposleni->prezime = $zaposleniDTO->prezime;
        $zaposleni->srednje_slovo = $zaposleniDTO->srednje_slovo;
        $zaposleni->email = $zaposleniDTO->email;
        $zaposleni->pol = $zaposleniDTO->pol;
        $zaposleni->fis_broj = $zaposleniDTO->fis_broj;
        $zaposleni->u_penziji = $zaposleniDTO->u_penziji;
        $zaposleni->datum_penzije = $zaposleniDTO->datum_penzije;
        $zaposleni->active = 1;

        $zaposleni->save();

        if ($zaposleniDTO->katedra) {
            $katedra = Katedra::find($zaposleniDTO->katedra->id);
            $zap = new ZaposleniNaKatedriDTO($zaposleni->id, null, $zaposleniDTO->katedra->datum_od, $zaposleniDTO->katedra->datum_do);
            (new KatedraService())->upsertZaposlenog($katedra, $zap);
        }

        if ($zaposleniDTO->zvanje) {
            self::upsertZvanje($zaposleni, $zaposleniDTO->zvanje);
        }
    }

    public static function upsertZvanje(Zaposleni $zaposleni, ZvanjeZaposlenogDTO $zvanjeDTO)
    {
        $table_name = 'izbor_u_zvanje';
        // nadji prethodne izbore istog zvanja zaposlenog
        $prethodna = $zaposleni->zvanja()->where('zvanje_id', $zvanjeDTO->id)->get();

        $updated = false;
        $exists = false;
        foreach ($prethodna as $zvanje) {
            $pr_id = $zvanje->pivot->id;
            $pr_datum_od = $zvanje->pivot->datum_od;
            $pr_datum_do = $zvanje->pivot->datum_do;

            // ako postoji isto zvanje u istom periodu, ne menjati
            if ($pr_datum_od == $zvanjeDTO->datum_od && $pr_datum_do == $zvanjeDTO->datum_do) {
                $exists = true;
                break;
            }

            // ako postoji preklapanje tog istog zvanja, izmeniti ga
            // ako postoji vise istih, obrisati
            // zaposleni ne moze imati vise puta isto zvanje u isto vreme
            if (DateService::isOverlapping($pr_datum_od, $pr_datum_do, $zvanjeDTO->datum_od, $zvanjeDTO->datum_do)) {
                if (!$updated) {
                    DB::table($table_name)->where('id', $pr_id)
                        ->update([
                            'datum_od' => $zvanjeDTO->datum_od,
                            'datum_do' => $zvanjeDTO->datum_do,
                            'updated_at' => Carbon::now(),
                        ]);
                    $updated = true;
                } else {
                    DB::table($table_name)->delete($pr_id);
                }
            }
        }

        // ukoliko je novo zvanje, uneti ga
        if (!$updated && !$exists) {
            $zaposleni->zvanja()->attach($zvanjeDTO->id, [
                'datum_od' => $zvanjeDTO->datum_od,
                'datum_do' => $zvanjeDTO->datum_do
            ]);
        }

        // nadji ostale izbore u zvanja zaposlenog i obradi preklapanja ukoliko postoje
        $ostala_zvanja = $zaposleni->zvanja()->whereNot('zvanje_id', $zvanjeDTO->id)->get();
        foreach ($ostala_zvanja as $prethodno) {
            DateService::obradiPreklapanje($prethodno, $table_name, $zvanjeDTO->datum_od, $zvanjeDTO->datum_od);
        }
    }
}
