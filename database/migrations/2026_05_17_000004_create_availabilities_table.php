<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date');
            $table->time('time_slot');
            $table->unsignedTinyInteger('max_seats');
            $table->unsignedTinyInteger('booked_seats')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['experience_id', 'date', 'time_slot']);
            $table->index(['experience_id', 'date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
