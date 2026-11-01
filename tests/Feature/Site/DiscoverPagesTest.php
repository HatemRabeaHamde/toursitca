<?php

namespace Tests\Feature\Site;

use App\Domain\Discover\Models\Interest;
use App\Domain\Landing\Models\Destination;
use Database\Seeders\DiscoverContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DiscoverPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DiscoverContentSeeder::class);
    }

    #[DataProvider('locales')]
    public function test_discover_hub_renders_in_every_locale(string $locale, string $title): void
    {
        $this->get(route('site.discover.index', ['locale' => $locale]))
            ->assertOk()
            ->assertSee($title)
            ->assertSee('Marrakech')
            ->assertSee(route('site.discover.destinations.show', ['locale' => $locale, 'destination' => 'marrakesh']), false)
            ->assertSee(route('site.discover.interests.show', ['locale' => $locale, 'interest' => 'cuisine']), false);
    }

    public static function locales(): array
    {
        return [
            'en' => ['en', 'Explore by'],
            'fr' => ['fr', 'Explorer par'],
            'nl' => ['nl', 'Ontdek per'],
        ];
    }

    public function test_destination_guide_renders_its_chapters(): void
    {
        $this->get(route('site.discover.destinations.show', ['locale' => 'en', 'destination' => 'marrakesh']))
            ->assertOk()
            ->assertSee('In this guide')
            ->assertSee('Jemaa el-Fnaa')
            ->assertSee('Tanjia')
            ->assertSee('Getting around')
            ->assertSee('Marrakech-Safi');
    }

    public function test_interest_guide_renders_its_sections(): void
    {
        $this->get(route('site.discover.interests.show', ['locale' => 'fr', 'interest' => 'cuisine']))
            ->assertOk()
            ->assertSee('Tagine')
            ->assertSee('Tanjia and evening food stalls.')
            ->assertSee('Ce que cela signifie');
    }

    public function test_pages_without_a_guide_are_not_found(): void
    {
        $destination = Destination::query()->where('slug', 'fes')->firstOrFail();
        $interest = Interest::query()->where('slug', 'nature')->firstOrFail();

        $this->assertNull($destination->guide);
        $this->get(route('site.discover.destinations.show', ['locale' => 'en', 'destination' => 'fes']))->assertNotFound();
        $this->get(route('site.discover.interests.show', ['locale' => 'en', 'interest' => $interest->slug]))->assertNotFound();
    }

    public function test_inactive_destination_is_not_found(): void
    {
        Destination::query()->where('slug', 'marrakesh')->update(['is_active' => false]);

        $this->get(route('site.discover.destinations.show', ['locale' => 'en', 'destination' => 'marrakesh']))->assertNotFound();
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(DiscoverContentSeeder::class);

        $this->assertSame(1, Destination::query()->where('slug', 'marrakesh')->count());
        $this->assertSame(6, Interest::query()->count());
    }

    public function test_landing_links_into_discover(): void
    {
        $this->get(route('site.home', ['locale' => 'en']))
            ->assertOk()
            ->assertSee(route('site.discover.index', ['locale' => 'en']), false)
            ->assertSee(route('site.hosts.index', ['locale' => 'en']), false);
    }
}
