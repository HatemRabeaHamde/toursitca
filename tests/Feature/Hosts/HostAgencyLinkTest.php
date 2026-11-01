<?php

namespace Tests\Feature\Hosts;

use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HostAgencyLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_agency_adds_a_host_that_waits_for_review(): void
    {
        Storage::fake('public');
        [$user, $agency] = $this->createAgencyUser('one');

        $this->actingAs($user)->post(route('agency.hosts.store'), [
            'name' => 'Hassan Ait Brahim',
            'role' => ['en' => 'Desert guide', 'fr' => 'Guide du désert'],
            'city' => 'M’Hamid',
            'story_text' => ['en' => "Grew up in the dunes.\n\nGuides small groups."],
            'skills_text' => ['en' => "Stargazing\nCamel trekking"],
            'languages' => ['ar', 'en'],
            'licence_number' => 'MA-3847',
            'photo' => UploadedFile::fake()->image('hassan.jpg', 800, 900),
        ])->assertRedirect(route('agency.hosts.index'))->assertSessionHasNoErrors();

        $host = Host::query()->firstOrFail();
        $this->assertSame($agency->id, $host->agency_id);
        $this->assertSame('hassan-ait-brahim', $host->slug);
        $this->assertFalse($host->is_active);
        $this->assertFalse($host->is_verified);
        $this->assertNull($host->reviewed_at);
        $this->assertSame(['Grew up in the dunes.', 'Guides small groups.'], $host->getTranslation('story', 'en'));
        $this->assertSame(['Stargazing', 'Camel trekking'], $host->getTranslation('skills', 'en'));
        $this->assertSame('Guide du désert', $host->getTranslation('role', 'fr'));
        $this->assertStringStartsWith('storage/hosts/', $host->photo_url);

        $this->actingAs($user)->get(route('agency.hosts.index'))->assertOk()->assertSee('Hassan Ait Brahim')->assertSee('Waiting for review');
        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => $host->slug]))->assertNotFound();
    }

    public function test_the_host_form_shows_and_keeps_every_field(): void
    {
        [$user, $agency] = $this->createAgencyUser('one');
        $host = $this->createHost($agency, [
            'story' => ['en' => ['First paragraph.', 'Second paragraph.']],
            'skills' => ['en' => ['Stargazing', 'Camel trekking']],
            'quote' => ['en' => 'Patience.'],
        ]);

        $this->actingAs($user)->get(route('agency.hosts.create'))->assertOk()
            ->assertSee('name="story_text[en]"', false)->assertSee('name="skills_text[nl]"', false)->assertDontSee('<x-ui', false);

        $this->actingAs($user)->get(route('agency.hosts.edit', $host))->assertOk()
            ->assertSee("First paragraph.\n\nSecond paragraph.", false)
            ->assertSee("Stargazing\nCamel trekking", false)
            ->assertSee('Patience.');
    }

    public function test_slugs_stay_unique_for_hosts_with_the_same_name(): void
    {
        [$user] = $this->createAgencyUser('one');

        foreach ([1, 2] as $ignored) {
            $this->actingAs($user)->post(route('agency.hosts.store'), ['name' => 'Fatima', 'role' => ['en' => 'Home cook']])->assertSessionHasNoErrors();
        }

        $this->assertSame(['fatima', 'fatima-2'], Host::query()->orderBy('id')->pluck('slug')->all());
    }

    public function test_an_agency_cannot_touch_another_agencys_host(): void
    {
        [$user] = $this->createAgencyUser('one');
        [, $other] = $this->createAgencyUser('two');
        $host = $this->createHost($other);

        $this->actingAs($user)->get(route('agency.hosts.edit', $host))->assertForbidden();
        $this->actingAs($user)->patch(route('agency.hosts.update', $host), ['name' => 'Hacked', 'role' => ['en' => 'x']])->assertForbidden();
        $this->actingAs($user)->delete(route('agency.hosts.destroy', $host))->assertForbidden();
        $this->assertSame('Host', $host->fresh()->name);
    }

    public function test_changing_the_licence_of_a_verified_host_sends_it_back_for_review(): void
    {
        [$user, $agency] = $this->createAgencyUser('one');
        $host = $this->createHost($agency, ['licence_number' => 'MA-1', 'is_verified' => true, 'is_active' => true, 'reviewed_at' => now()]);

        $this->actingAs($user)->patch(route('agency.hosts.update', $host), ['name' => 'Host', 'role' => ['en' => 'Guide'], 'licence_number' => 'MA-1'])->assertSessionHasNoErrors();
        $this->assertTrue($host->fresh()->is_verified);

        $this->actingAs($user)->patch(route('agency.hosts.update', $host), ['name' => 'Host', 'role' => ['en' => 'Guide'], 'licence_number' => 'MA-2'])->assertSessionHasNoErrors();
        $host->refresh();
        $this->assertFalse($host->is_verified);
        $this->assertNull($host->reviewed_at);
        $this->assertTrue($host->is_active);
    }

    public function test_a_host_who_leads_experiences_cannot_be_removed(): void
    {
        [$user, $agency] = $this->createAgencyUser('one');
        $host = $this->createHost($agency);
        $this->createExperience($agency, $host);

        $this->actingAs($user)->delete(route('agency.hosts.destroy', $host))->assertSessionHas('error');
        $this->assertNotNull($host->fresh());

        $empty = $this->createHost($agency, ['slug' => 'empty']);
        $this->actingAs($user)->delete(route('agency.hosts.destroy', $empty))->assertSessionHas('success');
        $this->assertSoftDeleted($empty);
    }

    public function test_an_experience_can_only_be_led_by_a_host_of_the_same_agency(): void
    {
        [$user, $agency] = $this->createAgencyUser('one');
        [, $other] = $this->createAgencyUser('two');
        $own = $this->createHost($agency, ['slug' => 'own']);
        $foreign = $this->createHost($other, ['slug' => 'foreign']);

        $this->actingAs($user)->post(route('agency.experiences.store'), $this->experiencePayload(['host_id' => $foreign->id]))->assertSessionHasErrors('host_id');
        $this->assertSame(0, Experience::query()->count());

        $this->actingAs($user)->post(route('agency.experiences.store'), $this->experiencePayload(['host_id' => $own->id]))->assertSessionHasNoErrors();
        $this->assertSame($own->id, Experience::query()->firstOrFail()->host_id);

        $this->actingAs($user)->get(route('agency.experiences.create'))->assertOk()->assertSee('Host leading this experience');
    }

    public function test_only_admins_reach_the_host_review_screen(): void
    {
        [$user] = $this->createAgencyUser('one');

        $this->actingAs($user)->get(route('admin.hosts.index'))->assertForbidden();
    }

    public function test_admin_approves_a_host_with_a_licence_and_it_goes_live(): void
    {
        [, $agency] = $this->createAgencyUser('one');
        $host = $this->createHost($agency, ['licence_number' => 'MA-3847']);
        $noLicence = $this->createHost($agency, ['slug' => 'no-licence']);

        $this->actingAs($this->createAdmin())->get(route('admin.hosts.index', ['status' => 'review']))->assertOk()->assertSee('Waiting for review');

        $this->actingAs($this->createAdmin())->post(route('admin.hosts.approve', $host))->assertSessionHas('success');
        $host->refresh();
        $this->assertTrue($host->is_active);
        $this->assertTrue($host->is_verified);
        $this->assertNotNull($host->reviewed_at);

        $this->actingAs($this->createAdmin())->post(route('admin.hosts.approve', $noLicence))->assertSessionHas('error');
        $this->assertFalse($noLicence->fresh()->is_verified);
    }

    public function test_admin_edit_sets_ownership_visibility_and_verification(): void
    {
        [, $agency] = $this->createAgencyUser('one');
        [, $other] = $this->createAgencyUser('two');
        $host = $this->createHost($agency);
        $admin = $this->createAdmin();

        $payload = ['name' => 'Host', 'role' => ['en' => 'Guide'], 'agency_id' => $other->id, 'is_active' => 1, 'is_verified' => 1, 'sort_order' => 3];

        $this->actingAs($admin)->patch(route('admin.hosts.update', $host), $payload)->assertSessionHas('error');
        $this->assertFalse($host->fresh()->is_verified);

        $this->actingAs($admin)->patch(route('admin.hosts.update', $host), [...$payload, 'licence_number' => 'MA-9'])->assertSessionHas('success');
        $host->refresh();
        $this->assertSame($other->id, $host->agency_id);
        $this->assertTrue($host->is_active && $host->is_verified);
        $this->assertSame(3, $host->sort_order);
    }

    public function test_travellers_only_see_live_hosts_and_verified_licences(): void
    {
        [, $agency] = $this->createAgencyUser('one');
        $pending = $this->createHost($agency, ['slug' => 'pending', 'name' => 'Pending Pat', 'licence_number' => 'MA-1']);
        $live = $this->createHost($agency, ['slug' => 'live', 'name' => 'Live Lia', 'is_active' => true, 'reviewed_at' => now(), 'licence_number' => 'MA-2']);
        $experience = $this->createExperience($agency, $pending);
        $liveExperience = $this->createExperience($agency, $live);

        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $experience->slug]))->assertOk()->assertDontSee('Pending Pat');
        $this->get(route('site.experiences.show', ['locale' => 'en', 'experience' => $liveExperience->slug]))->assertOk()->assertSee('Live Lia')->assertDontSee('MA-2');

        $live->forceFill(['is_verified' => true])->save();
        $this->get(route('site.hosts.show', ['locale' => 'en', 'host' => 'live']))->assertOk()->assertSee('Licence #MA-2');
    }

    public function test_the_landing_shows_verified_hosts_from_the_database(): void
    {
        [, $agency] = $this->createAgencyUser('one');
        $this->createHost($agency, ['slug' => 'verified', 'name' => 'Verified Vera', 'is_active' => true, 'is_verified' => true, 'reviewed_at' => now(), 'quote' => ['en' => 'The desert taught me patience.']]);
        $this->createHost($agency, ['slug' => 'live-only', 'name' => 'Unverified Uma', 'is_active' => true, 'reviewed_at' => now()]);

        $this->get(route('site.home', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Verified Vera')
            ->assertSee('The desert taught me patience.')
            ->assertDontSee('Unverified Uma')
            ->assertSee(route('site.hosts.show', ['locale' => 'en', 'host' => 'verified']), false);
    }

    /** @return array{0: User, 1: Agency} */
    private function createAgencyUser(string $key): array
    {
        $user = User::factory()->create(['email' => "agency-$key@example.com"]);
        $user->assignRole(Role::findOrCreate('travel_agency', 'web'));
        $agency = Agency::query()->create([
            'user_id' => $user->id,
            'name' => "Agency $key",
            'slug' => "agency-$key",
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return [$user->refresh(), $agency];
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        return $admin;
    }

    private function createHost(Agency $agency, array $overrides = []): Host
    {
        return Host::query()->create(array_merge([
            'agency_id' => $agency->id,
            'slug' => 'host-'.uniqid(),
            'name' => 'Host',
            'role' => ['en' => 'Guide'],
            'city' => 'Marrakesh',
            'photo_url' => '/images/discover/hosts/hassan.jpg',
            'is_active' => false,
            'is_verified' => false,
        ], $overrides));
    }

    private function createExperience(Agency $agency, Host $host): Experience
    {
        return Experience::query()->create([
            'agency_id' => $agency->id,
            'host_id' => $host->id,
            'created_by' => $agency->user_id,
            'title' => ['en' => 'Trip '.uniqid()],
            'description' => ['en' => 'Description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);
    }

    private function experiencePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => ['en' => 'Hosted trip'],
            'description' => ['en' => 'A trip with a host.'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4',
            'max_group_size' => 6,
            'price_per_person' => '100',
            'location_city' => 'Marrakesh',
        ], $overrides);
    }
}
