<?php

namespace App\Services;

use App\DTO\KatedraZaposlenogDTO;
use App\DTO\ZaposleniDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\DTO\ZvanjeZaposlenogDTO;
use App\Models\Katedra;
use App\Models\Zaposleni;

class ZaposleniService
{
    public static function toDTO(Zaposleni $zaposleni)
    {
        $katedra = $zaposleni->katedra->first();
        $zvanje = $zaposleni->zvanje->first();

        return new ZaposleniDTO(
            $zaposleni->id,
            $zaposleni->ime,
            $zaposleni->prezime,
            $zaposleni->srednje_slovo,
            $zaposleni->email,
            $zaposleni->pol,
            $zaposleni->fis_broj,
            $zaposleni->u_penziji,
            $zaposleni->datum_penzije,
            $katedra ? new KatedraZaposlenogDTO(
                $katedra->id,
                $katedra->naziv_katedre,
                $katedra->pivot->datum_od,
                $katedra->pivot->datum_do,
            ) : null,
            $zvanje ? new ZvanjeZaposlenogDTO(
                $zvanje->id,
                $zvanje->naziv_zvanja,
                $zvanje->pivot->datum_od,
                $zvanje->pivot->datum_do,
            ) : null,
        );
    }

    public static function upsert(ZaposleniDTO $zaposleniDTO)
    {
        $zaposleni = Zaposleni::find($zaposleniDTO->id) ?? new Zaposleni();

        $zaposleni->id = $zaposleniDTO->id;
        $zaposleni->ime = $zaposleniDTO->ime;
        $zaposleni->prezime = $zaposleniDTO->prezime;
        $zaposleni->srednje_slovo = $zaposleniDTO->srednje_slovo;
        $zaposleni->email = $zaposleniDTO->email;
        $zaposleni->pol = $zaposleniDTO->pol;
        $zaposleni->fis_broj = $zaposleniDTO->fis_broj;
        $zaposleni->u_penziji = $zaposleniDTO->u_penziji;
        $zaposleni->datum_penzije = $zaposleniDTO->datum_penzije;
        $zaposleni->active = 1;

        $zaposleni->save();

        if ($zaposleniDTO->katedra) {
            $katedra = Katedra::find($zaposleniDTO->katedra->id);
            $zap = new ZaposleniNaKatedriDTO($zaposleni->id, null, $zaposleniDTO->katedra->datum_od, $zaposleniDTO->katedra->datum_do);
            (new KatedraService())->upsertZaposlenog($katedra, $zap);
        }

        // TODO uraditi isto za zvanje
        // fulltext index https://laravel.com/docs/11.x/queries#full-text-where-clauses
        // Rule 'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($this->user()->id)]

    }
}
