<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceLandingPageService;
use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_uses_managed_content_when_available(): void
    {
        $this->createExperience([
            'title' => ['en' => 'Managed Desert Tour'],
            'category' => 'desert',
            'location_city' => 'Marrakesh',
        ]);

        ExperienceCategory::query()->create([
            'slug' => 'desert',
            'name' => ['en' => 'Managed Desert'],
            'image_url' => 'https://example.com/desert.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Destination::query()->create([
            'slug' => 'marrakesh',
            'name' => ['en' => 'Managed Marrakesh'],
            'city' => 'Marrakesh',
            'image_url' => 'https://example.com/marrakesh.jpg',
            'is_featured' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LandingFaq::query()->create([
            'question' => ['en' => 'Managed question?'],
            'answer' => ['en' => 'Managed answer.'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('site.home', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Managed Desert Tour')
            ->assertSee('Managed Desert')
            ->assertSee('Managed Marrakesh')
            ->assertSee('Managed question?')
            ->assertSee('Managed answer.');
    }

    public function test_morocco_compass_landing_page_renders(): void
    {
        $this->createExperience([
            'title' => ['en' => 'Compass Desert Route'],
            'category' => 'desert',
            'location_city' => 'Marrakesh',
        ]);

        $this->get(route('site.home.compass', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Morocco Compass')
            ->assertSee('Compass Desert Route');
    }

    public function test_newsletter_subscription_is_stored_once_per_email(): void
    {
        $payload = ['email' => 'Traveler@Example.com'];

        $this->post(route('site.newsletter.store', ['locale' => 'en']), $payload)
            ->assertRedirect();

        $this->post(route('site.newsletter.store', ['locale' => 'en']), $payload)
            ->assertRedirect();

        $this->assertDatabaseCount('newsletter_subscribers', 1);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'traveler@example.com',
            'locale' => 'en',
            'source' => 'landing',
        ]);
    }

    public function test_flash_offers_only_include_active_time_limited_deals(): void
    {
        app()->setLocale('en');

        $this->createExperience([
            'title' => ['en' => 'Active Deal'],
            'price_per_person' => '80.00',
            'original_price' => '120.00',
            'deal_starts_at' => now()->subDay(),
            'deal_ends_at' => now()->addDay(),
        ]);

        $this->createExperience([
            'title' => ['en' => 'Expired Deal'],
            'price_per_person' => '80.00',
            'original_price' => '120.00',
            'deal_starts_at' => now()->subDays(3),
            'deal_ends_at' => now()->subDay(),
        ]);

        $this->createExperience([
            'title' => ['en' => 'Discount Without End Date'],
            'price_per_person' => '80.00',
            'original_price' => '120.00',
            'deal_starts_at' => now()->subDay(),
            'deal_ends_at' => null,
        ]);

        $dealTitles = app(ExperienceLandingPageService::class)
            ->get()
            ->dealCards
            ->pluck('title')
            ->all();

        $this->assertContains('Active Deal', $dealTitles);
        $this->assertNotContains('Expired Deal', $dealTitles);
        $this->assertNotContains('Discount Without End Date', $dealTitles);
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Landing Agency '.uniqid(),
            'slug' => 'landing-agency-'.uniqid(),
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
}
