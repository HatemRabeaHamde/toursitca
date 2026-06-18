<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceMedia;
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
            ->assertSee('Marrakech Balloon Ride')
            ->assertSee('Fly over Marrakech at sunrise.')
            ->assertSee('Top rated')
            ->assertSee('Activity provider')
            ->assertSee('Manual confirmation')
            ->assertSee('Choose your date')
            ->assertSee('Private option available')
            ->assertSee('Pickup available')
            ->assertSee('Breakfast')
            ->assertSee('Next available dates');
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
