<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_itinerary_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->enum('type', ['start', 'transport', 'stop', 'activity', 'dropoff'])->default('stop');
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->json('location_name')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->boolean('is_main_stop')->default(false);
            $table->string('icon', 40)->nullable();
            $table->timestamps();

            $table->index(['experience_id', 'sort_order']);
            $table->index(['experience_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_itinerary_items');
    }
};
