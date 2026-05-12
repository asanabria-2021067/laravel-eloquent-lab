<?php

namespace Database\Factories;

use App\Models\AppointmentNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentNoteFactory extends Factory
{
    protected $model = AppointmentNote::class;

    public function definition(): array
    {
        return [
            'body' => $this->faker->paragraph(),
            'noted_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
