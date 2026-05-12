<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 3);
        $unitPrice = $this->faker->randomFloat(2, 10, 200);

        return [
            'description' => $this->faker->sentence(3),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => round($quantity * $unitPrice, 2),
        ];
    }
}
