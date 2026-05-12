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
            'description' => $this->faker->sentence(),
            'dosage_form' => $this->faker->randomElement(['Tabletas', 'Jarabe', 'Inyección', 'Pomada']),
            'stock' => $this->faker->numberBetween(20, 500),
            'reorder_level' => $this->faker->numberBetween(15, 60),
            'price' => $this->faker->randomFloat(2, 5, 60),
            'is_prescription_only' => $this->faker->boolean(70),
        ];
    }
}
