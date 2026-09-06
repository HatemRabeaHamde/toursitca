<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_can_search_and_filter_published_experiences(): void
    {
        $balloon = $this->createExperience([
            'title' => ['en' => 'Marrakech Balloon Ride'],
            'category' => 'balloon',
            'location_city' => 'Marrakesh',
            'price_per_person' => '120.00',
            'private_price' => '600.00',
            'pickup_enabled' => true,
            'rating_avg' => '4.80',
            'reviews_count' => 100,
            'is_top_rated' => true,
        ]);
        $this->createAvailability($balloon, now()->addDays(10)->toDateString());

        $desert = $this->createExperience([
            'title' => ['en' => 'Fes Desert Dinner'],
            'category' => 'desert',
            'location_city' => 'Fes',
            'price_per_person' => '80.00',
            'private_price' => null,
            'pickup_enabled' => false,
        ]);
        $this->createAvailability($desert, now()->addDays(10)->toDateString());

        $this->get(route('site.experiences.index', [
            'locale' => 'en',
            'search' => 'Marrakech',
            'date' => now()->addDays(10)->toDateString(),
            'private_available' => 1,
            'pickup_available' => 1,
        ]))
            ->assertOk()
            ->assertSee('Marrakech Balloon Ride')
            ->assertSee('Top rated')
            ->assertSee('Private option available')
            ->assertSee('Pickup available')
            ->assertSeeHtml('<strong>1</strong> experience found');
    }

    public function test_listing_filters_by_multiple_categories(): void
    {
        $balloon = $this->createExperience([
            'title' => ['en' => 'Marrakech Balloon Ride'],
            'category' => 'balloon',
        ]);
        $desert = $this->createExperience([
            'title' => ['en' => 'Fes Desert Dinner'],
            'category' => 'desert',
        ]);
        $cooking = $this->createExperience([
            'title' => ['en' => 'Medina Cooking Class'],
            'category' => 'food',
        ]);

        foreach ([$balloon, $desert, $cooking] as $experience) {
            $this->createAvailability($experience, now()->addDays(5)->toDateString());
        }

        $this->get(route('site.experiences.index', [
            'locale' => 'en',
            'category' => ['balloon', 'desert'],
        ]))
            ->assertOk()
            ->assertSee('Marrakech Balloon Ride')
            ->assertSee('Fes Desert Dinner')
            ->assertDontSee('Medina Cooking Class');
    }

    public function test_listing_sorts_by_price_low_to_high(): void
    {
        $expensive = $this->createExperience([
            'title' => ['en' => 'Premium Balloon Ride'],
            'price_per_person' => '300.00',
            'status' => 'published',
        ]);
        $cheap = $this->createExperience([
            'title' => ['en' => 'Budget Walking Tour'],
            'price_per_person' => '40.00',
            'status' => 'published',
        ]);

        $this->createAvailability($expensive, now()->addDays(5)->toDateString());
        $this->createAvailability($cheap, now()->addDays(5)->toDateString());

        $this->get(route('site.experiences.index', [
            'locale' => 'en',
            'sort' => 'price_low',
        ]))
            ->assertOk()
            ->assertSeeInOrder(['Budget Walking Tour', 'Premium Balloon Ride']);
    }

    public function test_listing_filters_by_participants_capacity_and_available_seats(): void
    {
        $available = $this->createExperience([
            'title' => ['en' => 'Family Desert Camp'],
            'max_group_size' => 6,
        ]);
        $this->createAvailability($available, now()->addDays(7)->toDateString(), [
            'max_seats' => 6,
            'booked_seats' => 1,
            'held_seats' => 1,
        ]);

        $tooSmall = $this->createExperience([
            'title' => ['en' => 'Tiny Medina Walk'],
            'max_group_size' => 3,
        ]);
        $this->createAvailability($tooSmall, now()->addDays(7)->toDateString(), [
            'max_seats' => 6,
        ]);

        $notEnoughSeats = $this->createExperience([
            'title' => ['en' => 'Nearly Full Food Tour'],
            'max_group_size' => 8,
        ]);
        $this->createAvailability($notEnoughSeats, now()->addDays(7)->toDateString(), [
            'max_seats' => 6,
            'booked_seats' => 3,
        ]);

        $this->get(route('site.experiences.index', [
            'locale' => 'en',
            'date' => now()->addDays(7)->toDateString(),
            'participants' => 4,
        ]))
            ->assertOk()
            ->assertSee('Family Desert Camp')
            ->assertDontSee('Tiny Medina Walk')
            ->assertDontSee('Nearly Full Food Tour');
    }

    public function test_listing_filters_by_date_range(): void
    {
        $insideRange = $this->createExperience([
            'title' => ['en' => 'Range Match Desert Walk'],
        ]);
        $this->createAvailability($insideRange, now()->addDays(8)->toDateString());

        $outsideRange = $this->createExperience([
            'title' => ['en' => 'Outside Range Food Tour'],
        ]);
        $this->createAvailability($outsideRange, now()->addDays(20)->toDateString());

        $this->get(route('site.experiences.index', [
            'locale' => 'en',
            'date_from' => now()->addDays(7)->toDateString(),
            'date_to' => now()->addDays(10)->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('Range Match Desert Walk')
            ->assertDontSee('Outside Range Food Tour');
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Listing Agency '.uniqid(),
            'slug' => 'listing-agency-'.uniqid(),
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
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ], $overrides));
    }

    private function createAvailability(Experience $experience, string $date, array $overrides = []): Availability
    {
        return Availability::query()->create(array_merge([
            'experience_id' => $experience->id,
            'date' => $date,
            'time_slot' => '09:00:00',
            'max_seats' => 6,
            'booked_seats' => 0,
            'held_seats' => 0,
            'is_active' => true,
        ], $overrides));
    }
}
