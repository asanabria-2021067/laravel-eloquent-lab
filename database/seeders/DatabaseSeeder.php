<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentMedication;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Medication;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faker = fake();

        $roles = [
            'admin' => Role::create(['name' => 'admin', 'description' => 'Gestión completa del sistema']),
            'veterinarian' => Role::create(['name' => 'veterinarian', 'description' => 'Profesional veterinario']),
            'receptionist' => Role::create(['name' => 'receptionist', 'description' => 'Atención al cliente y agenda']),
        ];

        $adminUsers = User::factory(5)->create();
        $adminUsers->each(fn (User $user) => $user->roles()->attach($roles['admin']->id));

        $receptionistUsers = User::factory(15)->create();
        $receptionistUsers->each(fn (User $user) => $user->roles()->attach($roles['receptionist']->id));

        $veterinarianUsers = User::factory(120)->create();
        $veterinarianUsers->each(fn (User $user) => $user->roles()->attach($roles['veterinarian']->id));

        $veterinarians = $veterinarianUsers->map(fn (User $user) => Veterinarian::factory()->create(['user_id' => $user->id]));
        $veterinarianIds = $veterinarians->pluck('id');

        $services = Service::factory(20)->create();
        $medications = Medication::factory(80)->create();

        $clients = Client::factory(2500)->create();

        $clients->each(function (Client $client) use ($faker, $services, $medications, $veterinarianIds) {
            $pets = Pet::factory($faker->numberBetween(1, 3))->create([
                'client_id' => $client->id,
            ]);

            $pets->each(function (Pet $pet) use ($faker, $services, $medications, $veterinarianIds) {
                $appointments = Appointment::factory($faker->numberBetween(2, 4))->create([
                    'pet_id' => $pet->id,
                    'veterinarian_id' => $faker->randomElement($veterinarianIds),
                    'service_id' => $services->random()->id,
                    'scheduled_at' => $faker->dateTimeBetween('-6 months', '+3 months'),
                ]);

                $appointments->each(function (Appointment $appointment) use ($faker, $services, $medications) {
                    $invoice = Invoice::factory()->create([
                        'appointment_id' => $appointment->id,
                        'issued_at' => $appointment->scheduled_at->copy()->subDays($faker->numberBetween(0, 2)),
                        'due_at' => $appointment->scheduled_at->copy()->addDays($faker->numberBetween(5, 15)),
                        'status' => $faker->randomElement(['issued', 'paid', 'overdue']),
                        'total' => 0,
                    ]);

                    $itemsTotal = 0;
                    $itemsCount = $faker->numberBetween(1, 3);

                    for ($i = 0; $i < $itemsCount; $i++) {
                        $service = $services->random();
                        $quantity = $faker->numberBetween(1, 3);
                        $unitPrice = round($service->base_price + $faker->randomFloat(2, 5, 40), 2);
                        $subtotal = round($quantity * $unitPrice, 2);

                        InvoiceItem::factory()->create([
                            'invoice_id' => $invoice->id,
                            'service_id' => $service->id,
                            'description' => $service->name,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'subtotal' => $subtotal,
                        ]);

                        $itemsTotal += $subtotal;
                    }

                    $invoice->update(['total' => round($itemsTotal, 2)]);

                    $medicationCount = $faker->numberBetween(1, 3);
                    $selectedMedications = Collection::wrap($medications->random($medicationCount));

                    $selectedMedications->each(function ($medication) use ($appointment, $faker) {
                        AppointmentMedication::create([
                            'appointment_id' => $appointment->id,
                            'medication_id' => $medication->id,
                            'dosage_amount' => $faker->randomFloat(2, 0.5, 5),
                            'dosage_unit' => $faker->randomElement(['ml', 'mg', 'tabletas']),
                            'instructions' => $faker->sentence(),
                            'administered_at' => $faker->optional()->dateTimeBetween('-1 month', '+1 day'),
                        ]);
                    });
                });
            });
        });
    }
}
