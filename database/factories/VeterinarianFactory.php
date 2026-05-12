<?php

namespace Database\Factories;

use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeterinarianFactory extends Factory
{
    protected $model = Veterinarian::class;

    public function definition(): array
    {
        return [
            'specialty' => $this->faker->randomElement([
                'Dermatología',
                'Odontología',
                'Cirugía',
                'Emergencias',
                'Medicina preventiva',
            ]),
            'license_number' => $this->faker->unique()->numerify('LIC-#####'),
            'years_experience' => $this->faker->numberBetween(1, 35),
            'biography' => $this->faker->paragraph(),
            'available_from' => $this->faker->time('H:i'),
            'available_to' => $this->faker->time('H:i'),
            'consultation_fee' => $this->faker->randomFloat(2, 20, 120),
        ];
    }
}
