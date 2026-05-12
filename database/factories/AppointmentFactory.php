<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'scheduled_at' => $this->faker->dateTimeBetween('-6 months', '+6 months'),
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'cancelled']),
            'follow_up_required' => $this->faker->boolean(25),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
