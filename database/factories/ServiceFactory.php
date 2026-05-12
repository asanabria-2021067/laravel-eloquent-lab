<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Consulta general',
                'Vacunación',
                'Profilaxis dental',
                'Esterilización',
                'Ecografía',
                'Radiografía',
                'Control nutricional',
                'Urgencias 24h',
            ]),
            'description' => $this->faker->sentence(),
            'base_price' => $this->faker->randomFloat(2, 15, 250),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'is_active' => true,
        ];
    }
}
