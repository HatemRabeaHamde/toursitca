<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Discover Morocco: hosts, interests, guide content and the links between
 * destinations, interests, stories and experiences.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('agency_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->json('role');
            $table->string('city')->nullable()->index();
            $table->json('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('licence_number', 40)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('interests', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('summary')->nullable();
            $table->string('image_url')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->json('category_slugs')->nullable();   // experience categories this interest covers
            $table->json('guide')->nullable();            // translatable structured page copy
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::table('destinations', function (Blueprint $table): void {
            $table->string('region')->nullable()->after('city');
            $table->json('summary')->nullable()->after('region');
            $table->json('tags')->nullable()->after('summary');   // short translatable labels for city cards
            $table->string('hero_image_url')->nullable()->after('image_url');
            $table->decimal('map_x', 5, 2)->nullable()->after('hero_image_url');   // pin position, % of map width
            $table->decimal('map_y', 5, 2)->nullable()->after('map_x');            // pin position, % of map height
            $table->json('guide')->nullable()->after('map_y');
        });

        Schema::create('destination_interest', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();
            $table->json('note')->nullable();   // "Tanjia and evening food stalls."
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['destination_id', 'interest_id']);
        });

        Schema::create('guide_blocks', function (Blueprint $table): void {
            $table->id();
            $table->morphs('guideable');                 // Destination | Interest
            $table->string('type', 32);                  // landmark | dish | tip | origin | craft | also
            $table->json('title')->nullable();
            $table->json('body')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['guideable_type', 'guideable_id', 'type', 'sort_order']);
        });

        Schema::table('experiences', function (Blueprint $table): void {
            $table->foreignId('host_id')->nullable()->after('agency_id')->constrained()->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('testimonials', function (Blueprint $table): void {
            $table->string('image_url')->nullable()->after('avatar_url');
            $table->string('city')->nullable()->after('image_url');
            $table->unsignedTinyInteger('days')->nullable()->after('city');
            $table->foreignId('destination_id')->nullable()->after('days')->constrained()->nullOnDelete();
            $table->foreignId('interest_id')->nullable()->after('destination_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('interest_id');
            $table->dropConstrainedForeignId('destination_id');
            $table->dropColumn(['image_url', 'city', 'days']);
        });

        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('host_id');
        });

        Schema::dropIfExists('guide_blocks');
        Schema::dropIfExists('destination_interest');

        Schema::table('destinations', function (Blueprint $table): void {
            $table->dropColumn(['region', 'summary', 'tags', 'hero_image_url', 'map_x', 'map_y', 'guide']);
        });

        Schema::dropIfExists('interests');
        Schema::dropIfExists('hosts');
    }
};
