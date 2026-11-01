<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HostProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_tells_the_host_story(): void
    {
        $host = $this->createHost([
            'slug' => 'hassan',
            'name' => 'Hassan',
            'quote' => ['en' => 'The desert taught me patience.'],
            'story' => ['en' => ['Grew up in the dunes.', 'Guides small groups.']],
            'skills' => ['en' => ['Stargazing']],
            'languages' => ['fr', 'en'],
            'partner_since' => 2019,
            'licence_number' => 'MA-3847',
        ]);
        $this->createExperience($host, 'Desert Nights');
        $this->createHost(['slug' => 'fatima', 'name' => 'Fatima']);

        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => 'hassan']))
            ->assertOk()
            ->assertViewIs('site.hosts.show')
            ->assertSee('<title>Hassan, local host in Morocco</title>', false)
            ->assertSee('The desert taught me patience.')
            ->assertSeeInOrder(['About Hassan', 'Grew up in the dunes.', 'Guides small groups.'])
            ->assertSee('Stargazing')
            ->assertSee('French, English')
            ->assertSee('Desert Nights')
            ->assertSee('More with Hassan soon')
            ->assertSee('Licence #MA-3847')
            ->assertSee('Local partner since 2019')
            ->assertSee('Fatima')
            ->assertSee(route('site.hosts.show', ['locale' => 'en', 'host' => 'fatima']), false);
    }

    public function test_sections_without_data_stay_hidden(): void
    {
        $this->createHost(['slug' => 'omar', 'name' => 'Omar', 'is_verified' => false]);

        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => 'omar']))
            ->assertOk()
            ->assertDontSee('id="experiences"', false)
            ->assertDontSee('id="reviews"', false)
            ->assertDontSee('Why you can trust Omar')
            ->assertDontSee('About Omar');
    }

    public function test_inactive_host_returns_not_found(): void
    {
        $this->createHost(['slug' => 'gone', 'is_active' => false]);

        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => 'gone']))->assertNotFound();
    }

    public function test_unpublished_experiences_are_not_listed(): void
    {
        $host = $this->createHost(['slug' => 'leila', 'name' => 'Leila']);
        $this->createExperience($host, 'Hidden Draft', 'draft');

        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => 'leila']))
            ->assertOk()
            ->assertDontSee('Hidden Draft');
    }

    private function createHost(array $overrides = []): Host
    {
        return Host::query()->create(array_merge([
            'slug' => 'host-'.uniqid(),
            'name' => 'Host',
            'role' => ['en' => 'Desert guide'],
            'city' => 'M’Hamid',
            'photo_url' => '/images/discover/hosts/hassan.jpg',
            'is_verified' => true,
            'is_active' => true,
        ], $overrides));
    }

    private function createExperience(Host $host, string $title, string $status = 'published'): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Agency '.uniqid(),
            'slug' => 'agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create([
            'agency_id' => $agency->id,
            'host_id' => $host->id,
            'created_by' => $owner->id,
            'title' => ['en' => $title],
            'description' => ['en' => 'Description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'location_city' => 'Erg Chigaga',
            'status' => $status,
        ]);
    }
}
