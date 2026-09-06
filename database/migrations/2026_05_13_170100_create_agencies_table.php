<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(15.00);
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->string('city', 80)->nullable();
            $table->string('phone', 20)->nullable();
            $table->json('languages')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
