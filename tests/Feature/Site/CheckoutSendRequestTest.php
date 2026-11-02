<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Checkout\Actions\ConfirmCheckoutSessionAction;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckoutSendRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_the_request_creates_a_booking_from_the_snapshot(): void
    {
        $user = User::factory()->create();
        [$checkoutSession, $availability] = $this->createCheckoutSession([
            'user_id' => $user->id,
            'session_id' => null,
            'participants' => ['adult' => 2],
            'participants_count' => 2,
            'charged_seats' => 2,
            'pickup_status' => 'add_now',
            'pickup_address' => 'Riad Example',
            'pickup_lat' => '31.6294723',
            'pickup_lng' => '-7.9810845',
            'contact_first_name' => 'Hatem',
            'contact_last_name' => 'Ali',
            'contact_email' => 'hatem@example.com',
            'contact_phone' => '+201000000000',
            'contact_country' => 'Egypt',
            'special_requests' => 'WhatsApp preferred.',
            'price_snapshot' => [
                'unit_price' => '100.00',
                'total' => '200.00',
            ],
            'payment_method' => 'manual',
            'payment_status' => 'pending',
        ], [
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('site.checkout.contact.store', [
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
        ]);

        $booking = Booking::query()->firstOrFail();

        $response->assertRedirect(route('site.bookings.confirmation', [
            'locale' => 'en',
            'booking' => $booking,
        ]));

        $availability->refresh();
        $checkoutSession->refresh();

        $this->assertSame(0, $availability->held_seats);
        $this->assertSame(2, $availability->booked_seats);
        $this->assertSame('completed', $checkoutSession->status);
        $this->assertSame($checkoutSession->id, $booking->checkout_session_id);
        $this->assertSame($user->id, $booking->user_id);
        $this->assertSame(2, $booking->participants_count);
        $this->assertSame(2, $booking->charged_seats);
        $this->assertSame('Riad Example', $booking->pickup_address);
        $this->assertSame('Hatem Ali', $booking->guest_name);
        $this->assertSame('hatem@example.com', $booking->guest_email);
        $this->assertSame('manual', $booking->payment_method);
        $this->assertSame('pending', $booking->payment_status);
        $this->assertSame('confirmed', $booking->status);
        $this->assertNull($booking->reserved_until);
        $this->assertDatabaseHas('payouts', ['booking_id' => $booking->id, 'amount' => '170.00', 'status' => 'pending']);
        $this->assertSame('200.00', $booking->total_price);
        $this->assertSame('30.00', $booking->commission_amount);
    }

    public function test_request_needs_contact_details_and_terms(): void
    {
        $user = User::factory()->create();
        [$checkoutSession] = $this->createCheckoutSession(['user_id' => $user->id, 'session_id' => null]);

        $this->actingAs($user)->post(route('site.checkout.contact.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [])->assertSessionHasErrors(['contact_first_name', 'contact_last_name', 'contact_email', 'terms']);

        $this->assertSame(0, Booking::query()->count());
    }

    public function test_a_guest_session_cannot_send_the_request(): void
    {
        [$checkoutSession] = $this->createCheckoutSession([
            'user_id' => null,
            'session_id' => 'guest-session-id',
        ]);

        $this->post(route('site.checkout.contact.store', [
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
        ])->assertForbidden();
    }

    #[DataProvider('unavailableSlots')]
    public function test_confirmation_rechecks_live_availability_and_rolls_back(string $scenario): void
    {
        [$session, $availability] = $this->createCheckoutSession(['user_id' => User::factory()->create()->id]);
        match ($scenario) {
            'inactive' => $availability->update(['is_active' => false]),
            'past' => $availability->update(['date' => now()->subDay()]),
            'full' => $availability->update(['booked_seats' => 10]),
            'missing_hold' => $availability->update(['held_seats' => 0]),
            'unpublished' => $availability->experience->update(['status' => 'draft']),
            'suspended' => $availability->experience->agency->update(['status' => 'suspended']),
            'expired' => $session->update(['reserved_until' => now()->subMinute()]),
        };
        $before = $availability->fresh()->only(['held_seats', 'booked_seats']);

        try {
            app(ConfirmCheckoutSessionAction::class)->execute($session);
            $this->fail('Unavailable checkout must not confirm.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('bookings', 0);
            $this->assertDatabaseCount('payouts', 0);
            $this->assertSame($before, $availability->fresh()->only(['held_seats', 'booked_seats']));
            $this->assertSame('active', $session->fresh()->status);
        }
    }

    public static function unavailableSlots(): array
    {
        return array_map(fn (string $scenario): array => [$scenario], ['inactive', 'past', 'full', 'missing_hold', 'unpublished', 'suspended', 'expired']);
    }

    public function test_completed_checkout_returns_the_same_booking_without_duplicate_effects(): void
    {
        [$session, $availability] = $this->createCheckoutSession(['user_id' => User::factory()->create()->id]);
        $action = app(ConfirmCheckoutSessionAction::class);
        $first = $action->execute($session);
        $second = $action->execute($session);
        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('payouts', 1);
        $this->assertSame(2, $availability->fresh()->booked_seats);
    }

    public function test_private_platform_booking_confirms_without_creating_a_payout(): void
    {
        [$session, $availability] = $this->createCheckoutSession([
            'user_id' => User::factory()->create()->id,
            'booking_type' => 'private',
            'charged_seats' => 10,
        ], ['held_seats' => 10]);
        $availability->experience->agency->update(['is_platform' => true]);

        $booking = app(ConfirmCheckoutSessionAction::class)->execute($session);

        $this->assertSame('confirmed', $booking->status);
        $this->assertSame(10, $availability->fresh()->booked_seats);
        $this->assertSame(0, $availability->fresh()->held_seats);
        $this->assertDatabaseCount('payouts', 0);
        $this->assertSame('pending', $booking->payment_status);
    }

    private function createCheckoutSession(array $sessionOverrides = [], array $availabilityOverrides = []): array
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Confirm Agency '.uniqid(),
            'slug' => 'confirm-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);
        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Confirm Experience'],
            'description' => ['en' => 'Confirm description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ]);
        $availability = Availability::query()->create(array_merge([
            'experience_id' => $experience->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 2,
            'is_active' => true,
        ], $availabilityOverrides));

        $checkoutSession = CheckoutSession::query()->create(array_merge([
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
            'contact_first_name' => 'Hatem',
            'contact_last_name' => 'Ali',
            'contact_email' => 'hatem@example.com',
            'price_snapshot' => [
                'unit_price' => '100.00',
                'total' => '200.00',
            ],
            'payment_method' => 'manual',
            'payment_status' => 'pending',
            'status' => 'active',
            'reserved_until' => now()->addMinutes(30),
        ], $sessionOverrides));

        return [$checkoutSession, $availability];
    }
}
