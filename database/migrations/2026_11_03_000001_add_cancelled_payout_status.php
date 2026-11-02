<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->unique('checkout_session_id', 'bookings_checkout_unique');
        });
        Schema::table('payouts', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropUnique('bookings_checkout_unique');
        });
        // Retain cancelled financial history on rollback; never silently make it payable again.
    }
};
