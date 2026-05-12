<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('veterinarians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('license_number')->unique();
            $table->unsignedTinyInteger('years_experience');
            $table->text('biography')->nullable();
            $table->time('available_from');
            $table->time('available_to');
            $table->decimal('consultation_fee', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('veterinarians');
    }
};
