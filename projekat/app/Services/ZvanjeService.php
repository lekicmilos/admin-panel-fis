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

    public function store($data)
    {
        $validator = Validator::make($data, [
            'naziv' => 'required',
            'nivo' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        return $this->zvanjeRepository->store($data);
    }


}