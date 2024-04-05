<?php

namespace App\Services;

use App\Models\Katedra;
use App\Repositories\KatedraRepository;
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
        return Validator::make($data, [
            'naziv' => 'required',
            'nivo' => 'required',
        ]);
    }

    public function store($data)
    {
        //$validator = $this->validator($data);

        /*if ($validator->fails()) {
            return $validator;
        }*/

        return $this->katedraRepository->store($data);
    }

    public function update($katedra, $data)
    {
        $validator = $this->validator($data);

        if ($validator->fails()) {
            return $validator;
        }

        return $this->katedraRepository->update($katedra, $data);
    }


}