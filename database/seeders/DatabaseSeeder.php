<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\AppointmentNote;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Medication;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faker = fake();

        // Users segmented by role so appointments can target real veterinarians.
        User::factory(5)->state(['role' => 'admin'])->create();
        $veterinarians = User::factory(60)->state(['role' => 'veterinarian'])->create();
        $receptionists = User::factory(25)->state(['role' => 'receptionist'])->create();

        $services = Service::factory(12)->create();
        $medications = Medication::factory(60)->create();

        $clients = Client::factory(1600)->create();

        $veterinarianIds = $veterinarians->pluck('id');
        $authorIds = $receptionists->pluck('id');

        $clients->each(function (Client $client) use ($faker, $services, $veterinarianIds, $authorIds) {
            $petCount = $faker->numberBetween(1, 2);
            $pets = Pet::factory($petCount)->create([
                'client_id' => $client->id,
            ]);

            $pets->each(function (Pet $pet) use ($faker, $services, $veterinarianIds, $authorIds) {
                $appointments = Appointment::factory($faker->numberBetween(1, 3))->create([
                    'pet_id' => $pet->id,
                    'service_id' => $services->random()->id,
                    'veterinarian_id' => $faker->randomElement($veterinarianIds),
                    'scheduled_at' => $faker->dateTimeBetween('-4 months', '+2 months'),
                ]);

                $appointments->each(function (Appointment $appointment) use ($faker, $services, $authorIds, $medications) {
                    $invoice = Invoice::factory()->create([
                        'appointment_id' => $appointment->id,
                        'issued_at' => $appointment->scheduled_at->copy()->subDays($faker->numberBetween(0, 2)),
                        'due_at' => $appointment->scheduled_at->copy()->addDays($faker->numberBetween(5, 12)),
                        'status' => $faker->randomElement(['issued', 'paid', 'overdue']),
                        'total' => 0,
                    ]);

                    $itemsTotal = 0;
                    $itemCount = $faker->numberBetween(1, 2);

                    for ($i = 0; $i < $itemCount; $i++) {
                        $service = $services->random();
                        $quantity = $faker->numberBetween(1, 2);
                        $unitPrice = round($service->price + $faker->randomFloat(2, 0, 40), 2);
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

                    $prescriptionCount = $faker->numberBetween(0, 2);

                    if ($prescriptionCount > 0) {
                        Collection::wrap($medications->random($prescriptionCount))->each(function ($medication) use ($appointment) {
                            Prescription::factory()->create([
                                'appointment_id' => $appointment->id,
                                'medication_id' => $medication->id,
                            ]);
                        });
                    }

                    if ($faker->boolean(60)) {
                        $noteCount = $faker->numberBetween(1, 2);
                        AppointmentNote::factory($noteCount)->create([
                            'appointment_id' => $appointment->id,
                            'author_id' => $faker->randomElement($authorIds->all()),
                            'noted_at' => $appointment->scheduled_at->copy()->subDays($faker->numberBetween(0, 5)),
                        ]);
                    }
                });
            });
        });
    }
}
