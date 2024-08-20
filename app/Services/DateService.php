<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DateService
{
    public static function isOverlapping($startDate1, $endDate1, $startDate2, $endDate2): bool
    {
        $endDate1 = $endDate1 ?? PHP_INT_MAX;
        $endDate2 = $endDate2 ?? PHP_INT_MAX;
        // Check if the periods overlap
        return $startDate1 <= $endDate2 && $endDate1 >= $startDate2;
    }

    // Funkcija koja prima prethodno angazovanje/poziciju zaposlenog na drugoj katedri,
    // proverava da li postoji poklapanja i to resava tako sto menja datume/brise prethodno angazovanje
    // npr. ako je zaposleni radio na katedri1, a sada radi na katedri2, angazovanje na katedri1 se zatvara sa datumom do
    public static function obradiPreklapanje($prethodno, $table_name, $novo_od, $novo_do): void
    {
        $pr_id = $prethodno->pivot->id;
        $pr_datum_od = $prethodno->pivot->datum_od;
        $pr_datum_do = $prethodno->pivot->datum_do;

        if (self::isOverlapping($pr_datum_od, $pr_datum_do, $novo_od, $novo_do)) {
            // ako je angazovanje pocelo pre pocetka novog, zatvori ga pomocu datuma do
            if ($pr_datum_od < $novo_od)
            {
                $datum_zavrsetka_starog = Carbon::createFromFormat('Y-m-d', $novo_od)->subDay();
                DB::table($table_name)->where('id', $pr_id)
                    ->update([
                        'datum_do' => $datum_zavrsetka_starog,
                        'updated_at' => Carbon::now(),
                    ]);
            }
            // ako je angazovanje pocelo pre zavrsetka novog, promeniti pocetak starog angazovanja
            elseif ($novo_do && $pr_datum_od < $novo_do && (is_null($pr_datum_do) || $pr_datum_do > $novo_do))
            {
                $datum_pocetka_starog = Carbon::createFromFormat('Y-m-d', $novo_do)->addDay();
                DB::table($table_name)->where('id', $pr_id)
                    ->update([
                        'datum_od' => $datum_pocetka_starog,
                        'updated_at' => Carbon::now(),
                    ]);
            }
            // ako je angazovanje pocelo u trajanju novog ili u slucaju kompletnog preklapanja, obrisati ga
            else {
                DB::table($table_name)->delete($pr_id);
            }
        }
    }
}
