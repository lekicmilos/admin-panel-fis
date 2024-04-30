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




}