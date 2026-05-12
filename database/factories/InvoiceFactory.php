<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $issuedAt = $this->faker->dateTimeBetween('-3 months', 'now');

        return [
            'invoice_number' => $this->faker->unique()->numerify('INV-#####'),
            'issued_at' => $issuedAt,
            'due_at' => (clone $issuedAt)->modify('+7 days'),
            'status' => $this->faker->randomElement(['issued', 'paid', 'overdue']),
            'total' => 0,
        ];
    }
}
