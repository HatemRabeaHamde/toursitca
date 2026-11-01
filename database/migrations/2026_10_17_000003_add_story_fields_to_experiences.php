<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Experience detail v2: translatable highlight lines shown as pills under the
| overview, and an optional photo per itinerary step (the open accordion day).
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->json('highlights')->nullable()->after('exclusions');
        });

        Schema::table('experience_itinerary_items', function (Blueprint $table): void {
            $table->string('image')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('experience_itinerary_items', function (Blueprint $table): void {
            $table->dropColumn('image');
        });

        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropColumn('highlights');
        });
    }
};
