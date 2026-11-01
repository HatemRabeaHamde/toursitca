<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('section');
            $table->string('key');
            $table->json('value')->nullable();
            $table->json('draft_value')->nullable();
            $table->string('type');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['page', 'section', 'key']);
        });
        Schema::create('site_content_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_content_id')->constrained()->cascadeOnDelete();
            $table->json('value')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_content_revisions');
        Schema::dropIfExists('site_contents');
    }
};
