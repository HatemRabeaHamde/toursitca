<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('title');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->boolean('pickup_enabled')->default(false);
            $table->boolean('private_available')->default(false);
            $table->boolean('pay_later_enabled')->default(false);
            $table->unsignedSmallInteger('cancellation_hours')->nullable();
            $table->enum('price_type', ['per_person', 'per_group'])->default('per_person');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['experience_id', 'status']);
        });

        Schema::create('experience_option_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_option_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('participant_type', 40);
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('currency', 3)->default(config('payment.currency', 'MAD'));
            $table->timestamps();

            $table->unique(['experience_option_id', 'participant_type'], 'option_prices_option_type_unique');
        });

        Schema::create('experience_option_languages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_option_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('language_code', 12);
            $table->enum('type', ['live_guide', 'audio_guide'])->default('live_guide');
            $table->timestamps();

            $table->unique(['experience_option_id', 'language_code', 'type'], 'option_languages_code_type_unique');
        });

        Schema::table('availabilities', function (Blueprint $table): void {
            $table->foreignId('experience_option_id')
                ->nullable()
                ->after('experience_id')
                ->constrained('experience_options')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->unsignedTinyInteger('held_seats')->default(0)->after('booked_seats');
            $table->index(['experience_option_id', 'date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table): void {
            $table->dropIndex(['experience_option_id', 'date', 'is_active']);
            $table->dropConstrainedForeignId('experience_option_id');
            $table->dropColumn('held_seats');
        });

        Schema::dropIfExists('experience_option_languages');
        Schema::dropIfExists('experience_option_prices');
        Schema::dropIfExists('experience_options');
    }
};
