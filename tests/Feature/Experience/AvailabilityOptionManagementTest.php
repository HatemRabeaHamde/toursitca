<?php

namespace Tests\Feature\Experience;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AvailabilityOptionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_availability_for_experience_option(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $experience = $this->createExperience();
        $option = $this->createOption($experience);

        $this->actingAs($admin)
            ->post(route('admin.availability.store'), $this->payload($experience, $option))
            ->assertRedirect(route('admin.availability.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('availabilities', [
            'experience_id' => $experience->id,
            'experience_option_id' => $option->id,
            'max_seats' => 8,
        ]);
    }

    public function test_availability_option_must_belong_to_selected_experience(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $experience = $this->createExperience();
        $otherOption = $this->createOption($this->createExperience());

        $this->actingAs($admin)
            ->post(route('admin.availability.store'), $this->payload($experience, $otherOption))
            ->assertSessionHasErrors('experience_option_id');

        $this->assertDatabaseCount('availabilities', 0);
    }

    public function test_agency_can_only_create_option_availability_for_own_experience(): void
    {
        $agencyUser = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $agencyUser->id,
            'name' => 'Own Agency',
            'slug' => 'own-agency',
            'description' => ['en' => 'Own agency'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);
        $ownExperience = $this->createExperience($agency, $agencyUser);
        $ownOption = $this->createOption($ownExperience);
        $otherOption = $this->createOption($this->createExperience());

        $this->actingAs($agencyUser)
            ->post(route('agency.availability.store'), $this->payload($ownExperience, $ownOption))
            ->assertRedirect(route('agency.availability.index'))
            ->assertSessionHasNoErrors();

        $this->actingAs($agencyUser)
            ->post(route('agency.availability.store'), array_merge(
                $this->payload($ownExperience, $otherOption),
                ['date' => now()->addDays(3)->toDateString()],
            ))
            ->assertSessionHasErrors('experience_option_id');

        $this->assertSame(1, Availability::query()->count());
    }

    private function payload(Experience $experience, ?ExperienceOption $option = null): array
    {
        return [
            'experience_id' => $experience->id,
            'experience_option_id' => $option?->id,
            'date' => now()->addDays(2)->toDateString(),
            'time_slot' => '09:00',
            'max_seats' => 8,
            'is_active' => 1,
        ];
    }

    private function createExperience(?Agency $agency = null, ?User $owner = null): Experience
    {
        $owner ??= User::factory()->create();
        $agency ??= Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Availability Agency '.uniqid(),
            'slug' => 'availability-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Availability Tour '.uniqid()],
            'description' => ['en' => 'Tour description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);
    }

    private function createOption(Experience $experience): ExperienceOption
    {
        return ExperienceOption::query()->create([
            'experience_id' => $experience->id,
            'title' => ['en' => 'Morning option'],
            'price_type' => 'per_person',
            'status' => 'active',
        ]);
    }
}
