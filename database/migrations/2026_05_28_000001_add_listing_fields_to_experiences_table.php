<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('agency_id');
            $table->decimal('original_price', 10, 2)->nullable()->after('price_per_person');
            $table->boolean('pickup_enabled')->default(false)->after('private_price');
            $table->boolean('is_top_rated')->default(false)->after('reviews_count');
        });

        DB::table('experiences')
            ->orderBy('id')
            ->select(['id', 'title'])
            ->chunk(100, function ($experiences): void {
                foreach ($experiences as $experience) {
                    $title = json_decode($experience->title, true);
                    $base = is_array($title) ? (string) reset($title) : 'experience-'.$experience->id;
                    $slug = Str::slug($base) ?: 'experience-'.$experience->id;

                    DB::table('experiences')
                        ->where('id', $experience->id)
                        ->update(['slug' => $slug.'-'.$experience->id]);
                }
            });

        Schema::table('experiences', function (Blueprint $table): void {
            $table->unique('slug');
            $table->index(['status', 'pickup_enabled']);
            $table->index(['status', 'is_top_rated']);
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropIndex(['status', 'pickup_enabled']);
            $table->dropIndex(['status', 'is_top_rated']);
            $table->dropColumn(['slug', 'original_price', 'pickup_enabled', 'is_top_rated']);
        });
    }
};
