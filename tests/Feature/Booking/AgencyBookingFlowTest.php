<?php

namespace Tests\Feature\Booking;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AgencyBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_can_list_only_its_own_bookings(): void
    {
        $agencyAvailability = $this->createAvailability('Agency Owner', 'agency@example.com', 'Agency One', 'agency-one', 'Agency Desert Tour');
        $otherAvailability = $this->createAvailability('Other Owner', 'other@example.com', 'Agency Two', 'agency-two', 'Other Desert Tour');

        app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $agencyAvailability->id,
            bookingType: 'group',
            participantsCount: 2,
            guestName: 'Visible Guest',
            guestEmail: 'visible@example.com',
        ));

        app(CreateBookingAction::class)->execute(new BookingData(
            availabilityId: $otherAvailability->id,
            bookingType: 'group',
            participantsCount: 1,
            guestName: 'Hidden Guest',
            guestEmail: 'hidden@example.com',
        ));

        $this->actingAs($agencyAvailability->experience->agency->user)
            ->get(route('agency.bookings.index'))
            ->assertOk()
            ->assertSee('Visible Guest')
            ->assertSee('Agency Desert Tour')
            ->assertDontSee('Hidden Guest')
            ->assertDontSee('Other Desert Tour');
    }

    private function createAvailability(string $ownerName, string $ownerEmail, string $agencyName, string $agencySlug, string $experienceTitle): Availability
    {
        $owner = User::factory()->create([
            'name' => $ownerName,
            'email' => $ownerEmail,
        ]);
        $owner->assignRole(Role::findOrCreate('travel_agency', 'web'));

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
            'title' => ['en' => $experienceTitle],
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
