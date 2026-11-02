<?php

namespace Tests\Feature\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('site.dashboard.bookings', ['locale' => 'en'], absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public static function dashboardAccounts(): array
    {
        return [
            'admin' => ['admin', null, 'en', '/admin/dashboard'],
            'active agency' => ['travel_agency', 'active', 'en', '/agency/dashboard'],
            'pending agency' => ['travel_agency', 'pending', 'en', '/agency/pending'],
            'suspended agency' => ['travel_agency', 'suspended', 'en', '/agency/pending'],
            'agency without profile' => ['travel_agency', null, 'en', '/agency/pending'],
            'client' => ['user', null, 'en', '/en/dashboard/bookings'],
            'French client' => ['user', null, 'fr', '/fr/dashboard/bookings'],
        ];
    }

    #[DataProvider('dashboardAccounts')]
    public function test_sign_in_ignores_stale_intended_urls_and_opens_own_dashboard(string $role, ?string $status, string $locale, string $destination): void
    {
        $user = $this->dashboardAccount($role, $status, $locale);
        $this->withSession(['url.intended' => '/'])->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect($destination)->assertSessionMissing('url.intended');
        $this->assertAuthenticatedAs($user);
    }

    #[DataProvider('dashboardAccounts')]
    public function test_signed_in_users_opening_login_are_sent_to_own_dashboard(string $role, ?string $status, string $locale, string $destination): void
    {
        $user = $this->dashboardAccount($role, $status, $locale);
        $this->actingAs($user)->get('/login')->assertRedirect($destination);
    }

    private function dashboardAccount(string $role, ?string $status, string $locale): User
    {
        Role::findOrCreate($role, 'web');
        $user = User::factory()->create(['preferred_lang' => $locale]);
        $user->assignRole($role);
        if ($status !== null) {
            Agency::create(['user_id' => $user->id, 'name' => 'Auth agency', 'slug' => 'auth-agency',
                'description' => ['en' => 'Auth test'], 'city' => 'Marrakesh', 'commission_rate' => '15.00', 'status' => $status]);
        }

        return $user;
    }
}
