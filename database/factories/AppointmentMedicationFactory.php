<?php

namespace Database\Factories;

use App\Models\AppointmentMedication;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentMedicationFactory extends Factory
{
    protected $model = AppointmentMedication::class;

    public function definition(): array
    {
        return [
            'dosage_amount' => $this->faker->randomFloat(2, 0.5, 5),
            'dosage_unit' => $this->faker->randomElement(['ml', 'mg', 'tabletas']),
            'instructions' => $this->faker->sentence(),
            'administered_at' => $this->faker->optional()->dateTimeBetween('-1 month', '+1 day'),
        ];
    }
}
