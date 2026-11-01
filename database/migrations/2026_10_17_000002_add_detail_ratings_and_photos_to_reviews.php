<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Traveller reviews page: optional detail ratings (1–5) and traveller photos.
 * Both are optional — older reviews simply have none.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->unsignedTinyInteger('rating_host')->nullable()->after('rating');
            $table->unsignedTinyInteger('rating_organisation')->nullable()->after('rating_host');
            $table->unsignedTinyInteger('rating_pickup')->nullable()->after('rating_organisation');
            $table->unsignedTinyInteger('rating_value')->nullable()->after('rating_pickup');
        });

        Schema::create('review_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['review_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_photos');

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropColumn(['rating_host', 'rating_organisation', 'rating_pickup', 'rating_value']);
        });
    }
};
