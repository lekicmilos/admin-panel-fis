<?php

namespace App\DTO;

readonly class ZvanjeDTO
{
    public function __construct(
        public ?int $id,
        public string $naziv,
        public string $nivo,
    ) {}
}
