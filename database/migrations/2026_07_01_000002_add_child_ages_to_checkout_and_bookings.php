<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->json('child_ages')->nullable()->after('participants');
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->json('child_ages')->nullable()->after('participants');
        });
    }

    public function down(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->dropColumn('child_ages');
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn('child_ages');
        });
    }
};
