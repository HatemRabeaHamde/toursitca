<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgencyDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_home_redirects_to_dashboard_route(): void
    {
        $user = $this->createAgencyUser();

        $this->actingAs($user)
            ->get('/agency')
            ->assertRedirect('/agency/dashboard');
    }

    public function test_agency_dashboard_displays_agency_data(): void
    {
        $user = $this->createAgencyUser();
        $agency = $user->agency;

        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $user->id,
            'title' => ['en' => 'Agency Desert Tour'],
            'description' => ['en' => 'Tour description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);

        Availability::query()->create([
            'experience_id' => $experience->id,
            'date' => now()->addDays(3)->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 6,
            'booked_seats' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('agency.dashboard'))
            ->assertOk()
            ->assertSee('Agency Desert Tour');
    }

    private function createAgencyUser(): User
    {
        $user = User::factory()->create([
            'email' => 'agency-dashboard@example.com',
        ]);
        $user->assignRole(Role::findOrCreate('travel_agency', 'web'));

        Agency::query()->create([
            'user_id' => $user->id,
            'name' => 'Agency Dashboard',
            'slug' => 'agency-dashboard',
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return $user->refresh();
    }
}
