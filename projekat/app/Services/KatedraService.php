<?php

namespace App\Services;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\Models\Katedra;
use App\Models\Pozicija;
use App\Models\Zaposleni;
use App\Repositories\KatedraRepository;
use Carbon\Carbon;
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

//    protected function validator($data)
//    {
//        return Validator::make($data, [
//            'naziv' => 'required',
//            'nivo' => 'required',
//        ]);
//    }

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
            ->whereRaw('pozicija = ? and (datum_do IS NULL or (datum_od < ? and datum_do > ?))',
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
        $aktivni_zaposleni = $katedra->angazovanje()
            ->whereRaw('datum_do IS NULL or (datum_od <= ? and datum_do >= ?)', [$datum, $datum])
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



    public function store(KatedraDTO $katedraDTO)
    {
        //$validator = $this->validator($data);

        /*if ($validator->fails()) {
            return $validator;
        }*/



        return $this->katedraRepository->store($katedraDTO);
    }

    public function update(Katedra $katedra, KatedraDTO $data)
    {
        /*$validator = $this->validator($data);

        if ($validator->fails()) {
            return $validator;
        }*/

        return $this->katedraRepository->update($katedra, $data);
    }


}