<?php

namespace Database\Seeders;

use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Models\Booking;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Review\Actions\SubmitReviewAction;
use App\Domain\Review\DTOs\ReviewData;
use App\Domain\Wishlist\Models\Wishlist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DemoClientSeeder extends Seeder
{
    public function run(
        CreateBookingAction $createBookingAction,
        SubmitReviewAction $submitReviewAction,
    ): void {
        Role::findOrCreate('user', 'web');

        $client = User::query()->updateOrCreate(
            ['email' => $this->stringConfig('services.demo_access.client_email')],
            [
                'name' => $this->stringConfig('services.demo_access.client_name'),
                'password' => Hash::make($this->password()),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $client->assignRole('user');

        $experiences = Experience::query()
            ->published()
            ->with('agency')
            ->orderBy('id')
            ->limit(4)
            ->get();

        if ($experiences->count() < 3) {
            $this->command?->warn('DemoClientSeeder: not enough published experiences to seed demo bookings — skipping bookings.');

            return;
        }

        [$pastExperience, $confirmedExperience, $pendingExperience] = $experiences->take(3)->all();
        $wishlistExperience = $experiences->last();

        $completedBooking = $this->bookExperience(
            $createBookingAction,
            $client,
            $pastExperience,
            now()->subDays(9)->toDateString(),
        );

        if ($completedBooking->status !== 'completed') {
            $completedBooking->forceFill(['status' => 'completed'])->save();
        }

        if (! $completedBooking->review()->exists()) {
            $submitReviewAction->execute($completedBooking, new ReviewData(
                bookingId: $completedBooking->id,
                rating: 5,
                body: 'Unforgettable trip — our guide was warm, knowledgeable, and made sure every detail was taken care of. Highly recommend booking this one.',
            ));
        }

        $confirmedBooking = $this->bookExperience(
            $createBookingAction,
            $client,
            $confirmedExperience,
            now()->addDays(12)->toDateString(),
        );

        if ($confirmedBooking->status !== 'confirmed') {
            $confirmedBooking->forceFill(['status' => 'confirmed'])->save();
        }

        $this->bookExperience(
            $createBookingAction,
            $client,
            $pendingExperience,
            now()->addDays(20)->toDateString(),
        );

        Wishlist::query()->updateOrCreate([
            'user_id' => $client->id,
            'experience_id' => $wishlistExperience->id,
        ]);

        $this->command?->info("DemoClientSeeder: seeded client {$client->email} with 1 completed (reviewed), 1 confirmed, and 1 pending booking.");
    }

    private function bookExperience(
        CreateBookingAction $createBookingAction,
        User $client,
        Experience $experience,
        string $date,
    ): Booking {
        $existing = Booking::query()
            ->where('user_id', $client->id)
            ->where('experience_id', $experience->id)
            ->whereHas('availability', fn ($query) => $query->whereDate('date', $date))
            ->first();

        if ($existing) {
            return $existing;
        }

        $availability = Availability::query()->updateOrCreate(
            [
                'experience_id' => $experience->id,
                'date' => $date,
                'time_slot' => '09:00:00',
            ],
            [
                'max_seats' => $experience->max_group_size,
                'booked_seats' => 0,
            ],
        );

        return $createBookingAction->execute(new BookingData(
            availabilityId: $availability->id,
            bookingType: 'group',
            participantsCount: min(2, $experience->max_group_size),
            guestName: $client->name,
            guestEmail: $client->email,
            userId: $client->id,
        ));
    }

    private function password(): string
    {
        $password = config('services.demo_access.client_password');

        if (is_string($password) && $password !== '') {
            return $password;
        }

        if (! app()->environment('production')) {
            return 'password';
        }

        throw new RuntimeException('DEMO_CLIENT_PASSWORD must be set before seeding the demo client in production.');
    }

    private function stringConfig(string $key): string
    {
        $value = config($key);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException("Missing string config value for [{$key}].");
        }

        return $value;
    }
}
