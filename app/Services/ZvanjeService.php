<?php

namespace App\Services;

use App\DTO\ZvanjeDTO;
use App\Models\Zvanje;
use App\Repositories\ZvanjeRepository;
use InvalidArgumentException;
use Illuminate\Support\Facades\Validator;

class ZvanjeService
{

    public function upsert(ZvanjeDTO $zvanjeDTO) {
        $zvanje = Zvanje::find($zvanjeDTO->id) ?? new Zvanje();

        $zvanje->naziv_zvanja = $zvanjeDTO->naziv;
        $zvanje->nivo = $zvanjeDTO->nivo;
        $zvanje->save();
    }


}
