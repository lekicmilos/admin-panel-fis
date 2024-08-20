<?php

namespace Database\Seeders;

use App\DTO\ZvanjeZaposlenogDTO;
use App\Models\Zaposleni;
use App\Models\Zvanje;
use App\Services\ZaposleniService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZaposleniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $zvanja = [
            ['naziv_zvanja' => 'Redovan profesor', 'nivo' => 1],
            ['naziv_zvanja' => 'Vanredni profesor', 'nivo' => 2],
            ['naziv_zvanja' => 'Docent', 'nivo' => 3],
            ['naziv_zvanja' => 'Asistent', 'nivo' => 4],
            ['naziv_zvanja' => 'Saradnik u nastavi', 'nivo' => 5],
        ];

        foreach ($zvanja as $zvanje) {
            Zvanje::create($zvanje);
        }

        // Generate 500 zaposleni
        $zaposleniCollection = Zaposleni::factory()->count(500)->create();

        foreach ($zaposleniCollection as $zaposleni) {
            // Generate random dates for datum_od and datum_do
            $datumOd = fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d');
            $datumDo = fake()->boolean(50) ? fake()->dateTimeBetween($datumOd, '+5 years')->format('Y-m-d') : null;

            // Create a ZvanjeZaposlenogDTO with the random zvanje ID and generated dates
            $zvanjeDTO = new ZvanjeZaposlenogDTO(
                Zvanje::inRandomOrder()->first()->id,
                null,
                $datumOd,
                $datumDo,
            );

            // Call the upsertZvanje function to associate the zvanje with the zaposleni
            ZaposleniService::upsertZvanje($zaposleni, $zvanjeDTO);
        }
    }
}
