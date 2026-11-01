<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| Hosts are now the public face of an agency. `reviewed_at` is empty while a
| host waits for an admin to look at it (new profile, or a changed licence).
| Hosts that exist today were curated by hand, so they count as reviewed.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->timestamp('reviewed_at')->nullable()->after('is_verified');
        });

        DB::table('hosts')->update(['reviewed_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table): void {
            $table->dropColumn('reviewed_at');
        });
    }
};
