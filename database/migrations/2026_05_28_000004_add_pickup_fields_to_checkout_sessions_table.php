<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->enum('pickup_status', ['add_now', 'unknown', 'not_required'])->default('unknown')->after('tour_language');
            $table->string('pickup_address')->nullable()->after('pickup_status');
            $table->decimal('pickup_lat', 10, 7)->nullable()->after('pickup_address');
            $table->decimal('pickup_lng', 10, 7)->nullable()->after('pickup_lat');
        });
    }

    public function down(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->dropColumn(['pickup_status', 'pickup_address', 'pickup_lat', 'pickup_lng']);
        });
    }
};
