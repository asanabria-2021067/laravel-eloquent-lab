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
            'price' => $this->faker->randomFloat(2, 20, 260),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'is_active' => true,
        ];
    }
}
