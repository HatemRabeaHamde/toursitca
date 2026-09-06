<?php

namespace Tests\Feature\Seeders;

use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Services\ExperienceLandingPageService;
use Database\Seeders\LandingShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingShowcaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_landing_showcase_cards_for_local_development(): void
    {
        app()->setLocale('en');

        $this->seed(LandingShowcaseSeeder::class);

        $landing = app(ExperienceLandingPageService::class)->get();

        $this->assertNotEmpty($landing->trendingCards);
        $this->assertNotEmpty($landing->newestCards);
        $this->assertNotEmpty($landing->dealCards);
        $this->assertNotEmpty($landing->testimonials);
        $this->assertContains('Showcase Marrakech balloon ride', $landing->trendingCards->pluck('title')->all());
        $this->assertContains('Marrakesh food walk', $landing->dealCards->pluck('title')->all());
    }

    public function test_it_seeds_a_rich_detail_page_showcase_experience(): void
    {
        app()->setLocale('en');

        $this->seed(LandingShowcaseSeeder::class);

        $experience = Experience::query()
            ->where('slug', 'showcase-marrakech-balloon-ride')
            ->with('images')
            ->firstOrFail();

        $this->assertCount(5, $experience->images);
        $this->assertSame('620.00', $experience->private_price);

        $this->get(route('site.experiences.show', [
            'locale' => 'en',
            'experience' => $experience->slug,
        ]))
            ->assertOk()
            ->assertSee('Showcase Marrakech balloon ride')
            ->assertSee('Sample detail-page listing')
            ->assertSee('Demo hotel pickup coordination')
            ->assertSee('Private option available')
            ->assertSee('images.unsplash.com', false);
    }
}
