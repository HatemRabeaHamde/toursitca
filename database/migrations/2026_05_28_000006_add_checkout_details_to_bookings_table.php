<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->enum('pickup_status', ['add_now', 'unknown', 'not_required'])->nullable()->after('tour_language');
            $table->string('pickup_address')->nullable()->after('pickup_status');
            $table->decimal('pickup_lat', 10, 7)->nullable()->after('pickup_address');
            $table->decimal('pickup_lng', 10, 7)->nullable()->after('pickup_lat');
            $table->string('contact_country', 80)->nullable()->after('guest_phone');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn([
                'pickup_status',
                'pickup_address',
                'pickup_lat',
                'pickup_lng',
                'contact_country',
            ]);
        });
    }
};
