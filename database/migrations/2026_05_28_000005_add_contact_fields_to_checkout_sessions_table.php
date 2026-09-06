<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->string('contact_first_name', 100)->nullable()->after('pickup_lng');
            $table->string('contact_last_name', 100)->nullable()->after('contact_first_name');
            $table->string('contact_email', 150)->nullable()->after('contact_last_name');
            $table->string('contact_phone', 50)->nullable()->after('contact_email');
            $table->string('contact_country', 80)->nullable()->after('contact_phone');
            $table->text('special_requests')->nullable()->after('contact_country');
        });
    }

    public function down(): void
    {
        Schema::table('checkout_sessions', function (Blueprint $table): void {
            $table->dropColumn([
                'contact_first_name',
                'contact_last_name',
                'contact_email',
                'contact_phone',
                'contact_country',
                'special_requests',
            ]);
        });
    }
};
