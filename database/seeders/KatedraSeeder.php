<?php

namespace Database\Seeders;

use App\DTO\KatedraDTO;
use App\DTO\ZaposleniNaKatedriDTO;
use App\Models\Katedra;
use App\Models\Zaposleni;
use App\Services\KatedraService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KatedraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $zaposleniIds = Zaposleni::pluck('id')->toArray();
        $katedraService = app(KatedraService::class);

        for ($i = 0; $i < 50; $i++) {
            $naziv = fake()->company;
            $aktivna = 1;

            // Generate a unique recent date for each assignment
            $sefDatumOd = fake()->dateTimeBetween('-10 year', 'now')->format('Y-m-d');
            $zamenikDatumOd = fake()->dateTimeBetween('-10 year', 'now')->format('Y-m-d');

            // Create Šef assignment
            $sefId = fake()->randomElement($zaposleniIds);
            $sefDatumDo = fake()->boolean(50) ? fake()->dateTimeBetween($sefDatumOd, '+5 year')->format('Y-m-d') : null;

            $sef = new ZaposleniNaKatedriDTO(
                id: $sefId,
                ime: Zaposleni::find($sefId)->ime,
                datum_od: $sefDatumOd,
                datum_do: $sefDatumDo
            );

            // Create Zamenik assignment
            do {
                $zamenikId = fake()->randomElement($zaposleniIds);
            } while ($zamenikId == $sefId);

            $zamenikDatumDo = fake()->boolean(50) ? fake()->dateTimeBetween($zamenikDatumOd, '+5 year')->format('Y-m-d') : null;

            $zamenik = new ZaposleniNaKatedriDTO(
                id: $zamenikId,
                ime: Zaposleni::find($zamenikId)->ime,
                datum_od: $zamenikDatumOd,
                datum_do: $zamenikDatumDo
            );

            // Assign employees
            $employeeCount = fake()->numberBetween(5, 30);
            $zaposleni = [];
            for ($j = 0; $j < $employeeCount; $j++) {
                $employeeId = fake()->randomElement($zaposleniIds);
                $employeeDatumOd = fake()->dateTimeBetween('-10 year', 'now')->format('Y-m-d');
                $employeeDatumDo = fake()->boolean(70) ? fake()->dateTimeBetween($employeeDatumOd, '+2 year')->format('Y-m-d') : null;

                $zaposleni[] = new ZaposleniNaKatedriDTO(
                    id: $employeeId,
                    ime: Zaposleni::find($employeeId)->ime,
                    datum_od: $employeeDatumOd,
                    datum_do: $employeeDatumDo
                );
            }

            // Create KatedraDTO instance
            $katedraDTO = new KatedraDTO(
                id: null, // Set to null for new records
                naziv: $naziv,
                aktivna: $aktivna,
                zaposleni: $zaposleni,
                sef: $sef,
                zamenik: $zamenik
            );

            // Upsert Katedra
            $katedraService->upsert($katedraDTO);
        }
    }

}
