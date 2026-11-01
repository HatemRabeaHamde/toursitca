<?php

namespace Tests\Feature\Site;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravellerReviewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_reviews_page_lists_booking_linked_reviews_with_summary(): void
    {
        $this->get(route('site.reviews.index', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('Leila showed us where locals actually shop')
            ->assertSee('images/reviews/emily-souk.jpg', false)
            ->assertSee('Thanks James — we now mention the boots in the packing list.');
    }

    public function test_reviews_can_be_filtered_by_experience_and_photos(): void
    {
        $this->get(route('site.reviews.index', ['locale' => 'en', 'experience' => 'two-nights-erg-chigaga-dunes']))
            ->assertOk()
            ->assertSee('Hassan is an amazing guide')
            ->assertDontSee('Leila showed us where locals actually shop');

        $this->get(route('site.reviews.index', ['locale' => 'en', 'photos' => 1]))
            ->assertOk()
            ->assertSee('images/reviews/james-high-atlas.jpg', false)
            ->assertDontSee('Sara showed us her Essaouira');
    }

    public function test_live_filters_receive_json_fragments(): void
    {
        $this->getJson(route('site.reviews.index', ['locale' => 'en', 'rating' => 4]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonStructure(['chips', 'count', 'results']);
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->get(route('site.reviews.index', ['locale' => 'en', 'sort' => 'random']))->assertRedirect();
    }
}
