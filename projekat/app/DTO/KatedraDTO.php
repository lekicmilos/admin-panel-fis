<?php

namespace App\DTO;


use App\Models\Katedra;
use App\Models\Pozicija;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

readonly class KatedraDTO
{
    public function __construct(
        public ?int $id,
        public string $naziv,
        public bool $aktivna,
        public array $zaposleni,
        public ?ZaposleniNaKatedriDTO $sef,
        public ?ZaposleniNaKatedriDTO $zamenik,
    )
    {}


}