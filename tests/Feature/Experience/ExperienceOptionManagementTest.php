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

class ExperienceOptionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_experience_option(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $experience = $this->createExperience();

        $this->actingAs($admin)
            ->post(route('admin.experiences.options.store', $experience), $this->payload())
            ->assertRedirect(route('admin.experiences.options.index', $experience))
            ->assertSessionHasNoErrors();

        $option = ExperienceOption::query()->firstOrFail();

        $this->assertSame('Morning option', $option->getTranslation('title', 'en'));
        $this->assertSame(2, $option->prices()->count());
        $this->assertSame(2, $option->languages()->count());

        $this->actingAs($admin)
            ->patch(route('admin.experiences.options.update', ['experience' => $experience, 'option' => $option]), array_merge($this->payload(), [
                'title' => ['en' => 'Updated option'],
                'adult_price' => '120.00',
            ]))
            ->assertRedirect(route('admin.experiences.options.index', $experience))
            ->assertSessionHasNoErrors();

        $option->refresh();

        $this->assertSame('Updated option', $option->getTranslation('title', 'en'));
        $this->assertSame('120.00', $option->prices()->where('participant_type', 'adult')->first()->price);
    }

    public function test_agency_cannot_manage_options_for_another_agency_experience(): void
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

        $otherExperience = $this->createExperience();

        $this->actingAs($agencyUser)
            ->post(route('agency.experiences.options.store', $otherExperience), $this->payload())
            ->assertForbidden();
    }

    public function test_option_with_availability_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $experience = $this->createExperience();
        $option = ExperienceOption::query()->create([
            'experience_id' => $experience->id,
            'title' => ['en' => 'Morning option'],
            'price_type' => 'per_person',
            'status' => 'active',
        ]);
        Availability::query()->create([
            'experience_id' => $experience->id,
            'experience_option_id' => $option->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.experiences.options.destroy', ['experience' => $experience, 'option' => $option]))
            ->assertRedirect(route('admin.experiences.options.index', $experience))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('experience_options', ['id' => $option->id]);
    }

    private function payload(): array
    {
        return [
            'title' => ['en' => 'Morning option'],
            'duration_minutes' => 240,
            'pickup_enabled' => 1,
            'private_available' => 1,
            'pay_later_enabled' => 1,
            'cancellation_hours' => 24,
            'price_type' => 'per_person',
            'status' => 'active',
            'adult_price' => '100.00',
            'adult_original_price' => '120.00',
            'child_price' => '60.00',
            'child_original_price' => '80.00',
            'languages_text' => "en\nfr",
        ];
    }

    private function createExperience(): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Option Agency '.uniqid(),
            'slug' => 'option-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Option Experience'],
            'description' => ['en' => 'Option description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);
    }
}
