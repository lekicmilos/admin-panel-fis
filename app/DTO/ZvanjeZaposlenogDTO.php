<?php

namespace App\DTO;

readonly class ZvanjeZaposlenogDTO
{

    public function __construct(
        public int $id,
        public ?string $naziv_zvanja,
        public string $datum_od,
        public ?string $datum_do,
    )
    {
    }
}
