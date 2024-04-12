<?php

namespace App\Repositories;

use App\DTO\KatedraDTO;
use App\Models\Katedra;
use App\Models\Pozicija;
use App\Models\Zvanje;

class KatedraRepository
{
    /**
     * @var Katedra
     */
    protected $katedra;

    /**
     * @param $katedra
     */
    public function __construct(Katedra $katedra)
    {
        $this->katedra = $katedra;
    }

    public function upsert(KatedraDTO $data)
    {
        $katedra = Katedra::find($data->id);
        if (empty($katedra)) {
            $katedra = new $this->katedra;
        }

        $katedra->naziv_katedre = $data->naziv;
        $katedra->aktivna = true;

        $katedra->save();

        /*$zaposleni = [];
        foreach ($data->zaposleni as $zap)
        {
            $zaposleni[$zap->id] = [
                'datum_od' => $zap->datum_od,
                'datum_do' => $zap->datum_do
            ];
        }

        // sync ili attach ??
        $katedra->angazovanje()->sync($zaposleni);*/

        // da li sef i zamenik moze biti ista osoba????
        $katedra->pozicija()->attach([
            $data->sef->id => [
                'pozicija' => Pozicija::Sef,
                'datum_od' => $data->sef->datum_od,
                'datum_do' => $data->sef->datum_do
            ],

            $data->zamenik->id => [
                'pozicija' => Pozicija::Zamenik,
                'datum_od' => $data->zamenik->datum_od,
                'datum_do' => $data->zamenik->datum_do
            ]
        ]);

        return $katedra->fresh();
    }


}