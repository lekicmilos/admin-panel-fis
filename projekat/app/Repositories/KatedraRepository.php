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

    public function store(KatedraDTO $data)
    {
        $katedra = new $this->katedra;
        $katedra->naziv_katedre = $data->naziv;
        $katedra->aktivna = true;

        $katedra->save();

        $zaposleni = [];
        foreach ($data->zaposleni as $zap)
        {
            $zaposleni[$zap->id] = [
                'datum_od' => $zap->datum_od,
                'datum_do' => $zap->datum_do
            ];
        }

        $katedra->angazovanje()->attach($zaposleni);

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

    public function update(Katedra $katedra, KatedraDTO $data)
    {
        //$katedra = new $this->katedra;
        $katedra->naziv_katedre = $data->naziv;
        $katedra->aktivna = true;

        $katedra->update();

        $zaposleni = [];
        foreach ($data->zaposleni as $zap)
        {
            $zaposleni[$zap->id] = [
                'datum_od' => $zap->datum_od,
                'datum_do' => $zap->datum_do
            ];
        }

        $katedra->angazovanje()->sync($zaposleni);

        $katedra->pozicija()->sync([
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

        return $katedra;

        /*$zvanje->naziv_zvanja = $data['naziv'];
        $zvanje->nivo = $data['nivo'];

        $zvanje->update();

        return $zvanje;*/
    }
}