<?php

namespace Tests\Feature\Site;

use App\Domain\Discover\Models\Host;
use Database\Seeders\DiscoverContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HostsDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DiscoverContentSeeder::class);
    }

    #[DataProvider('locales')]
    public function test_directory_renders_in_every_locale(string $locale, string $heading): void
    {
        $this->get(route('site.hosts.index', ['locale' => $locale]))
            ->assertOk()
            ->assertSee($heading)
            ->assertSee('Hassan')
            ->assertSee('Rachid')
            ->assertSee('MA-3847');
    }

    public static function locales(): array
    {
        return [
            'en' => ['en', '9 local hosts'],
            'fr' => ['fr', '9 hôtes locaux'],
            'nl' => ['nl', '9 lokale gastheren'],
        ];
    }

    public function test_region_filter_keeps_only_that_region(): void
    {
        $this->get(route('site.hosts.index', ['locale' => 'en', 'region' => 'atlantic-coast']))
            ->assertOk()
            ->assertSee('2 local hosts')
            ->assertSee('Karim')
            ->assertSee('Sara')
            ->assertDontSee('Hassan');
    }

    public function test_specialty_and_search_filters(): void
    {
        $this->get(route('site.hosts.index', ['locale' => 'en', 'specialty' => 'cuisine']))
            ->assertSee('Fatima')
            ->assertDontSee('Omar');

        $this->get(route('site.hosts.index', ['locale' => 'en', 'q' => 'surf']))
            ->assertSee('1 local host')
            ->assertSee('Karim');
    }

    public function test_name_sort_orders_alphabetically(): void
    {
        $this->get(route('site.hosts.index', ['locale' => 'en', 'sort' => 'name']))
            ->assertOk()
            ->assertSeeInOrder(['Amina', 'Fatima', 'Hassan', 'Karim']);
    }

    public function test_inactive_hosts_and_hosts_without_photo_are_hidden(): void
    {
        Host::query()->where('slug', 'omar')->update(['is_active' => false]);
        Host::query()->where('slug', 'leila')->update(['photo_url' => null]);

        $this->get(route('site.hosts.index', ['locale' => 'en']))
            ->assertSee('7 local hosts')
            ->assertDontSee('Omar')
            ->assertDontSee('Leila');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->get(route('site.hosts.index', ['locale' => 'en', 'sort' => 'drop table']))
            ->assertRedirect();
    }

    public function test_view_profile_links_to_the_hosts_experiences(): void
    {
        $this->get(route('site.hosts.index', ['locale' => 'en']))
            ->assertSee(route('site.hosts.show', ['locale' => 'en', 'host' => 'fatima']), false);
    }

    public function test_filters_can_be_fetched_as_fragments(): void
    {
        $this->getJson(route('site.hosts.index', ['locale' => 'en', 'region' => 'atlantic-coast']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonStructure(['chips', 'count', 'results'])
            ->assertJsonPath('count', '2 local hosts');
    }
}
