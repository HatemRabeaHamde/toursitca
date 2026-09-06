<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('experience_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1–5
            $table->text('body');
            $table->boolean('is_visible')->default(true);
            $table->text('agency_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique('booking_id'); // one review per booking
            $table->index(['experience_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
