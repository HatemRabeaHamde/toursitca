<?php

namespace Tests\Feature\Admin;

use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Domain\Landing\Models\Testimonial;
use App\Domain\Landing\Models\TravelReel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_landing_categories(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.landing.categories.store'), [
                'slug' => 'desert',
                'name' => ['en' => 'Desert'],
                'image_url' => 'https://example.com/desert.jpg',
                'sort_order' => 5,
                'is_active' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'desert');

        $category = ExperienceCategory::query()->firstOrFail();

        $this->actingAs($admin)
            ->patchJson(route('admin.landing.categories.update', $category), [
                'slug' => 'desert-tours',
                'name' => ['en' => 'Desert tours'],
                'sort_order' => 1,
                'is_active' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.slug', 'desert-tours');

        $category->refresh();

        $this->assertSame('Desert tours', $category->getTranslation('name', 'en'));
        $this->assertFalse($category->is_active);

        $this->actingAs($admin)
            ->deleteJson(route('admin.landing.categories.destroy', $category))
            ->assertNoContent();

        $this->assertSoftDeleted('experience_categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_each_landing_content_type(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.landing.destinations.store'), [
                'slug' => 'marrakesh',
                'name' => ['en' => 'Marrakesh'],
                'city' => 'Marrakesh',
                'image_url' => 'https://example.com/marrakesh.jpg',
                'is_featured' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.city', 'Marrakesh');

        $this->actingAs($admin)
            ->postJson(route('admin.landing.faqs.store'), [
                'question' => ['en' => 'How do I book?'],
                'answer' => ['en' => 'Use checkout.'],
            ])
            ->assertCreated();

        $this->actingAs($admin)
            ->postJson(route('admin.landing.reels.store'), [
                'title' => ['en' => 'Sunrise balloon'],
                'city' => 'Marrakesh',
                'thumbnail_url' => 'https://example.com/reel.jpg',
                'video_url' => 'https://example.com/reel.mp4',
            ])
            ->assertCreated();

        $this->actingAs($admin)
            ->postJson(route('admin.landing.testimonials.store'), [
                'author_name' => ['en' => 'Sara'],
                'author_country' => 'UK',
                'body' => ['en' => 'A great trip.'],
                'experience_title' => 'Balloon ride',
                'rating' => 4.8,
            ])
            ->assertCreated();

        $this->assertDatabaseCount('destinations', 1);
        $this->assertDatabaseCount('landing_faqs', 1);
        $this->assertDatabaseCount('travel_reels', 1);
        $this->assertDatabaseCount('testimonials', 1);

        $this->assertSame('Marrakesh', Destination::query()->firstOrFail()->getTranslation('name', 'en'));
        $this->assertSame('How do I book?', LandingFaq::query()->firstOrFail()->getTranslation('question', 'en'));
        $this->assertSame('Sunrise balloon', TravelReel::query()->firstOrFail()->getTranslation('title', 'en'));
        $this->assertSame('Sara', Testimonial::query()->firstOrFail()->getTranslation('author_name', 'en'));
    }

    public function test_non_admin_cannot_manage_landing_content(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('admin.landing.categories.store'), [
                'slug' => 'desert',
                'name' => ['en' => 'Desert'],
            ])
            ->assertForbidden();
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        return $admin;
    }
}
