<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_contact_details_for_owned_checkout_session(): void
    {
        $user = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->actingAs($user)->post(route('site.checkout.contact.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'contact_first_name' => 'Hatem',
            'contact_last_name' => 'Ali',
            'contact_email' => 'hatem@example.com',
            'contact_phone' => '+201000000000',
            'contact_country' => 'Egypt',
            'special_requests' => 'WhatsApp preferred.',
            'terms' => '1',
        ])
            ->assertRedirect(route('site.bookings.confirmation', [
                'locale' => 'en',
                'booking' => Booking::query()->firstOrFail(),
            ]));

        $checkoutSession->refresh();
        $this->assertSame('completed', $checkoutSession->status);

        $this->assertSame('Hatem', $checkoutSession->contact_first_name);
        $this->assertSame('Ali', $checkoutSession->contact_last_name);
        $this->assertSame('hatem@example.com', $checkoutSession->contact_email);
        $this->assertSame('+201000000000', $checkoutSession->contact_phone);
        $this->assertSame('Egypt', $checkoutSession->contact_country);
        $this->assertSame('WhatsApp preferred.', $checkoutSession->special_requests);

        $booking = Booking::query()->firstOrFail();
        $this->assertSame($user->id, $booking->user_id);
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('Hatem Ali', $booking->guest_name);
        $this->assertSame('WhatsApp preferred.', $booking->special_notes);
    }

    public function test_request_cannot_be_sent_without_accepting_the_terms(): void
    {
        $user = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession(['user_id' => $user->id, 'session_id' => null]);

        $this->actingAs($user)->post(route('site.checkout.contact.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'contact_first_name' => 'Hatem',
            'contact_last_name' => 'Ali',
            'contact_email' => 'hatem@example.com',
        ])->assertSessionHasErrors('terms');

        $this->assertSame(0, Booking::query()->count());
    }

    public function test_request_page_shows_the_trip_and_the_contact_form(): void
    {
        $user = User::factory()->create(['name' => 'Sarah Mitchell', 'country' => 'Canada']);
        $checkoutSession = $this->createCheckoutSession(['user_id' => $user->id, 'session_id' => null]);

        $response = $this->actingAs($user)->get(route('site.checkout.contact', ['locale' => 'en', 'checkoutSession' => $checkoutSession]));

        $this->assertMatchesRegularExpression('/MAD\s*200/u', $response->getContent());
        $response
            ->assertOk()
            ->assertViewIs('site.checkout.request')
            ->assertSee('Contact Experience')
            ->assertSee('value="Sarah"', false)
            ->assertSee('value="Mitchell"', false)
            ->assertSee('value="Canada"', false)
            ->assertSee('Confirm booking');
    }

    public function test_guest_sees_sign_in_instead_of_the_contact_form(): void
    {
        $checkoutSession = $this->createCheckoutSession();

        $this->withSession([])->get(route('site.checkout.contact', ['locale' => 'en', 'checkoutSession' => $checkoutSession]))
            ->assertForbidden();
    }

    public function test_contact_email_is_required_and_valid(): void
    {
        $user = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->actingAs($user)->post(route('site.checkout.contact.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'contact_first_name' => 'Hatem',
            'contact_last_name' => 'Ali',
            'contact_email' => 'not-an-email',
            'terms' => '1',
        ])->assertSessionHasErrors('contact_email');
    }

    public function test_cannot_update_contact_for_another_user_checkout_session(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $owner->id,
            'session_id' => null,
        ]);

        $this->actingAs($otherUser)->post(route('site.checkout.contact.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'contact_first_name' => 'Other',
            'contact_last_name' => 'User',
            'contact_email' => 'other@example.com',
            'terms' => '1',
        ])->assertForbidden();
    }

    private function createCheckoutSession(array $overrides = []): CheckoutSession
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Contact Agency '.uniqid(),
            'slug' => 'contact-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);
        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Contact Experience'],
            'description' => ['en' => 'Contact description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);
        $availability = Availability::query()->create([
            'experience_id' => $experience->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 2,
            'is_active' => true,
        ]);

        return CheckoutSession::query()->create(array_merge([
            'uuid' => (string) Str::uuid(),
            'session_id' => 'guest-session-id',
            'experience_id' => $experience->id,
            'availability_id' => $availability->id,
            'locale' => 'en',
            'currency' => 'MAD',
            'booking_type' => 'group',
            'participants' => ['adult' => 2],
            'participants_count' => 2,
            'charged_seats' => 2,
            'pickup_status' => 'unknown',
            'price_snapshot' => ['unit_price' => '100.00', 'total' => '200.00'],
            'payment_method' => 'manual',
            'payment_status' => 'pending',
            'status' => 'active',
            'reserved_until' => now()->addMinutes(30),
        ], $overrides));
    }
}
