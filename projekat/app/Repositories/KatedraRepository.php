<?php

namespace App\Repositories;

use App\Models\Katedra;
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

    public function store($data)
    {
        $katedra = new $this->katedra;
        $katedra->naziv_katedre = $data['naziv'];
        $katedra->aktivna = true;

        $katedra->save();

        /*
        zaposleni niz u formi
        [ id_zap => ['datum_od' => ..., 'datum_do' => ...],
          ... ]
        */
        $katedra->angazovanje()->attach($data['zaposleni']);

        $katedra->pozicija()->attach([
            $data['sef_id'] => [
                'pozicija' => 'Šef katedre',
                'datum_od' => $data['sef_datum_od'],
                'datum_do' => $data['sef_datum_do']
            ],

            $data['zamenik_id'] => [
                'pozicija' => 'Zamenik katedre',
                'datum_od' => $data['zamenik_datum_od'],
                'datum_do' => $data['zamenik_datum_do']
            ]
        ]);


        return $katedra->fresh();
    }

    public function update($zvanje, $data)
    {
        $zvanje->naziv_zvanja = $data['naziv'];
        $zvanje->nivo = $data['nivo'];

        $zvanje->update();

        return $zvanje;
    }
}