<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('presentation');
            $table->text('description')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(20);
            $table->decimal('unit_cost', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
