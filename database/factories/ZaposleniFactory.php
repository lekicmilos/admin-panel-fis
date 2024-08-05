<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zaposleni>
 */
class ZaposleniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $uPenziji = $this->faker->boolean;

        return [
            'ime' => $this->faker->firstName,
            'prezime' => $this->faker->lastName,
            'srednje_slovo' => Str::upper($this->faker->randomLetter()),
            'email' => $this->faker->unique()->safeEmail,
            'pol' => $this->faker->randomElement(['Muski', 'Zenski']),
            'fis_broj' => $this->faker->unique()->randomNumber(5),
            'u_penziji' => $uPenziji,
            'datum_penzije' => $uPenziji ? $this->faker->date : null,
        ];
    }
}
