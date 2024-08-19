<?php

namespace App\DTO;

readonly class KatedraZaposlenogDTO
{

    public function __construct(
        public int $id,
        public ?string $naziv_katedre,
        public string $datum_od,
        public ?string $datum_do,
    )
    {
    }
}
