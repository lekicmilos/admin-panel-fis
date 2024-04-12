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
    /**
     * @var KatedraRepository
     */
    protected $katedraRepository;

    /**
     * @param $katedraRepository
     */
    public function __construct(KatedraRepository $katedraRepository)
    {
        $this->katedraRepository = $katedraRepository;
    }

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
            $zap->ime.' '.$zap->srednje_slovo.'. '.$zap->prezime,
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
        //$validator = $this->validator($data);

        /*if ($validator->fails()) {
            return $validator;
        }*/

        $zaposleni_sync = [];
        $zaposleni_attach = [];

        // zatvaranje angazovanja zaposlenih prilikom unosa nove katedra
        foreach ($katedraDTO->zaposleni as $zap)
        {
            // pri izmeni: ako je vec aktivan, izmeniti angazovanje (sync)
            // ako nije aktivan, dodati angazovanje (attach)
            $aktivan = false;

            if ($katedraDTO->id)
            {
                $aktivan = DB::table('angazovanje_na_katedri')
                    ->whereRaw('( zaposleni_id = ? AND katedra_id = ? AND ( datum_do > ? OR datum_od < ?) )',
                        [$zap->id, $katedraDTO->id, $zap->datum_od, $zap->datum_do])->doesntExist();

            }


            if ($aktivan)
                $zaposleni_sync[$zap->id] = [
                    'datum_od' => $zap->datum_od,
                    'datum_do' => $zap->datum_do
                ];
            else
                $zaposleni_attach[$zap->id] = [
                    'datum_od' => $zap->datum_od,
                    'datum_do' => $zap->datum_do
                ];



            $datum_pocetka_novog = Carbon::createFromFormat('Y-m-d', $zap->datum_od);
            $datum_zavrsetka_starog = $datum_pocetka_novog->addDays(-1);

            // nadji prethodno angazovanje -> zatvoriti datum_do kao pocetak novog - 1
            DB::table('angazovanje_na_katedri')
                ->whereRaw('( zaposleni_id = ? AND datum_od < ? AND (datum_do IS NULL OR datum_do > ?) )',
                    [$zap->id, $zap->datum_od, $zap->datum_od])
                ->update(['datum_do' => $datum_zavrsetka_starog]);

            /** varijanta brisemo sve */

//            // nadji angazovanja koja pocinju nakon novog angazovanja -> obrisati kompletno
//            // alternativa: izbaciti gresku ?
//            DB::table('angazovanje_na_katedri')
//                ->whereRaw('( zaposleni_id = ? AND datum_od > ? )',
//                    [$zap->id, $zap->datum_od])
//                ->delete();


            // nadji angazovanja koja pocinju u trajanju novog -> obrisati kompletno
            // alternativa: izbaciti gresku ??
            DB::table('angazovanje_na_katedri')
                ->whereRaw('( zaposleni_id = ? AND datum_od > ? AND (datum_do IS NULL OR datum_do < ? OR ?) )',
                    [$zap->id, $zap->datum_od, $zap->datum_do, $zap->datum_do === null ? 1 : 0])
                ->delete();

            // nadji angazovanja koja su pocela pre zavrsetka novog -> pomeriti pocetak starog
            // alternativa: izbaciti gresku ??
            if ($zap->datum_do)
            {
                $datum_zavrsetka_novog = Carbon::createFromFormat('Y-m-d', $zap->datum_do);
                $datum_pocetka_starog = $datum_zavrsetka_novog->addDays(1);

                DB::table('angazovanje_na_katedri')
                    ->whereRaw('( zaposleni_id = ? AND datum_od < ? AND (datum_do IS NULL OR datum_do > ?) )',
                        [$zap->id, $zap->datum_do, $zap->datum_do])
                    ->update(['datum_od' => $datum_pocetka_starog]);
            }

        }

        $katedra = Katedra::find($katedraDTO->id);
        if (empty($katedra)) {
            $katedra = new Katedra();
        }

        $katedra->naziv_katedre = $katedraDTO->naziv;
        $katedra->aktivna = true;

        $katedra->save();

        $katedra->angazovanje()->attach($zaposleni_attach);
        $katedra->angazovanje()->syncWithoutDetaching($zaposleni_sync);

        return $katedra->fresh();
        //return $this->katedraRepository->upsert($katedraDTO);
    }




}