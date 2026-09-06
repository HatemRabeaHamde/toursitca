<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_and_remove_an_experience_from_wishlist(): void
    {
        $user = User::factory()->create();
        $experience = $this->createExperience();

        $this->actingAs($user)
            ->postJson(route('site.wishlist.toggle', ['locale' => 'en', 'experience' => $experience->id]))
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'experience_id' => $experience->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('site.wishlist.toggle', ['locale' => 'en', 'experience' => $experience->id]))
            ->assertOk()
            ->assertJson(['saved' => false]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'experience_id' => $experience->id,
        ]);
    }

    public function test_guest_cannot_toggle_wishlist(): void
    {
        $experience = $this->createExperience();

        $this->postJson(route('site.wishlist.toggle', ['locale' => 'en', 'experience' => $experience->id]))
            ->assertStatus(401);

        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_wishlisted_experience_is_marked_as_saved_on_the_listing_page(): void
    {
        $user = User::factory()->create();
        $experience = $this->createExperience();
        $user->wishlists()->create(['experience_id' => $experience->id]);

        $this->actingAs($user)
            ->get(route('site.experiences.index', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('isSaved: true', false);
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Wishlist Agency '.uniqid(),
            'slug' => 'wishlist-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create(array_merge([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Wishlist Test Experience'],
            'description' => ['en' => 'Default description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ], $overrides));
    }
}
