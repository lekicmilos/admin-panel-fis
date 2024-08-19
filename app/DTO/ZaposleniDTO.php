<?php

namespace App\DTO;

readonly class ZaposleniDTO
{

    public function __construct(
        public ?int $id,
        public string $ime,
        public string $prezime,
        public string $srednje_slovo,
        public string $email,
        public string $pol,
        public int $broj,
        public bool $u_penziji,
        public string $datum_penzije,
        public ?KatedraZaposlenogDTO $katedra,
        public ?ZvanjeZaposlenogDTO $zvanje,
    )
    {
    }
}
