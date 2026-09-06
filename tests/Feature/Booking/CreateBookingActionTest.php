<?php

namespace Tests\Feature\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateBookingActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_booking_creates_pending_booking_and_reserves_requested_seats(): void
    {
        $availability = $this->createAvailability();

        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));

        $this->assertSame('pending', $booking->status);
        $this->assertSame('group', $booking->booking_type);
        $this->assertSame('200.00', $booking->total_price);
        $this->assertSame(2, $availability->refresh()->booked_seats);
    }

    public function test_private_booking_creates_pending_booking_and_locks_whole_slot(): void
    {
        $availability = $this->createAvailability();

        $booking = app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'private',
            participantsCount: 3,
            guestName: 'Test Guest',
            guestEmail: 'guest@example.com',
        ));

        $this->assertSame('pending', $booking->status);
        $this->assertSame('private', $booking->booking_type);
        $this->assertSame('450.00', $booking->total_price);
        $this->assertSame($availability->max_seats, $availability->refresh()->booked_seats);
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
