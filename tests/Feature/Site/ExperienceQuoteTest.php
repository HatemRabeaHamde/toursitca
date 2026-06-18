<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Experience\Models\ExperienceOptionLanguage;
use App\Domain\Experience\Models\ExperienceOptionPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceQuoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_quote_uses_participant_option_prices(): void
    {
        $experience = $this->createExperience();
        $option = $this->createOption($experience);
        $availability = $this->createAvailability($experience, $option);

        ExperienceOptionPrice::query()->create([
            'experience_option_id' => $option->id,
            'participant_type' => 'adult',
            'price' => '100.00',
            'original_price' => '120.00',
            'currency' => 'MAD',
        ]);
        ExperienceOptionPrice::query()->create([
            'experience_option_id' => $option->id,
            'participant_type' => 'child',
            'price' => '60.00',
            'original_price' => '80.00',
            'currency' => 'MAD',
        ]);
        ExperienceOptionLanguage::query()->create([
            'experience_option_id' => $option->id,
            'language_code' => 'en',
            'type' => 'live_guide',
        ]);

        $this->postJson(route('site.experiences.quote', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]), [
            'booking_type' => 'group',
            'participants' => [
                'adult' => 2,
                'child' => 1,
            ],
            'option_id' => $option->id,
            'availability_id' => $availability->id,
            'language' => 'en',
        ])
            ->assertOk()
            ->assertJsonPath('data.participants_count', 3)
            ->assertJsonPath('data.charged_seats', 3)
            ->assertJsonPath('data.subtotal', '260.00')
            ->assertJsonPath('data.original_subtotal', '320.00')
            ->assertJsonPath('data.discount_amount', '60.00')
            ->assertJsonPath('data.total', '260.00');
    }

    public function test_private_quote_without_private_price_charges_all_seats(): void
    {
        $experience = $this->createExperience([
            'price_per_person' => '100.00',
            'private_price' => null,
            'max_group_size' => 10,
        ]);
        $availability = $this->createAvailability($experience, null, [
            'max_seats' => 10,
        ]);

        $this->postJson(route('site.experiences.quote', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]), [
            'booking_type' => 'private',
            'participants' => [
                'adult' => 3,
            ],
            'availability_id' => $availability->id,
        ])
            ->assertOk()
            ->assertJsonPath('data.participants_count', 3)
            ->assertJsonPath('data.charged_seats', 10)
            ->assertJsonPath('data.total', '1000.00');
    }

    public function test_quote_rejects_unavailable_language(): void
    {
        $experience = $this->createExperience();
        $option = $this->createOption($experience);

        ExperienceOptionLanguage::query()->create([
            'experience_option_id' => $option->id,
            'language_code' => 'fr',
            'type' => 'live_guide',
        ]);

        $this->postJson(route('site.experiences.quote', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]), [
            'booking_type' => 'group',
            'participants_count' => 1,
            'option_id' => $option->id,
            'language' => 'en',
        ])->assertUnprocessable();
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Quote Agency '.uniqid(),
            'slug' => 'quote-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create(array_merge([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Quote Experience'],
            'description' => ['en' => 'Quote description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ], $overrides));
    }

    private function createOption(Experience $experience): ExperienceOption
    {
        return ExperienceOption::query()->create([
            'experience_id' => $experience->id,
            'title' => ['en' => 'Morning option'],
            'duration_minutes' => 240,
            'pickup_enabled' => true,
            'private_available' => true,
            'price_type' => 'per_person',
            'status' => 'active',
        ]);
    }

    private function createAvailability(Experience $experience, ?ExperienceOption $option = null, array $overrides = []): Availability
    {
        return Availability::query()->create(array_merge([
            'experience_id' => $experience->id,
            'experience_option_id' => $option?->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 0,
            'is_active' => true,
        ], $overrides));
    }
}
