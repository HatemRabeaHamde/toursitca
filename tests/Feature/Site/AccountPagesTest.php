<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_my_bookings_groups_trips_and_counts_the_tabs(): void
    {
        $user = User::factory()->create();
        $experience = $this->createExperience('Desert Nights');
        $this->createBooking($user, $experience, 'pending');
        $this->createBooking($user, $experience, 'completed');
        $this->createBooking(User::factory()->create(), $experience, 'pending');

        $this->actingAs($user)->get(route('site.dashboard.bookings', ['locale' => 'en']))
            ->assertOk()
            ->assertSee('All (2)')
            ->assertSee('Requests (1)')
            ->assertSee('Past (1)')
            ->assertSee('Upcoming (0)')
            ->assertSeeInOrder(['Waiting for confirmation', 'Awaiting platform review', 'Past trips', 'Completed'])
            ->assertSee('Leave a review');

        $this->actingAs($user)->get(route('site.dashboard.bookings', ['locale' => 'en', 'tab' => 'past']))
            ->assertOk()
            ->assertDontSee('Waiting for confirmation');
    }

    public function test_traveller_can_cancel_a_pending_request_but_not_someone_elses(): void
    {
        $user = User::factory()->create();
        $experience = $this->createExperience('Desert Nights');
        $own = $this->createBooking($user, $experience, 'pending');
        $other = $this->createBooking(User::factory()->create(), $experience, 'pending');

        $this->actingAs($user)->post(route('site.dashboard.bookings.cancel', ['locale' => 'en', 'booking' => $other->id]))->assertForbidden();
        $this->actingAs($user)->post(route('site.dashboard.bookings.cancel', ['locale' => 'en', 'booking' => $own->id]))
            ->assertRedirect(route('site.dashboard.bookings', ['locale' => 'en']));

        $this->assertSame('cancelled', $own->fresh()->status);
        $this->assertSame('pending', $other->fresh()->status);
    }

    public function test_request_sent_page_belongs_to_the_traveller(): void
    {
        $user = User::factory()->create();
        $booking = $this->createBooking($user, $this->createExperience('Desert Nights'), 'pending');

        $this->actingAs($user)->get(route('site.bookings.confirmation', ['locale' => 'en', 'booking' => $booking->id]))
            ->assertOk()
            ->assertSee('Booking status')
            ->assertDontSee('Your booking is confirmed.')
            ->assertSee('Desert Nights')
            ->assertSee($booking->guest_email);

        $this->actingAs(User::factory()->create())->get(route('site.bookings.confirmation', ['locale' => 'en', 'booking' => $booking->id]))
            ->assertForbidden();
    }

    public function test_profile_page_splits_the_name_and_saves_it_back(): void
    {
        $user = User::factory()->create(['name' => 'Sarah Mitchell', 'email' => 'sarah@example.com', 'preferred_lang' => 'en']);

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Welcome back,')
            ->assertSee('value="Sarah"', false)
            ->assertSee('value="Mitchell"', false);

        $this->actingAs($user)->patch(route('profile.update'), [
            'first_name' => 'Sara',
            'last_name' => 'Mitchell-Jones',
            'email' => 'sarah@example.com',
            'phone' => '+14165550182',
            'country' => 'Canada',
            'preferred_lang' => 'fr',
        ])->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('Sara Mitchell-Jones', $user->name);
        $this->assertSame('Canada', $user->country);
        $this->assertSame('fr', $user->preferred_lang);
    }

    public function test_profile_page_offers_a_review_for_a_completed_trip(): void
    {
        $user = User::factory()->create();
        $this->createBooking($user, $this->createExperience('Food Journey'), 'completed');

        $this->actingAs($user)->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('How was Food Journey?');
    }

    public function test_review_with_detail_ratings_and_photos_leads_to_the_thank_you_page(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Sarah Mitchell']);
        $booking = $this->createBooking($user, $this->createExperience('Food Journey'), 'completed');

        $this->actingAs($user)->get(route('site.reviews.create', ['locale' => 'en', 'booking' => $booking->id]))
            ->assertOk()
            ->assertSee('Rate the details')
            ->assertSee('Food Journey');

        $this->actingAs($user)->post(route('site.reviews.store', ['locale' => 'en', 'booking' => $booking->id]), [
            'rating' => 5,
            'rating_host' => 5,
            'rating_value' => 4,
            'body' => 'We folded msemen in her courtyard and ate with her family.',
            'photos' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
        ])->assertRedirect(route('site.reviews.thanks', ['locale' => 'en', 'booking' => $booking->id]));

        $review = $booking->fresh()->review;
        $this->assertSame(5, $review->rating_host);
        $this->assertSame(4, $review->rating_value);
        $this->assertNull($review->rating_pickup);
        $this->assertCount(2, $review->photos);

        $this->actingAs($user)->get(route('site.reviews.thanks', ['locale' => 'en', 'booking' => $booking->id]))
            ->assertOk()
            ->assertSee('Thank you,')
            ->assertSee('Sarah.')
            ->assertSee('We folded msemen');
    }

    public function test_review_needs_a_rating_and_a_real_message(): void
    {
        $user = User::factory()->create();
        $booking = $this->createBooking($user, $this->createExperience('Food Journey'), 'completed');

        $this->actingAs($user)->post(route('site.reviews.store', ['locale' => 'en', 'booking' => $booking->id]), ['body' => 'short'])
            ->assertSessionHasErrors(['rating', 'body']);
        $this->assertNull($booking->fresh()->review);
    }

    private function createExperience(string $title): Experience
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
            'created_by' => $owner->id,
            'title' => ['en' => $title],
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

    private function createBooking(User $user, Experience $experience, string $status): Booking
    {
        static $day = 1;
        $availability = Availability::query()->create([
            'experience_id' => $experience->id,
            'date' => now()->addDays($day++)->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 6,
            'booked_seats' => 1,
            'is_active' => true,
        ]);

        return Booking::query()->create([
            'user_id' => $user->id,
            'experience_id' => $experience->id,
            'availability_id' => $availability->id,
            'booking_type' => 'group',
            'participants_count' => 1,
            'participants' => ['adult' => 1],
            'unit_price' => '100.00',
            'total_price' => '100.00',
            'commission_rate' => '15.00',
            'commission_amount' => '15.00',
            'agency_amount' => '85.00',
            'status' => $status,
            'guest_name' => $user->name,
            'guest_email' => 'guest-'.uniqid().'@example.test',
        ]);
    }
}
