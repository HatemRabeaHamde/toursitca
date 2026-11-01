<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Domain\Landing\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_managed_content(): void
    {
        $this->createExperience(['title' => ['en' => 'Two nights in the dunes']]);

        ExperienceCategory::query()->create([
            'slug' => 'desert',
            'name' => ['en' => 'Managed Desert'],
            'image_url' => 'https://example.com/desert.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LandingFaq::query()->create([
            'question' => ['en' => 'When is the best time to go?'],
            'answer' => ['en' => 'Managed answer.'],
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Testimonial::query()->create([
            'author_name' => ['en' => 'Anouk'],
            'author_country' => 'Netherlands',
            'body' => ['en' => 'We still talk about that night.'],
            'experience_title' => 'Two nights in the dunes',
            'rating' => 5,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Host::query()->create(['slug' => 'fatima', 'name' => 'Fatima', 'role' => ['en' => 'Home cook'], 'photo_url' => '/images/discover/hosts/fatima.jpg', 'is_active' => true, 'is_verified' => true, 'reviewed_at' => now()]);

        $this->get(route('site.home', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Experience it.')
            ->assertSee('Two nights in the dunes')
            ->assertSee('Managed Desert')
            ->assertSee('When is the best time to go?')
            ->assertSee('Anouk')
            ->assertSee('We still talk about that night.')
            ->assertSee('Watch the film')
            ->assertSee('Your route')
            ->assertSee('Fatima');
    }

    public function test_the_root_and_the_locale_home_serve_the_new_landing(): void
    {
        $this->get('/en')->assertOk()->assertSee('Experience it.');
        $this->get('/')->assertOk()->assertSee('Experience it.');
    }

    public function test_the_old_home_v2_address_redirects_permanently(): void
    {
        $this->get('/en/home-v2')->assertStatus(301)->assertRedirect(route('site.home', ['locale' => 'en']));
    }

    #[DataProvider('locales')]
    public function test_home_renders_in_every_locale(string $locale, string $expected): void
    {
        $this->get(route('site.home', ['locale' => $locale]))
            ->assertOk()
            ->assertSee($expected);
    }

    public static function locales(): array
    {
        return [
            'en' => ['en', 'Experience it.'],
            'fr' => ['fr', 'Vivez-le.'],
            'nl' => ['nl', 'Beleef het.'],
        ];
    }

    public function test_home_newsletter_accepts_json(): void
    {
        $this->postJson(route('site.newsletter.store', ['locale' => 'en']), ['email' => 'traveler@example.com', 'source' => 'landing'])
            ->assertOk()
            ->assertJsonStructure(['message']);
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Atlas Hosts',
            'slug' => 'atlas-hosts-'.uniqid(),
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
