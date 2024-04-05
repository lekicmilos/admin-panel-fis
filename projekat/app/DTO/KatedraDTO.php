<?php

namespace App\DTO;


use App\Models\Katedra;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

//class ZaposleniNaKatedri
//{
//    public int $id;
//    public string $ime;
//    public $datum_od;
//    public $datum_do;
//}

readonly class KatedraDTO
{
    public int $id;
    public string $naziv;
    public bool $aktivna;
    public array $zaposleni;
    public ?ZaposleniNaKatedriDTO $sef;
    public ?ZaposleniNaKatedriDTO $zamenik;

    public function fromRequest(\Illuminate\Http\Request $request)
    {
        //$this->naziv = $request->naziv;
    }

    public function fromModel(Katedra $katedra)
    {
        $this->id = $katedra->id;
        $this->naziv = $katedra->naziv_katedre;
        $this->aktivna = $katedra->aktivna;
        
        $this->sef = $this->uzmiPoziciju('Šef katedre', $katedra);
        $this->zamenik = $this->uzmiPoziciju('Zamenik katedre', $katedra);

        $aktivni_zaposleni = $katedra->angazovanje()
            ->wherePivotNull('datum_do')
            ->orWhere(function (Builder $query) {
                $query->where('datum_od', '<=', Carbon::now())
                    ->where('datum_do', '>=', Carbon::now());
            })->get();

        $zaposleni = [];
        foreach ($aktivni_zaposleni as $zap)
        {
            $zaposleni[] = new ZaposleniNaKatedriDTO(
                $zap->id,
                $zap->ime.' '.$zap->prezime,
                $zap->pivot->datum_od,
                $zap->pivot->datum_do
            );
        }
        $this->zaposleni = $zaposleni;

        return $this;
    }



    public function uzmiPoziciju(string $pozicija, Katedra $katedra) {
        $zap = $katedra->pozicija()
            ->wherePivot('pozicija', $pozicija)
            ->wherePivotNull('datum_do')
            ->first();

        if (is_null($zap))
        {
            $datum = Carbon::now();

            $zap = $katedra->pozicija()
                ->wherePivot('pozicija', $pozicija)
                ->wherePivot('datum_od' ,'<=', $datum)
                ->wherePivot('datum_do' ,'>=', $datum)
                ->first();
        }

        if ($zap)
            return new ZaposleniNaKatedriDTO(
                $zap->id,
                $zap->ime.' '.$zap->prezime,
                $zap->pivot->datum_od,
                $zap->pivot->datum_do
            );
        else
            return null;
    }

}