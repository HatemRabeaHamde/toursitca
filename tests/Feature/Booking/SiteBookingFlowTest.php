<?php

namespace Tests\Feature\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tourist_can_start_group_checkout_from_legacy_booking_form(): void
    {
        $availability = $this->createAvailability();

        $response = $this->post(route('site.bookings.store', [
            'locale' => 'en',
            'experience' => $availability->experience,
        ]), [
            'availability_id' => $availability->id,
            'booking_type' => 'group',
            'participants_count' => 2,
            'guest_name' => 'Tourist Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '+212600000000',
            'special_notes' => 'Please call before arrival.',
        ]);

        $response->assertSessionHasNoErrors();

        $checkoutSession = CheckoutSession::query()->firstOrFail();

        $response->assertRedirect(route('site.checkout.activity', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]));

        $this->assertSame('active', $checkoutSession->status);
        $this->assertSame('group', $checkoutSession->booking_type);
        $this->assertSame('200.00', $checkoutSession->price_snapshot['total']);
        $this->assertSame('Tourist', $checkoutSession->contact_first_name);
        $this->assertSame('Guest', $checkoutSession->contact_last_name);
        $this->assertSame(2, $availability->refresh()->held_seats);
        $this->assertSame(0, $availability->booked_seats);
    }

    public function test_private_checkout_holds_the_whole_slot_from_legacy_booking_form(): void
    {
        $availability = $this->createAvailability();

        $this->post(route('site.bookings.store', [
            'locale' => 'en',
            'experience' => $availability->experience,
        ]), [
            'availability_id' => $availability->id,
            'booking_type' => 'private',
            'participants_count' => 3,
            'guest_name' => 'Private Guest',
            'guest_email' => 'private@example.com',
        ])->assertSessionHasNoErrors();

        $checkoutSession = CheckoutSession::query()->firstOrFail();

        $this->assertSame('private', $checkoutSession->booking_type);
        $this->assertSame('450.00', $checkoutSession->price_snapshot['total']);
        $this->assertSame(3, $checkoutSession->participants_count);
        $this->assertSame($availability->max_seats, $checkoutSession->charged_seats);
        $this->assertSame($availability->max_seats, $availability->refresh()->held_seats);
        $this->assertSame(0, $availability->booked_seats);
    }

    public function test_booking_rejects_slot_from_another_experience(): void
    {
        $availability = $this->createAvailability();
        $anotherAvailability = $this->createAvailability('Another Agency', 'another-agency');

        $this->post(route('site.bookings.store', [
            'locale' => 'en',
            'experience' => $availability->experience,
        ]), [
            'availability_id' => $anotherAvailability->id,
            'booking_type' => 'group',
            'participants_count' => 1,
            'guest_name' => 'Tourist Guest',
            'guest_email' => 'guest@example.com',
        ])->assertSessionHasErrors('availability_id');

        $this->assertDatabaseCount('checkout_sessions', 0);
    }

    private function createAvailability(string $agencyName = 'Test Agency', string $agencySlug = 'test-agency'): Availability
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => $agencyName,
            'slug' => $agencySlug,
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Desert Tour'],
            'description' => ['en' => 'Tour description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 6,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);

        return Availability::query()->create([
            'experience_id' => $experience->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 6,
            'booked_seats' => 0,
            'is_active' => true,
        ]);
    }
}
