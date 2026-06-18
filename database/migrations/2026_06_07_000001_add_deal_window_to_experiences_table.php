<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->timestamp('deal_starts_at')->nullable()->after('original_price');
            $table->timestamp('deal_ends_at')->nullable()->after('deal_starts_at');

            $table->index(['status', 'deal_starts_at', 'deal_ends_at'], 'experiences_status_deal_window_index');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropIndex('experiences_status_deal_window_index');
            $table->dropColumn(['deal_starts_at', 'deal_ends_at']);
        });
    }
};
