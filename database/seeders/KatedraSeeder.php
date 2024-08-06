<?php

namespace Database\Seeders;

use App\Models\Katedra;
use App\Models\Zaposleni;
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

        for ($i = 0; $i < 50; $i++) {
            $katedra = Katedra::create([
                'naziv_katedre' => fake()->company,
                'aktivna' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign a Šef
            $sefId = fake()->randomElement($zaposleniIds);
            $katedra->pozicija()->attach($sefId, [
                'pozicija' => 'Šef katedre',
                'datum_od' => fake()->date,
                'datum_do' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign a Zamenik
            do {
                $zamenikId = fake()->randomElement($zaposleniIds);
            } while ($zamenikId == $sefId);

            $katedra->pozicija()->attach($zamenikId, [
                'pozicija' => 'Zamenik katedre',
                'datum_od' => fake()->date,
                'datum_do' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign employees
            $employeeCount = fake()->numberBetween(5, 30);
            for ($j = 0; $j < $employeeCount; $j++) {
                $employeeId = fake()->randomElement($zaposleniIds);
                $katedra->angazovanje()->attach($employeeId, [
                    'datum_od' => fake()->date,
                    'datum_do' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
