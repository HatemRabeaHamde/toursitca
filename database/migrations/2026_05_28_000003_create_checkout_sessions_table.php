<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_sessions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('experience_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('experience_option_id')->nullable()->constrained('experience_options')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('availability_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('locale', 12);
            $table->string('currency', 3);
            $table->enum('booking_type', ['group', 'private']);
            $table->json('participants');
            $table->unsignedTinyInteger('participants_count');
            $table->unsignedTinyInteger('charged_seats');
            $table->string('tour_language', 12)->nullable();
            $table->json('price_snapshot');
            $table->enum('payment_method', ['manual', 'pay_later'])->default('manual');
            $table->enum('payment_status', ['pending', 'not_required', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->enum('status', ['active', 'expired', 'completed', 'cancelled'])->default('active');
            $table->timestamp('reserved_until');
            $table->timestamps();

            $table->index(['status', 'reserved_until']);
            $table->index(['availability_id', 'status']);
        });

        Schema::table('bookings', function (Blueprint $table): void {
            $table->foreignId('checkout_session_id')
                ->nullable()
                ->after('id')
                ->constrained('checkout_sessions')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('experience_option_id')
                ->nullable()
                ->after('experience_id')
                ->constrained('experience_options')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->json('participants')->nullable()->after('participants_count');
            $table->unsignedTinyInteger('charged_seats')->nullable()->after('participants');
            $table->string('tour_language', 12)->nullable()->after('charged_seats');
            $table->json('price_snapshot')->nullable()->after('agency_amount');
            $table->enum('payment_method', ['manual', 'pay_later'])->default('manual')->after('price_snapshot');
            $table->enum('payment_status', ['pending', 'not_required', 'paid', 'failed', 'cancelled'])->default('pending')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('checkout_session_id');
            $table->dropConstrainedForeignId('experience_option_id');
            $table->dropColumn([
                'participants',
                'charged_seats',
                'tour_language',
                'price_snapshot',
                'payment_method',
                'payment_status',
            ]);
        });

        Schema::dropIfExists('checkout_sessions');
    }
};
