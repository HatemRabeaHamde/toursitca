<?php

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Services\CommissionCalculator;
use App\Domain\Booking\Services\SeatReservationService;
use App\Domain\Experience\Models\Availability;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateBookingAction
{
    public function __construct(
        private readonly SeatReservationService $seatReservationService,
        private readonly CommissionCalculator $commissionCalculator,
    ) {}

    public function execute(BookingData $data): Booking
    {
        if ($data->participantsCount > (int) config('booking.max_participants')) {
            throw new InvalidArgumentException('Participants count exceeds platform limit.');
        }

        return DB::transaction(function () use ($data): Booking {
            $availability = Availability::query()
                ->with('experience.agency')
                ->findOrFail($data->availabilityId);

            if ($data->participantsCount > $availability->experience->max_group_size) {
                throw new InvalidArgumentException('Participants count exceeds experience limit.');
            }

            $lockedAvailability = $this->seatReservationService->reserve(
                $availability,
                $data->bookingType,
                $data->participantsCount,
            );

            $lockedAvailability->load('experience.agency');
            $price = $this->commissionCalculator->calculate(
                $lockedAvailability->experience,
                $data->bookingType,
                $data->participantsCount,
            );

            return Booking::query()->create([
                'user_id' => $data->userId,
                'experience_id' => $lockedAvailability->experience_id,
                'availability_id' => $lockedAvailability->id,
                'booking_type' => $data->bookingType,
                'participants_count' => $data->participantsCount,
                'unit_price' => $price['unit_price'],
                'total_price' => $price['total_price'],
                'commission_rate' => $price['commission_rate'],
                'commission_amount' => $price['commission_amount'],
                'agency_amount' => $price['agency_amount'],
                'status' => 'pending',
                'guest_name' => $data->guestName,
                'guest_email' => $data->guestEmail,
                'guest_phone' => $data->guestPhone,
                'special_notes' => $data->specialNotes,
                'reserved_until' => now()->addHours((int) config('booking.unpaid_ttl_hours')),
            ]);
        });
    }
}
