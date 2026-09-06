<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DemoAccessSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureRoles();

        $admin = User::query()->updateOrCreate(
            ['email' => $this->stringConfig('services.demo_access.admin_email')],
            [
                'name' => $this->stringConfig('services.demo_access.admin_name'),
                'password' => Hash::make($this->password('admin_password')),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $admin->assignRole('admin');

        $agencyOwner = User::query()->updateOrCreate(
            ['email' => $this->stringConfig('services.demo_access.agency_email')],
            [
                'name' => $this->stringConfig('services.demo_access.agency_owner_name'),
                'password' => Hash::make($this->password('agency_password')),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $agencyOwner->assignRole('travel_agency');

        Agency::query()->updateOrCreate(
            ['slug' => 'tourstica-demo-agency'],
            [
                'user_id' => $agencyOwner->id,
                'name' => $this->stringConfig('services.demo_access.agency_name'),
                'description' => [
                    'en' => 'TourstiCa demo agency account for dashboard access.',
                    'fr' => 'Compte agence de démonstration TourstiCa pour accéder au tableau de bord.',
                    'nl' => 'TourstiCa demo agency account for dashboard access.',
                ],
                'commission_rate' => '15.00',
                'status' => 'active',
                'city' => 'Marrakesh',
                'phone' => null,
                'languages' => ['en', 'fr', 'nl'],
                'is_platform' => false,
            ],
        );
    }

    private function ensureRoles(): void
    {
        foreach (['admin', 'travel_agency', 'user'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }

    private function password(string $key): string
    {
        $password = config("services.demo_access.{$key}");

        if (is_string($password) && $password !== '') {
            return $password;
        }

        if (! app()->environment('production')) {
            return 'password';
        }

        throw new RuntimeException("DEMO_{$this->envPasswordName($key)} must be set before seeding demo access users.");
    }

    private function envPasswordName(string $key): string
    {
        return match ($key) {
            'admin_password' => 'ADMIN_PASSWORD',
            'agency_password' => 'AGENCY_PASSWORD',
            default => strtoupper($key),
        };
    }

    private function stringConfig(string $key): string
    {
        $value = config($key);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException("Missing string config value for [{$key}].");
        }

        return $value;
    }
}
