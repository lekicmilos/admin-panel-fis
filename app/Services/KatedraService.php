<?php

namespace App\Services;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\Models\Katedra;
use App\Models\Pozicija;
use App\Models\Zaposleni;
use App\Repositories\KatedraRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Illuminate\Support\Facades\Validator;

class KatedraService
{
    private function toZaposleniNaKatedriDTO(Zaposleni $zap) : ZaposleniNaKatedriDTO
    {
        return new ZaposleniNaKatedriDTO(
            $zap->id,
            $zap->punoIme(),
            $zap->pivot->datum_od,
            $zap->pivot->datum_do
        );
    }

    public function toDTO(Katedra $katedra): KatedraDTO
    {
        $sef = $katedra->sef->first();
        $zamenik = $katedra->zamenik->first();

        $sef = $sef ? $this->toZaposleniNaKatedriDTO($sef) : null;
        $zamenik = $zamenik ? $this->toZaposleniNaKatedriDTO($zamenik) : null;

        $aktivni_zaposleni = $katedra->angazovanje()->get()->sortBy(['ime', 'pivot.datum_od']);

        $zaposleni = [];
        foreach ($aktivni_zaposleni as $zap)
            $zaposleni[] = $this->toZaposleniNaKatedriDTO($zap);

        return new KatedraDTO(
            $katedra->id,
            $katedra->naziv_katedre,
            $katedra->aktivna,
            $zaposleni,
            $sef,
            $zamenik,
        );
    }

    public function upsert(KatedraDTO $katedraDTO)
    {
        $katedra = Katedra::with(['angazovanje', 'pozicija'])->find($katedraDTO->id) ?? new Katedra();

        $katedra->naziv_katedre = $katedraDTO->naziv;
        $katedra->aktivna = 1;
        $katedra->save();

        // cuvanje ID-ova angazovanja pre obrade radi poredjenja
        $postojecaAng = DB::table('angazovanje_na_katedri')
            ->where('katedra_id', $katedra->id)
            ->pluck('id')
            ->all();

        $obradjenaAng = [];

        // obrada angazovanih
        foreach ($katedraDTO->zaposleni as $zap)
            $obradjenaAng[] = $this->upsertZaposlenog($katedra, $zap);

        // brisanje angazovanih koji nisu obradjeni
        $za_brisanje = array_diff($postojecaAng, $obradjenaAng);
        DB::table('angazovanje_na_katedri')->whereIn('id', $za_brisanje)->delete();

        // obrada pozicija
        $this->upsertPozicija($katedra, Pozicija::Sef, $katedraDTO->sef);
        $this->upsertPozicija($katedra, Pozicija::Zamenik, $katedraDTO->zamenik);

        return $katedra->fresh();
    }

    public function upsertZaposlenog(Katedra $katedra, ZaposleniNaKatedriDTO $zap) {
        $table_name = 'angazovanje_na_katedri';
        $processed_id = null;
        // nadji prethodna angazovanja zaposlenog na katedri
        $prethodna = $katedra->angazovanje()->where('zaposleni_id', $zap->id)->get();

        $updated = false;
        $existing = false;
        foreach ($prethodna as $angazovanja) {
            $pr_id = $angazovanja->pivot->id;
            $pr_datum_od = $angazovanja->pivot->datum_od;
            $pr_datum_do = $angazovanja->pivot->datum_do;

            // proveravamo da li postoji identicno angazovanje
            if ($pr_datum_od == $zap->datum_od && $pr_datum_do == $zap->datum_do) {
                $processed_id = $pr_id;
                $existing = true;
                break;
            }

            // ako postoji preklapanje, izmeniti datume angazovanja
            // ako postoji vise preklapanja, obrisati ostla angazovanja
            // ovo osigurava da jedan zaposleni ne moze biti angazovan vise puta na istoj katedri u isto vreme
            if (DateService::isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {
                if (!$updated) {
                    DB::table($table_name)->where('id', $pr_id)
                        ->update([
                            'datum_od' => $zap->datum_od,
                            'datum_do' => $zap->datum_do,
                            'updated_at' => Carbon::now(),
                        ]);
                    $updated = true;
                    $processed_id = $pr_id;
                } else {
                    DB::table($table_name)->delete($pr_id);
                }
            }
        }

        // dodaj novo angazovanje u tabelu
        if (!$updated && !$existing) {
            $katedra->angazovanje()->attach($zap->id, ['datum_od' => $zap->datum_od, 'datum_do' => $zap->datum_do]);
            $processed_id = 0;
        }

        // nadji angazovanja na ostalim katedrama
        $zaposleni = Zaposleni::find($zap->id);
        $angazovanja_na_ostalim_katedrama = $zaposleni->angazovanje()->whereNot('katedra_id', $katedra->id)->get();

        foreach ($angazovanja_na_ostalim_katedrama as $prethodna) {
            DateService::obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
        }

        return $processed_id;
    }

    public function upsertPozicija(Katedra $katedra, Pozicija $pozicija, ZaposleniNaKatedriDTO $zap) {

        $table_name = 'pozicija_na_katedri';
        $processed_id = null;
        // nadji prethodne pozicije zaposlenog
        $prethodna_pozicija_zap_na_katedri = $katedra->pozicija()->where('zaposleni_id', $zap->id)->get();

        $updated = false;
        $exists = false;
        foreach ($prethodna_pozicija_zap_na_katedri as $pozicije) {
            $pr_id = $pozicije->pivot->id;
            $pr_datum_od = $pozicije->pivot->datum_od;
            $pr_datum_do = $pozicije->pivot->datum_do;

            // proveravamo da li postoji identicno angazovanje
            if ($pr_datum_od == $zap->datum_od && $pr_datum_do == $zap->datum_do) {
                $exists = true;
                break;
            }

            // azurirati poziciju na katedri za datog zaposlenog, ostala poklapanja obrisati
            if (DateService::isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {
                if (!$updated) {
                    DB::table($table_name)->where('id', $pr_id)
                        ->update([
                            'pozicija' => $pozicija,
                            'datum_od' => $zap->datum_od,
                            'datum_do' => $zap->datum_do,
                            'updated_at' => Carbon::now(),
                        ]);
                    $updated = true;
                } else {
                    DB::table($table_name)->delete($pr_id);
                }
            }
        }

        // dodaj poziciju
        if (!$updated && !$exists) {
            $katedra->pozicija()->attach($zap->id, ['pozicija' => $pozicija, 'datum_od' => $zap->datum_od, 'datum_do' => $zap->datum_do]);
        }

        // nadji ko je drzao poziciju na toj katedri i azuiriraj datume
        $prethodne_pozicije_na_katedri = $katedra->pozicija()->whereNot('zaposleni_id', $zap->id)->wherePivot('pozicija', $pozicija)->get();
        foreach ($prethodne_pozicije_na_katedri as $prethodna) {
            DateService::obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
        }

        // nadji pozicije na ostalim katedrama
        // moze imati poziciju samo na jednoj katedri
        $zaposleni = Zaposleni::find($zap->id);
        $pozicije_na_ostalim_katedrama = $zaposleni->pozicija()->whereNot('katedra_id', $katedra->id)->wherePivot('pozicija', $pozicija)->get();
        foreach ($pozicije_na_ostalim_katedrama as $prethodna) {
            DateService::obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
        }
    }

    // for an array of ZaposleniNaKatedraDTO
    // it returns the index of the first zaposleni that was added twice with the overlapping dates
    public static function validateDuplicate(array $zaposleniNaKatedriDTOs): null | int
    {
        $datesById = [];
        foreach ($zaposleniNaKatedriDTOs as $index => $zap) {
            $id = $zap->id;
            if (!isset($datesById[$id])) {
                $datesById[$id] = [];
            }

            foreach ($datesById[$id] as $dates) {
                if (DateService::isOverlapping($zap->datum_od, $zap->datum_do, $dates['datum_od'], $dates['datum_do']))
                    return $index;
            }

            $datesById[$id][] = [
                'datum_od' => $zap->datum_od,
                'datum_do' => $zap->datum_do,
            ];
        }
        return null;
    }
}
