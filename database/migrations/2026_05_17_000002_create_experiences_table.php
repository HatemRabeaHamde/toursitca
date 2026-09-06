<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->json('title');
            $table->json('description');
            $table->string('category', 80);
            $table->enum('difficulty', ['easy', 'moderate', 'hard'])->default('easy');
            $table->decimal('duration_hours', 4, 1);
            $table->unsignedTinyInteger('max_group_size');
            $table->decimal('price_per_person', 10, 2);
            $table->decimal('private_price', 10, 2)->nullable();
            $table->string('location_city', 80);
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->text('meeting_point')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->enum('status', ['draft', 'published', 'unpublished'])->default('draft');
            $table->string('thumbnail')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'location_city', 'category']);
            $table->index(['status', 'price_per_person']);
            $table->index(['agency_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
