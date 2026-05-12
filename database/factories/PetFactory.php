<?php

namespace Database\Factories;

use App\Models\Pet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'species' => $this->faker->randomElement(['Canino', 'Felino', 'Ave', 'Reptil']),
            'breed' => $this->faker->randomElement(['Mestizo', 'Golden Retriever', 'Siames', 'Bulldog', 'Pastor Alemán']),
            'sex' => $this->faker->randomElement(['male', 'female']),
            'birth_date' => $this->faker->dateTimeBetween('-12 years', '-3 months'),
            'weight_kg' => $this->faker->randomFloat(2, 0.5, 40),
            'microchip_code' => $this->faker->boolean(50) ? $this->faker->unique()->numerify('MC-########') : null,
        ];
    }
}
