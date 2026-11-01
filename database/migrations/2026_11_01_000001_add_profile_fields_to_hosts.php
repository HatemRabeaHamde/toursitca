<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Host profile page: quote, story paragraphs and skills (translatable), plus
| spoken languages, the year they became a partner and a hero photo.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->json('quote')->nullable()->after('bio');
            $table->json('story')->nullable()->after('quote');
            $table->json('skills')->nullable()->after('story');
            $table->json('languages')->nullable()->after('skills');
            $table->unsignedSmallInteger('partner_since')->nullable()->after('languages');
            $table->string('cover_url')->nullable()->after('photo_url');
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->dropColumn(['quote', 'story', 'skills', 'languages', 'partner_since', 'cover_url']);
        });
    }
};
