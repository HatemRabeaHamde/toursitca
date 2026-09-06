<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('preferred_lang', 5)->default('en')->after('avatar');
            $table->enum('status', ['active', 'banned'])->default('active')->after('preferred_lang');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'preferred_lang', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
