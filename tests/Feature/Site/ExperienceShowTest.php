<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceMedia;
use App\Domain\Experience\Models\ExperienceOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_experience_can_be_opened_by_slug(): void
    {
        $experience = $this->createExperience([
            'title' => ['en' => 'Marrakech Balloon Ride'],
            'description' => ['en' => 'Fly over Marrakech at sunrise.'],
            'private_price' => '600.00',
            'pickup_enabled' => true,
            'is_top_rated' => true,
            'rating_avg' => '4.80',
            'reviews_count' => 120,
            'inclusions' => ['en' => ['Breakfast', 'Pickup']],
        ]);
        $this->createMedia($experience);
        $this->createAvailability($experience);

        $this->get(route('site.experiences.show', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]))
            ->assertOk()
            ->assertViewIs('site.experiences.detail-v1')
            ->assertSee('Marrakech Balloon Ride')
            ->assertSee('Fly over Marrakech at sunrise.')
            ->assertSee('Choose a date')
            ->assertSee('Request to book')
            ->assertSee('value="private"', false)
            ->assertSee('Your host confirms the exact pick-up time with you.')
            ->assertSee('Select dates')
            ->assertSee('Group type')
            ->assertSee('Breakfast')
            // Booking card keeps the existing quote + checkout endpoints.
            ->assertViewHas('page', fn ($page): bool => $page->booking['store']['quoteUrl'] === route('site.experiences.quote', ['locale' => 'en', 'experience' => $experience->slug])
                && $page->booking['store']['checkoutUrl'] === route('site.checkout.start', ['locale' => 'en']));
    }

    public function test_show_page_tells_the_trip_story_and_links_reviews(): void
    {
        $experience = $this->createExperience([
            'title' => ['en' => 'Desert Nights'],
            'duration_hours' => '120.0',
            'highlights' => ['en' => ['Camel trek at sunset']],
            'inclusions' => ['en' => ['Local guide']],
            'exclusions' => ['en' => ['flights', 'tips']],
        ]);
        $this->createMedia($experience);
        $experience->itineraryItems()->create(['sort_order' => 0, 'type' => 'stop', 'title' => ['en' => 'Marrakech to the dunes'], 'description' => ['en' => 'Over the High Atlas.'], 'image' => 'images/experiences/ait-benhaddou.jpg']);
        $experience->itineraryItems()->create(['sort_order' => 1, 'type' => 'stop', 'title' => ['en' => 'Back to Marrakech']]);

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))
            ->assertOk()
            ->assertSee('5 days')
            ->assertSee('Camel trek at sunset')
            ->assertSeeInOrder(['Day 1', 'Marrakech to the dunes', 'Over the High Atlas.', 'Day 2', 'Back to Marrakech'])
            ->assertSee('images/experiences/ait-benhaddou.jpg', false)
            ->assertSee('Local guide')
            ->assertSeeInOrder(['Not included', 'Flights', 'Tips'])
            ->assertSee('View photo')
            ->assertDontSee('id="reviews"', false)
            ->assertDontSee('id="location"', false);
    }

    public function test_show_page_draws_the_route_from_real_coordinates(): void
    {
        $experience = $this->createExperience(['title' => ['en' => 'Atlas Crossing']]);
        $experience->itineraryItems()->create(['sort_order' => 0, 'type' => 'stop', 'title' => ['en' => 'Start'], 'location_name' => ['en' => 'Marrakech'], 'location_lat' => 31.6295, 'location_lng' => -7.9811]);
        $experience->itineraryItems()->create(['sort_order' => 1, 'type' => 'stop', 'title' => ['en' => 'End'], 'location_name' => ['en' => 'Telouet'], 'location_lat' => 31.2889, 'location_lng' => -7.2383]);

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))
            ->assertOk()
            ->assertSee('Location &amp; meeting point', false)
            ->assertSee('We’ll meet in Marrakech')
            ->assertSee('query=31.6295,-7.9811', false)
            ->assertSee('Marrakech → Telouet');
    }

    public function test_show_page_summarises_reviews_by_star_rating(): void
    {
        $experience = $this->createExperience();
        $traveller = User::factory()->create();

        $availability = $this->createAvailability($experience);

        foreach ([5, 5, 5, 4] as $rating) {
            $booking = Booking::query()->create([
                'user_id' => $traveller->id,
                'experience_id' => $experience->id,
                'availability_id' => $availability->id,
                'booking_type' => 'group',
                'participants_count' => 1,
                'unit_price' => '100.00',
                'total_price' => '100.00',
                'commission_rate' => '15.00',
                'commission_amount' => '15.00',
                'agency_amount' => '85.00',
                'status' => 'completed',
                'guest_name' => 'Traveller',
                'guest_email' => 'traveller@example.test',
            ]);
            $experience->reviews()->create(['booking_id' => $booking->id, 'user_id' => $traveller->id, 'rating' => $rating, 'body' => 'Lovely trip', 'is_visible' => true]);
        }

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))
            ->assertOk()
            ->assertSee('Based on 4 reviews')
            ->assertSeeInOrder(['5 ★', '75%', '4 ★', '25%', '3 ★', '0%'], false);
    }

    public function test_options_get_their_own_section_instead_of_a_dropdown(): void
    {
        $experience = $this->createExperience(['title' => ['en' => 'Atlas Days']]);
        $this->createMedia($experience);
        $this->createAvailability($experience);
        $standard = $this->createOption($experience, 'Standard day trip', ['adult' => '100.00', 'child' => '60.00'], ['pickup_enabled' => true, 'cancellation_hours' => 24, 'duration_minutes' => 480], ['en', 'fr']);
        $this->createOption($experience, 'Private 3-day adventure', ['group' => '900.00'], ['price_type' => 'per_group', 'private_available' => true]);

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))
            ->assertOk()
            ->assertSee('id="options"', false)
            ->assertSee('Choose your option')
            ->assertSee('Pick a date to see the options')
            ->assertSee('Private 3-day adventure')
            ->assertSee('Standard day trip')
            ->assertSee('role="radiogroup"', false)
            ->assertDontSee('id="booking-option"', false)
            ->assertViewHas('page', function ($page) use ($standard): bool {
                $option = collect($page->booking['options'])->firstWhere('id', $standard->id);

                return $option['pickup'] === true
                    && $option['cancellationHours'] === 24
                    && $option['adultPrice'] === 100.0
                    && $option['childPrice'] === 60.0
                    && array_column($option['languages'], 'code') === ['en', 'fr']
                    && in_array('options', array_column($page->tabsV1(), 'id'), true);
            });
    }

    public function test_experience_without_options_has_no_options_section(): void
    {
        $experience = $this->createExperience();
        $this->createMedia($experience);

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))
            ->assertOk()
            ->assertDontSee('id="options"', false);
    }

    public function test_draft_experience_show_page_returns_not_found(): void
    {
        $experience = $this->createExperience([
            'status' => 'draft',
        ]);

        $this->get(route('site.experiences.show', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]))->assertNotFound();
    }

    public function test_show_page_includes_related_recommendations(): void
    {
        $experience = $this->createExperience([
            'title' => ['en' => 'Main Desert Tour'],
            'category' => 'desert',
            'location_city' => 'Marrakesh',
        ]);
        $recommended = $this->createExperience([
            'title' => ['en' => 'Related Desert Dinner'],
            'category' => 'desert',
            'location_city' => 'Marrakesh',
            'rating_avg' => '4.90',
        ]);
        $unrelated = $this->createExperience([
            'title' => ['en' => 'Unrelated Fes Workshop'],
            'category' => 'workshop',
            'location_city' => 'Fes',
        ]);

        $this->createMedia($experience);
        $this->createMedia($recommended);
        $this->createMedia($unrelated);

        $this->get(route('site.experiences.show', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]))
            ->assertOk()
            ->assertSee('Related Desert Dinner')
            ->assertDontSee('Unrelated Fes Workshop');
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Show Agency '.uniqid(),
            'slug' => 'show-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create(array_merge([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Default Experience'],
            'description' => ['en' => 'Default description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'private_price' => null,
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ], $overrides));
    }

    /** @param array<string, string> $prices participant type => price */
    private function createOption(Experience $experience, string $title, array $prices, array $attributes = [], array $languages = ['en']): ExperienceOption
    {
        $option = ExperienceOption::query()->create(array_merge([
            'experience_id' => $experience->id,
            'title' => ['en' => $title],
            'price_type' => 'per_person',
            'status' => 'active',
        ], $attributes));

        foreach ($prices as $type => $price) {
            $option->prices()->create(['participant_type' => $type, 'price' => $price, 'currency' => 'MAD']);
        }

        foreach ($languages as $code) {
            $option->languages()->create(['language_code' => $code, 'type' => 'live_guide']);
        }

        return $option;
    }

    private function createMedia(Experience $experience): void
    {
        ExperienceMedia::query()->create([
            'experience_id' => $experience->id,
            'type' => 'image',
            'source_type' => 'url',
            'path' => 'https://example.test/image.jpg',
            'sort_order' => 0,
        ]);
    }

    private function createAvailability(Experience $experience): Availability
    {
        return Availability::query()->create([
            'experience_id' => $experience->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 6,
            'booked_seats' => 0,
            'is_active' => true,
        ]);
    }
}
