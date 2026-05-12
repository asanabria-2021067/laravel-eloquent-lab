<?php

namespace Database\Factories;

use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    public function definition(): array
    {
        return [
            'dosage_amount' => $this->faker->randomFloat(2, 0.5, 3),
            'dosage_unit' => $this->faker->randomElement(['ml', 'mg', 'tabletas']),
            'frequency' => $this->faker->randomElement(['cada 8h', 'cada 12h', 'una vez al día']),
            'duration_days' => $this->faker->numberBetween(3, 14),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
