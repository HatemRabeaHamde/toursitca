<?php

namespace Tests\Feature\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PlatformAgencyExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_platform_agency_owned_by_admin(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.agencies.store'), [
                'agency_name' => 'Admin Trips',
                'city' => 'Marrakesh',
                'phone' => '+212600000000',
                'description' => 'Platform-owned trips.',
            ])
            ->assertRedirect(route('admin.agencies.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas(Agency::class, [
            'user_id' => $admin->id,
            'name' => 'Admin Trips',
            'commission_rate' => '0.00',
            'status' => 'active',
            'is_platform' => true,
        ]);
    }

    public function test_admin_can_create_experience_without_selecting_agency_when_platform_agency_exists(): void
    {
        $admin = $this->createAdmin();
        $platformAgency = Agency::query()->create([
            'user_id' => $admin->id,
            'name' => 'Admin Trips',
            'slug' => 'admin-trips',
            'description' => ['en' => 'Platform-owned trips.'],
            'commission_rate' => '0.00',
            'status' => 'active',
            'city' => 'Marrakesh',
            'languages' => ['en', 'pl', 'fr'],
            'is_platform' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.experiences.store'), $this->experiencePayload())
            ->assertRedirect(route('admin.experiences.index'))
            ->assertSessionHasNoErrors();

        $experience = Experience::query()->firstOrFail();

        $this->assertSame($platformAgency->id, $experience->agency_id);
        $this->assertSame($admin->id, $experience->created_by);
    }

    public function test_admin_without_platform_agency_is_redirected_to_create_one(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.experiences.store'), $this->experiencePayload())
            ->assertRedirect(route('admin.agencies.create'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('experiences', 0);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        return $admin;
    }

    private function experiencePayload(): array
    {
        return [
            'title' => ['en' => 'Admin Desert Tour'],
            'description' => ['en' => 'A platform-owned desert tour.'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
        ];
    }
}
