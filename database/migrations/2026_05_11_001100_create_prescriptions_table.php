<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained()->cascadeOnDelete();
            $table->decimal('dosage_amount', 5, 2);
            $table->string('dosage_unit', 20);
            $table->string('frequency', 50);
            $table->unsignedTinyInteger('duration_days');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['appointment_id', 'medication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
