<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            // Existing published rows remain valid; new authoring explicitly starts in draft.
            $table->string('review_status')->default('approved')->index();
            $table->unsignedInteger('published_version')->default(1);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
        });
        DB::table('experiences')->where('status', '!=', 'published')->update(['review_status' => 'draft']);
        Schema::create('experience_change_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('base_version');
            $table->string('status')->default('draft')->index();
            $table->json('payload');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_change_requests');
        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropIndex(['review_status']);
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['review_status', 'published_version', 'reviewed_at']);
        });
    }
};
