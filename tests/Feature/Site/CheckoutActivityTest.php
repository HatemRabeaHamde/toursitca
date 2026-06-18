<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_pickup_details_for_owned_checkout_session(): void
    {
        $user = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->actingAs($user)->post(route('site.checkout.activity.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'pickup_status' => 'add_now',
            'pickup_address' => 'Riad Example, Marrakesh',
            'pickup_lat' => '31.6294723',
            'pickup_lng' => '-7.9810845',
        ])
            ->assertRedirect(route('site.checkout.contact', [
                'locale' => 'en',
                'checkoutSession' => $checkoutSession,
            ]));

        $checkoutSession->refresh();

        $this->assertSame('add_now', $checkoutSession->pickup_status);
        $this->assertSame('Riad Example, Marrakesh', $checkoutSession->pickup_address);
        $this->assertSame('31.6294723', $checkoutSession->pickup_lat);
        $this->assertSame('-7.9810845', $checkoutSession->pickup_lng);
    }

    public function test_pickup_address_is_required_when_adding_now(): void
    {
        $user = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->actingAs($user)->post(route('site.checkout.activity.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'pickup_status' => 'add_now',
        ])->assertSessionHasErrors('pickup_address');
    }

    public function test_cannot_update_checkout_session_owned_by_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $checkoutSession = $this->createCheckoutSession([
            'user_id' => $owner->id,
            'session_id' => null,
        ]);

        $this->actingAs($otherUser)->post(route('site.checkout.activity.store', [
            'locale' => 'en',
            'checkoutSession' => $checkoutSession,
        ]), [
            'pickup_status' => 'unknown',
        ])->assertForbidden();
    }

    private function createCheckoutSession(array $overrides = []): CheckoutSession
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Activity Agency '.uniqid(),
            'slug' => 'activity-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);
        $experience = Experience::query()->create([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Activity Experience'],
            'description' => ['en' => 'Activity description'],
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
            'price_snapshot' => ['total' => '200.00'],
            'payment_method' => 'manual',
            'payment_status' => 'pending',
            'status' => 'active',
            'reserved_until' => now()->addMinutes(30),
        ], $overrides));
    }
}
