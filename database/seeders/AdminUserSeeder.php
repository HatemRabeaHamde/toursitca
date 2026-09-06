<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('services.admin.email');
        $password = config('services.admin.password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('ADMIN_PASSWORD must be set before seeding the admin user.');
        }

        $admin = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('services.admin.name'),
                'password' => Hash::make($password),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $admin->assignRole('admin');
    }
}
