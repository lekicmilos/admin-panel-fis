<?php

namespace App\Services;

use App\Repositories\ZvanjeRepository;
use InvalidArgumentException;
use Illuminate\Support\Facades\Validator;

class ZvanjeService
{
    /**
     * @var ZvanjeRepository
     */
    protected $zvanjeRepository;

    /**
     * @param $zvanjeRepository
     */
    public function __construct(ZvanjeRepository $zvanjeRepository)
    {
        $this->zvanjeRepository = $zvanjeRepository;
    }

    protected function validator($data)
    {
         // dodatna validacija, da li treba jos nesto?
        return Validator::make($data, [
            'naziv' => 'required',
            'nivo' => 'required',
        ]);
    }

    public function store($data)
    {
        $validator = $this->validator($data);

        if ($validator->fails()) {
            return $validator;
        }

        return $this->zvanjeRepository->store($data);
    }

    public function update($zvanje, $data)
    {
        $validator = $this->validator($data);

        if ($validator->fails()) {
            return $validator;
        }

        return $this->zvanjeRepository->update($zvanje, $data);
    }


}