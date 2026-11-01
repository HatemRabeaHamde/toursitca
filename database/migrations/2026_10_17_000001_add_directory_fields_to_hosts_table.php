<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local hosts directory: each host belongs to a region (destination) and has
 * a main specialty (interest), so the directory can be filtered by both.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->foreignId('destination_id')->nullable()->after('city')->constrained('destinations')->nullOnDelete();
            $table->foreignId('interest_id')->nullable()->after('destination_id')->constrained('interests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('interest_id');
            $table->dropConstrainedForeignId('destination_id');
        });
    }
};
