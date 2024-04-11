<?php

namespace App\DTO;

use App\Models\Zaposleni;
use Carbon\Carbon;

readonly class ZaposleniNaKatedriDTO
{

    public function __construct(
        public int $id,
        public ?string $ime,
        public string $datum_od,
        public ?string $datum_do,
    ) {}

}