<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformAgencySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->firstOrCreate(
            ['email' => env('PLATFORM_AGENCY_EMAIL', 'platform@morocco-tourism.test')],
            [
                'name' => env('PLATFORM_AGENCY_OWNER_NAME', 'Platform Experiences'),
                'password' => Hash::make(Str::password(16)),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        Agency::query()->updateOrCreate(
            ['slug' => 'platform'],
            [
                'user_id' => $owner->id,
                'name' => 'Platform',
                'description' => [
                    'en' => 'Platform-owned experiences.',
                    'pl' => 'Doświadczenia prowadzone przez platformę.',
                    'fr' => 'Expériences gérées par la plateforme.',
                ],
                'commission_rate' => '0.00',
                'status' => 'active',
                'city' => 'Marrakesh',
                'phone' => null,
                'languages' => ['en', 'pl', 'fr'],
                'is_platform' => true,
            ],
        );
    }
}
