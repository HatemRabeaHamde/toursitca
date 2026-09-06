<?php

namespace Tests\Feature\Site;

use App\Domain\Agency\Models\Agency;
use App\Domain\Checkout\Models\CheckoutSession;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStartTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_checkout_start_holds_requested_seats(): void
    {
        $experience = $this->createExperience();
        $availability = $this->createAvailability($experience);

        $this->postJson(route('site.checkout.start', ['locale' => 'en']), [
            'experience_id' => $experience->id,
            'availability_id' => $availability->id,
            'booking_type' => 'group',
            'participants' => [
                'adult' => 2,
                'child' => 1,
            ],
            'payment_method' => 'manual',
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'active');

        $checkoutSession = CheckoutSession::query()->firstOrFail();

        $this->assertSame(3, $availability->refresh()->held_seats);
        $this->assertSame(3, $checkoutSession->participants_count);
        $this->assertSame(3, $checkoutSession->charged_seats);
        $this->assertSame('manual', $checkoutSession->payment_method);
        $this->assertSame('pending', $checkoutSession->payment_status);
        $this->assertSame('active', $checkoutSession->status);
        $this->assertTrue($checkoutSession->reserved_until->isFuture());
    }

    public function test_private_checkout_start_holds_all_seats(): void
    {
        $experience = $this->createExperience([
            'price_per_person' => '100.00',
            'private_price' => null,
            'max_group_size' => 10,
        ]);
        $availability = $this->createAvailability($experience, [
            'max_seats' => 10,
        ]);

        $this->postJson(route('site.checkout.start', ['locale' => 'en']), [
            'experience_id' => $experience->id,
            'availability_id' => $availability->id,
            'booking_type' => 'private',
            'participants' => [
                'adult' => 3,
            ],
            'payment_method' => 'pay_later',
        ])->assertCreated();

        $checkoutSession = CheckoutSession::query()->firstOrFail();

        $this->assertSame(10, $availability->refresh()->held_seats);
        $this->assertSame(3, $checkoutSession->participants_count);
        $this->assertSame(10, $checkoutSession->charged_seats);
        $this->assertSame('1000.00', $checkoutSession->price_snapshot['total']);
        $this->assertSame('pay_later', $checkoutSession->payment_method);
        $this->assertSame('not_required', $checkoutSession->payment_status);
    }

    public function test_checkout_start_rejects_when_not_enough_seats_are_left(): void
    {
        $experience = $this->createExperience();
        $availability = $this->createAvailability($experience, [
            'max_seats' => 3,
            'held_seats' => 2,
        ]);

        $this->postJson(route('site.checkout.start', ['locale' => 'en']), [
            'experience_id' => $experience->id,
            'availability_id' => $availability->id,
            'booking_type' => 'group',
            'participants_count' => 2,
        ])->assertUnprocessable();
    }

    private function createExperience(array $overrides = []): Experience
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Checkout Agency '.uniqid(),
            'slug' => 'checkout-agency-'.uniqid(),
            'description' => ['en' => 'Agency description'],
            'commission_rate' => '15.00',
            'status' => 'active',
            'city' => 'Marrakesh',
        ]);

        return Experience::query()->create(array_merge([
            'agency_id' => $agency->id,
            'created_by' => $owner->id,
            'title' => ['en' => 'Checkout Experience'],
            'description' => ['en' => 'Checkout description'],
            'category' => 'desert',
            'difficulty' => 'easy',
            'duration_hours' => '4.0',
            'max_group_size' => 10,
            'price_per_person' => '100.00',
            'private_price' => '450.00',
            'location_city' => 'Marrakesh',
            'status' => 'published',
        ], $overrides));
    }

    private function createAvailability(Experience $experience, array $overrides = []): Availability
    {
        return Availability::query()->create(array_merge([
            'experience_id' => $experience->id,
            'date' => now()->addWeek()->toDateString(),
            'time_slot' => '09:00:00',
            'max_seats' => 10,
            'booked_seats' => 0,
            'held_seats' => 0,
            'is_active' => true,
        ], $overrides));
    }
}
