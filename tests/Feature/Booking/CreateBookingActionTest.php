<?php

namespace Tests\Feature\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Events\BookingConfirmed;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateBookingActionTest extends TestCase
{
    use DatabaseMigrations;

    public function test_group_booking_creates_confirmed_booking_and_reserves_requested_seats(): void
    {
        $availability = $this->createAvailability();

        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));

        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('group', $booking->booking_type);
        $this->assertSame('200.00', $booking->total_price);
        $this->assertSame(2, $availability->refresh()->booked_seats);
    }

    public function test_private_booking_creates_confirmed_booking_and_locks_whole_slot(): void
    {
        $availability = $this->createAvailability();

        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'private',
            participantsCount: 3,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));

        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('private', $booking->booking_type);
        $this->assertSame('450.00', $booking->total_price);
        $this->assertSame($availability->max_seats, $availability->refresh()->booked_seats);
    }

    public function test_confirmation_event_is_dispatched_only_after_commit(): void
    {
        Event::fake([BookingConfirmed::class]);
        $availability = $this->createAvailability();
        DB::beginTransaction();
        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));
        Event::assertNotDispatched(BookingConfirmed::class);
        DB::commit();

        Event::assertDispatchedTimes(BookingConfirmed::class, 1);
        $this->assertDatabaseHas('payouts', ['booking_id' => $booking->id, 'amount' => '170.00']);
    }

    public function test_rollback_does_not_dispatch_confirmation_or_leave_a_booking(): void
    {
        Event::fake([BookingConfirmed::class]);
        $availability = $this->createAvailability();
        DB::beginTransaction();
        app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));
        DB::rollBack();

        Event::assertNotDispatched(BookingConfirmed::class);
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('payouts', 0);
        $this->assertSame(0, $availability->fresh()->booked_seats);
    }

    private function createAvailability(): Availability
    {
        $owner = User::factory()->create();
        $agency = Agency::query()->create([
            'user_id' => $owner->id,
            'name' => 'Test Agency',
            'slug' => 'test-agency',
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
