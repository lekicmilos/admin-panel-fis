<?php

namespace Database\Seeders;

use App\Models\Zaposleni;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZaposleniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Zaposleni::factory()->count(16)->create();
    }
}
