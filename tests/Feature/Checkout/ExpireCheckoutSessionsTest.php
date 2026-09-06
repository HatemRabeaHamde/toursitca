<?php

namespace Tests\Feature\Checkout;

use App\Domain\Agency\Models\Agency;
use App\Domain\Checkout\Actions\ExpireCheckoutSessionsAction;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpireCheckoutSessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_checkout_sessions_release_held_seats(): void
    {
        [$checkoutSession, $availability] = $this->createCheckoutSession([
            'reserved_until' => now()->subMinute(),
            'charged_seats' => 3,
            'payment_status' => 'pending',
        ], [
            'held_seats' => 3,
        ]);

        $expiredCount = app(ExpireCheckoutSessionsAction::class)->execute();

        $this->assertSame(1, $expiredCount);
        $this->assertSame(0, $availability->refresh()->held_seats);
        $this->assertSame('expired', $checkoutSession->refresh()->status);
        $this->assertSame('cancelled', $checkoutSession->payment_status);
    }

    public function test_active_checkout_sessions_are_not_expired(): void
    {
        [$checkoutSession, $availability] = $this->createCheckoutSession([
            'reserved_until' => now()->addMinutes(10),
            'charged_seats' => 2,
        ], [
            'held_seats' => 2,
        ]);

        $expiredCount = app(ExpireCheckoutSessionsAction::class)->execute();

        $this->assertSame(0, $expiredCount);
        $this->assertSame(2, $availability->refresh()->held_seats);
        $this->assertSame('active', $checkoutSession->refresh()->status);
    }

    public function test_expire_command_reports_expired_count(): void
    {
        $this->createCheckoutSession([
            'reserved_until' => now()->subMinute(),
            'charged_seats' => 1,
        ], [
            'held_seats' => 1,
        ]);

        $this->artisan('checkout:expire')
            ->expectsOutput('Expired 1 checkout session(s).')
            ->assertSuccessful();
    }

    private function createCheckoutSession(array $sessionOverrides = [], array $availabilityOverrides = []): array
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Expire Agency '.uniqid(),
            'slug' => 'expire-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);
        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Expire Experience'],
            'description' => ['en' => 'Expire description'],
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
