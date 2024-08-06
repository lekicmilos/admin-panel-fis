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

    protected function validator($data)
    {
        // TODO duplikati zaposlenog

        // da li sef i zamenik moze biti ista osoba
        // sta se desava kada pri update obrisemo zaposlenog
        // sta se desava pri poklapanju datuma (da li menjamo pocetak buduceg?)
        // kako da znamo da li smo promenili nekom datume ili smo uneli duplikat

    }

    private function toZaposleniNaKatedriDTO(Zaposleni $zap) : ZaposleniNaKatedriDTO
    {
        return new ZaposleniNaKatedriDTO(
            $zap->id,
            $zap->punoIme(),
            $zap->pivot->datum_od,
            $zap->pivot->datum_do
        );
    }

    private function uzmiPoziciju(Pozicija $pozicija, Katedra $katedra) {
        $zap = $katedra->trenutnaPozicija($pozicija);
        return $zap ? $this->toZaposleniNaKatedriDTO($zap) : null;
    }

    public function toDTO(Katedra $katedra): KatedraDTO
    {
        $sef = $this->uzmiPoziciju(Pozicija::Sef, $katedra);
        $zamenik = $this->uzmiPoziciju(Pozicija::Zamenik, $katedra);

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
        $katedra = Katedra::find($katedraDTO->id) ?? new Katedra();

        $katedra->naziv_katedre = $katedraDTO->naziv;
        $katedra->aktivna = true;
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

            // ako je zaposleni vec angazovan na katedri u tom periodu obrisati to angazovanje
            // osiguravamo da zaposleni nema poklapajuca angazovanja na istoj katedri
            if ($this->isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {
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
            $this->obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
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
            if ($this->isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {
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
            $this->obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
        }

        // nadji pozicije na ostalim katedrama
        // moze imati poziciju samo na jednoj katedri
        $zaposleni = Zaposleni::find($zap->id);
        $pozicije_na_ostalim_katedrama = $zaposleni->pozicija()->whereNot('katedra_id', $katedra->id)->wherePivot('pozicija', $pozicija)->get();
        foreach ($pozicije_na_ostalim_katedrama as $prethodna) {
            $this->obradiPreklapanje($prethodna, $table_name, $zap->datum_od, $zap->datum_do);
        }
    }

    // Funkcija koja prima prethodno angazovanje/poziciju zaposlenog na drugoj katedri,
    // proverava da li postoji poklapanja i to resava tako sto menja datume/brise prethodno angazovanje
    // npr. ako je zaposleni radio na katedri1, a sada radi na katedri2, angazovanje na katedri1 se zatvara sa datumom do
    private function obradiPreklapanje($prethodno, $table_name, $novo_od, $novo_do): void
    {
        $pr_id = $prethodno->pivot->id;
        $pr_datum_od = $prethodno->pivot->datum_od;
        $pr_datum_do = $prethodno->pivot->datum_do;

        if ($this->isOverlapping($pr_datum_od, $pr_datum_do, $novo_od, $novo_do)) {
            // ako je angazovanje pocelo pre pocetka novog, zatvori ga pomocu datuma do
            if ($pr_datum_od < $novo_od)
            {
                $datum_zavrsetka_starog = Carbon::createFromFormat('Y-m-d', $novo_od)->subDay();
                DB::table($table_name)->where('id', $pr_id)
                    ->update([
                        'datum_do' => $datum_zavrsetka_starog,
                        'updated_at' => Carbon::now(),
                    ]);
            }
            // ako je angazovanje pocelo pre zavrsetka novog, promeniti pocetak starog angazovanja
            elseif ($novo_do && $pr_datum_od < $novo_do && (is_null($pr_datum_do) || $pr_datum_do > $novo_do))
            {
                $datum_pocetka_starog = Carbon::createFromFormat('Y-m-d', $novo_do)->addDay();
                DB::table($table_name)->where('id', $pr_id)
                    ->update([
                        'datum_od' => $datum_pocetka_starog,
                        'updated_at' => Carbon::now(),
                    ]);
            }
            // ako je angazovanje pocelo u trajanju novog ili u slucaju kompletnog preklapanja, obrisati ga
            else {
                DB::table($table_name)->delete($pr_id);
            }
        }
    }

    private function isOverlapping($startDate1, $endDate1, $startDate2, $endDate2): bool
    {
        $endDate1 = $endDate1 ?? PHP_INT_MAX;
        $endDate2 = $endDate2 ?? PHP_INT_MAX;
        // Check if the periods overlap
        return $startDate1 <= $endDate2 && $endDate1 >= $startDate2;
    }

}
