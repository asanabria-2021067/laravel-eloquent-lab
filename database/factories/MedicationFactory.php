<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word().' Vet',
            'presentation' => $this->faker->randomElement(['Tabletas', 'Jarabe', 'Inyectable', 'Pomada']),
            'description' => $this->faker->sentence(),
            'stock' => $this->faker->numberBetween(30, 400),
            'reorder_level' => $this->faker->numberBetween(20, 80),
            'unit_cost' => $this->faker->randomFloat(2, 5, 60),
        ];
    }
}
