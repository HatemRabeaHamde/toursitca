<?php

namespace Tests\Feature\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Actions\CancelBookingAction;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Experience\Actions\UpdateAvailabilityAction;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Payout\Actions\MarkPayoutPaidAction;
use App\Domain\Payout\Models\Payout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_and_confirm_pending_booking(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        $availability = $this->createAvailability();
        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Tourist Guest',
            guestEmail: 'guest@example.com',
        ));

        // Existing requests retain the legacy manual confirmation path.
        $booking->forceFill(['status' => 'pending'])->save();
        Payout::query()->where('booking_id', $booking->id)->delete();

        $this->actingAs($admin)
            ->get(route('admin.bookings.index'))
            ->assertOk()
            ->assertSee('Tourist Guest')
            ->assertSee('Desert Tour');

        $this->actingAs($admin)
            ->post(route('admin.bookings.confirm', $booking))
            ->assertRedirect(route('admin.bookings.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('confirmed', $booking->refresh()->status);
        $this->assertDatabaseHas(Payout::class, [
            'booking_id' => $booking->id,
            'agency_id' => $booking->experience->agency_id,
            'amount' => '170.00',
            'status' => 'pending',
        ]);
    }

    public function test_admin_cannot_confirm_non_pending_booking_again(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        $availability = $this->createAvailability();
        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 1,
            guestName: 'Tourist Guest',
            guestEmail: 'guest@example.com',
        ));
        $booking->forceFill(['status' => 'confirmed'])->save();

        $this->actingAs($admin)
            ->post(route('admin.bookings.confirm', $booking))
            ->assertRedirect(route('admin.bookings.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('payouts', 1);
    }

    public function test_admin_can_update_booking_payment_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        $availability = $this->createAvailability();
        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 1,
            guestName: 'Tourist Guest',
            guestEmail: 'guest@example.com',
        ));

        $this->actingAs($admin)
            ->patch(route('admin.bookings.payment-status.update', $booking), [
                'payment_status' => 'paid',
            ])
            ->assertRedirect(route('admin.bookings.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('paid', $booking->refresh()->payment_status);
    }

    public function test_admin_bookings_can_be_filtered_by_payment_status(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));

        $paidAvailability = $this->createAvailability('Paid Agency', 'paid-agency');
        $paidBooking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $paidAvailability->id,
            bookingType: 'group',
            participantsCount: 1,
            guestName: 'Paid Guest',
            guestEmail: 'paid@example.com',
        ));
        $paidBooking->forceFill(['payment_status' => 'paid'])->save();

        $pendingAvailability = $this->createAvailability('Pending Agency', 'pending-agency');
        app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $pendingAvailability->id,
            bookingType: 'group',
            participantsCount: 1,
            guestName: 'Pending Guest',
            guestEmail: 'pending@example.com',
        ));

        $this->actingAs($admin)
            ->get(route('admin.bookings.index', ['payment_status' => 'paid']))
            ->assertOk()
            ->assertSee('Paid Guest')
            ->assertDontSee('Pending Guest');
    }

    public function test_cancellation_releases_seats_and_keeps_cancelled_payout_out_of_payables(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin', 'web'));
        $availability = $this->createAvailability();
        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id, bookingType: 'group', participantsCount: 2,
            guestName: 'Cancel test', guestEmail: 'cancel@example.test',
        ));
        app(CancelBookingAction::class)->execute($booking);
        $this->assertSame(0, $availability->fresh()->booked_seats);
        $payout = $booking->payout()->firstOrFail();
        $this->assertSame('cancelled', $payout->status);
        $this->expectException(ValidationException::class);
        app(MarkPayoutPaidAction::class)->execute($payout, $admin);
    }

    public function test_private_booking_keeps_slot_locked_when_capacity_increases(): void
    {
        $availability = $this->createAvailability();
        app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id, bookingType: 'private', participantsCount: 2,
            guestName: 'Private test', guestEmail: 'private@example.test',
        ));
        app(UpdateAvailabilityAction::class)->execute($availability, [
            'experience_id' => $availability->experience_id, 'experience_option_id' => null,
            'date' => $availability->date->toDateString(), 'time_slot' => substr($availability->time_slot, 0, 5),
            'max_seats' => 8, 'is_active' => true,
        ]);
        $this->assertSame(8, $availability->fresh()->booked_seats);
        $this->assertSame(0, $availability->fresh()->availableSeats());
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
