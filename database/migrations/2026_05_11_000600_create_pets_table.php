<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->enum('sex', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('microchip_code')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
