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
        public int $fis_broj,
        public bool $u_penziji,
        public ?string $datum_penzije,
        public ?KatedraZaposlenogDTO $katedra,
        public ?ZvanjeZaposlenogDTO $zvanje,
        public array $all_katedra, //KatedraZaposlenogDTO
        public array $all_zvanje, //ZvanjeZaposlenogDTO
    )
    {
    }
}
