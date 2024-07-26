<?php

namespace App\Repositories;

use App\Models\Zvanje;

class ZvanjeRepository
{
    /**
     * @var Zvanje
     */
    protected $zvanje;

    /**
     * @param $zvanje
     */
    public function __construct(Zvanje $zvanje)
    {
        $this->zvanje = $zvanje;
    }

    public function store($data)
    {
        $zvanje = new $this->zvanje;
        $zvanje->naziv_zvanja = $data['naziv'];
        $zvanje->nivo = $data['nivo'];

        $zvanje->save();

        return $zvanje->fresh();
    }

    public function update($zvanje, $data)
    {
        $zvanje->naziv_zvanja = $data['naziv'];
        $zvanje->nivo = $data['nivo'];

        $zvanje->update();

        return $zvanje;
    }
}