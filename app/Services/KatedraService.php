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

//    /**
//     * @var KatedraRepository
//     */
//    protected $katedraRepository;
//
//    /**
//     * @param $katedraRepository
//     */
//    public function __construct(KatedraRepository $katedraRepository)
//    {
//        $this->katedraRepository = $katedraRepository;
//    }

    protected function validator($data)
    {
        return Validator::make($data, []);

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

        $datum = Carbon::now();
        $zap = $katedra->pozicija()
            ->whereRaw('(pozicija = ? and (datum_do IS NULL or (datum_od <= ? and datum_do >= ?)))',
                [$pozicija, $datum, $datum])->first();

        if ($zap)
            return $this->toZaposleniNaKatedriDTO($zap);
        else
            return null;
    }

    public function toDTO(Katedra $katedra): KatedraDTO
    {
        $sef = $this->uzmiPoziciju(Pozicija::Sef, $katedra);
        $zamenik = $this->uzmiPoziciju(Pozicija::Zamenik, $katedra);


        $datum = Carbon::now();
        $aktivni_zaposleni =$katedra->angazovanje()
            ->whereRaw('(datum_do IS NULL or (datum_od <= ? and datum_do >= ?))', [$datum, $datum])
            ->get();


        $zaposleni = [];
        foreach ($aktivni_zaposleni as $zap)
            $zaposleni[] = $this->toZaposleniNaKatedriDTO($zap);

        return new KatedraDTO(
            id: $katedra->id,
            naziv: $katedra->naziv_katedre,
            aktivna: $katedra->aktivna,
            zaposleni: $zaposleni,
            sef: $sef,
            zamenik: $zamenik,
        );
    }



    public function upsert(KatedraDTO $katedraDTO)
    {
        // provera da li je dva puta isti zaposleni sa preklapajucim?


        $katedra = Katedra::find($katedraDTO->id) ?? new Katedra();

        $katedra->naziv_katedre = $katedraDTO->naziv;
        $katedra->aktivna = true;

        $katedra->save();

        foreach ($katedraDTO->zaposleni as $zap)
            $this->upsertZaposlenog($katedra, $zap);

        $this->upsertPozicija($katedra, Pozicija::Sef, $katedraDTO->sef);
        $this->upsertPozicija($katedra, Pozicija::Zamenik, $katedraDTO->zamenik);

        return $katedra->fresh();
        //return $this->katedraRepository->upsert($katedraDTO);
    }

    private function upsertZaposlenog(Katedra $katedra, ZaposleniNaKatedriDTO $zap) {
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
                $existing = true;
                break;
            }

            // ako je zaposleni vec angazovan na katedri u tom periodu obrisati to angazovanje
            // osiguravamo da zaposleni nema poklapajuca angazovanja na istoj katedri
            if ($this->isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {

                if (!$updated) {
                    DB::table('angazovanje_na_katedri')->where('id', $pr_id)
                        ->update([
                            'datum_od' => $zap->datum_od,
                            'datum_do' => $zap->datum_do,
                            'updated_at' => Carbon::now(),
                        ]);
                    $updated = true;
                } else {
                    DB::table('angazovanje_na_katedri')->delete($pr_id);
                }
            }
        }

        // dodaj novo angazovanje u tabelu
        if (!$updated && !$existing) {
            $katedra->angazovanje()->attach($zap->id, ['datum_od' => $zap->datum_od, 'datum_do' => $zap->datum_do]);
        }


        // nadji angazovanja na ostalim katedrama
        $zaposleni = Zaposleni::find($zap->id);
        $ostala = $zaposleni->angazovanje()->whereNot('katedra_id', $katedra->id)->get();

        foreach ($ostala as $angazovanja) {
            $pr_id = $angazovanja->pivot->id;
            $pr_datum_od = $angazovanja->pivot->datum_od;
            $pr_datum_do = $angazovanja->pivot->datum_do;

            if ($this->isOverlapping($pr_datum_od, $pr_datum_do, $zap->datum_od, $zap->datum_do)) {
                // ako je angazovanje pocelo pre pocetka novog, zatvori ga pomocu datuma do
                if ($pr_datum_od < $zap->datum_od)
                {
                    $datum_zavrsetka_starog = Carbon::createFromFormat('Y-m-d', $zap->datum_od)->subDay();
                    DB::table('angazovanje_na_katedri')->where('id', $pr_id)
                        ->update([
                            'datum_do' => $datum_zavrsetka_starog,
                            'updated_at' => Carbon::now(),
                            ]);
                }
                // ako je angazovanje pocelo pre zavrsetka novog, promeniti pocetak starog angazovanja
                elseif ($zap->datum_do && $pr_datum_od < $zap->datum_do && (is_null($pr_datum_do) || $pr_datum_do > $zap->datum_do))
                {
                    $datum_pocetka_starog = Carbon::createFromFormat('Y-m-d', $zap->datum_do)->addDay();
                    DB::table('angazovanje_na_katedri')->where('id', $pr_id)
                        ->update([
                            'datum_od' => $datum_pocetka_starog,
                            'updated_at' => Carbon::now(),
                            ]);
                }
                // ako je angazovanje pocelo u trajanju novog ili u slucaju kompletnog preklapanja, obrisati ga
                else {
                    DB::table('angazovanje_na_katedri')->delete($pr_id);
                }
            }

        }
    }

    private function upsertPozicija(Katedra $katedra, Pozicija $pozicija, ZaposleniNaKatedriDTO $zap) {

    }

    private function isOverlapping($startDate1, $endDate1, $startDate2, $endDate2): bool
    {

        // Check if the periods overlap
        return $startDate1 <= $endDate2 && $endDate1 >= $startDate2;
    }

}
