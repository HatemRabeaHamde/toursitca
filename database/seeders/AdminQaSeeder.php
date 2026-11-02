<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceOption;
use App\Domain\Payout\Models\Payout;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Permission\Models\Role;

/** Explicit test-domain fixtures. Never called by DatabaseSeeder. No real records are updated. */
class AdminQaSeeder extends Seeder
{
    public function run(): void
    {
        $password = getenv('QA_SEED_PASSWORD');
        if (! is_string($password) || strlen($password) < 16) {
            throw new RuntimeException('Set QA_SEED_PASSWORD to at least 16 characters.');
        }
        if (User::withTrashed()->where('email', 'qa-admin@tourstica.test')->exists()) {
            $this->command?->info('QA batch already exists; existing data and credentials were preserved.');

            return;
        }

        DB::transaction(function () use ($password): void {
            foreach (['admin', 'travel_agency', 'user'] as $role) {
                Role::findOrCreate($role, 'web');
            }
            $admin = $this->account('qa-admin@tourstica.test', '[TEST] QA administrator', 'admin', $password);
            $clients = [];
            for ($n = 1; $n <= 20; $n++) {
                $clients[] = $this->account(sprintf('qa-client%02d@tourstica.test', $n), '[TEST] Traveller '.$n, 'user', $password);
            }
            foreach (['Marrakesh', 'Fes', 'Rabat', 'Essaouira', 'Agadir', 'Tangier', 'Meknes', 'Ouarzazate'] as $index => $city) {
                $this->agency($index + 1, $city, $admin, $clients, $password);
            }
        });
    }

    private function account(string $email, string $name, string $role, string $password): User
    {
        // create(), not updateOrCreate(): a collision rolls back instead of resetting another account.
        $user = User::create(['email' => $email, 'name' => $name, 'password' => $password,
            'status' => 'active', 'preferred_lang' => 'en', 'email_verified_at' => now()]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);

        return $user;
    }

    private function agency(int $number, string $city, User $admin, array $clients, string $password): void
    {
        $owner = $this->account(sprintf('qa-agent%02d@tourstica.test', $number), '[TEST] Agency owner '.$number, 'travel_agency', $password);
        $agency = Agency::create(['user_id' => $owner->id, 'slug' => 'qa-seed-agency-'.$number,
            'name' => '[TEST] QA agency '.$number, 'description' => $this->translations('Synthetic agency for dashboard testing; not a real supplier.'),
            'commission_rate' => '15.00', 'status' => match ($number) {
                7 => 'pending', 8 => 'suspended', default => 'active'
            },
            'city' => $city, 'languages' => ['en', 'fr', 'nl'], 'is_platform' => false]);
        $hosts = [];
        for ($n = 1; $n <= 2; $n++) {
            $hosts[] = Host::create(['agency_id' => $agency->id, 'slug' => "qa-seed-host-$number-$n", 'name' => "[TEST] Host $number.$n",
                'role' => $this->translations('Test host profile'), 'bio' => $this->translations('Fictional profile for testing the host editor. No licence or verification is claimed.'),
                'city' => $city, 'languages' => ['en', 'fr', 'nl'], 'quote' => $this->translations('Test profile copy'),
                'story' => $this->translations(['Synthetic biography for editor testing.']), 'skills' => $this->translations(['Test skill']),
                'is_verified' => false, 'is_active' => false, 'reviewed_at' => $n === 1 ? null : now(), 'sort_order' => $number * 2 + $n]);
        }
        for ($n = 1; $n <= 3; $n++) {
            $experience = Experience::create(['agency_id' => $agency->id, 'host_id' => $hosts[($n - 1) % 2]->id,
                'created_by' => $admin->id, 'slug' => "qa-seed-trip-$number-$n", 'title' => $this->translations("[TEST] $city booking scenario $n"),
                'description' => $this->translations('Synthetic test listing. Prices, packages and schedules are fixtures, not a real travel offer.'),
                'category' => ['culture', 'food', 'desert'][$n - 1], 'difficulty' => 'easy', 'duration_hours' => '4.0',
                'max_group_size' => 10, 'price_per_person' => '100.00', 'private_price' => '1000.00',
                'pickup_enabled' => $n !== 3, 'location_city' => $city, 'status' => 'draft',
                'highlights' => $this->translations(['[TEST] Check translated content and booking details']),
                'inclusions' => $this->translations(['[TEST] Sample inclusion']), 'exclusions' => $this->translations(['[TEST] Sample exclusion'])]);
            $options = $this->options($experience);
            foreach ($options as $index => $option) {
                foreach ([7, 14, 21, 28] as $days) {
                    $this->slot($experience, $option, $days, sprintf('%02d:00', 9 + $index));
                }
            }
            foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $index => $status) {
                $client = $clients[(($number - 1) * 3 + $n + $index) % count($clients)];
                $this->booking($experience, $options[0], $client, $admin, $status, $index, $n === 2);
            }
        }
    }

    /** @return list<ExperienceOption> */
    private function options(Experience $experience): array
    {
        $options = [];
        foreach (['Standard', 'Extended', 'Group'] as $index => $label) {
            $option = ExperienceOption::create(['experience_id' => $experience->id, 'title' => $this->translations('[TEST] '.$label),
                'duration_minutes' => 240, 'pickup_enabled' => $experience->pickup_enabled, 'private_available' => true,
                'pay_later_enabled' => false, 'cancellation_hours' => 24, 'price_type' => $index === 2 ? 'per_group' : 'per_person', 'status' => 'active']);
            $prices = $index === 2 ? ['group' => '800.00'] : ['adult' => $index === 0 ? '100.00' : '150.00', 'child' => '60.00'];
            foreach ($prices as $type => $price) {
                $option->prices()->create(['participant_type' => $type, 'price' => $price, 'currency' => 'MAD']);
            }
            foreach (['en', 'fr', 'nl'] as $locale) {
                $option->languages()->create(['language_code' => $locale, 'type' => 'live_guide']);
            }
            $options[] = $option;
        }

        return $options;
    }

    private function slot(Experience $experience, ExperienceOption $option, int $days, string $time): Availability
    {
        return Availability::create(['experience_id' => $experience->id, 'experience_option_id' => $option->id,
            'date' => now()->addDays($days)->toDateString(), 'time_slot' => $time,
            'max_seats' => 10, 'booked_seats' => 0, 'held_seats' => 0, 'is_active' => true]);
    }

    private function booking(Experience $experience, ExperienceOption $option, User $client, User $admin, string $status, int $index, bool $private): void
    {
        $private = $private && $status === 'confirmed';
        $slot = $this->slot($experience, $option, $status === 'completed' ? -7 : 8 + $index, '15:00');
        $slot = Availability::whereKey($slot->id)->lockForUpdate()->firstOrFail();
        $unit = Money::of($private ? '1000.00' : '100.00', 'MAD');
        $total = $private ? $unit : $unit->multipliedBy(2);
        $commission = $total->multipliedBy('0.15', RoundingMode::HALF_UP);
        $agencyAmount = $total->minus($commission);
        $charged = $private ? $slot->max_seats : 2;
        $booking = Booking::create(['user_id' => $client->id, 'experience_id' => $experience->id,
            'experience_option_id' => $option->id, 'availability_id' => $slot->id, 'booking_type' => $private ? 'private' : 'group',
            'participants_count' => 2, 'participants' => ['adult' => 2], 'charged_seats' => $charged, 'tour_language' => 'en',
            'pickup_status' => $experience->pickup_enabled ? 'unknown' : 'not_required',
            'unit_price' => (string) $unit->getAmount(), 'total_price' => (string) $total->getAmount(),
            'commission_rate' => '15.00', 'commission_amount' => (string) $commission->getAmount(),
            'agency_amount' => (string) $agencyAmount->getAmount(),
            'price_snapshot' => ['currency' => 'MAD', 'unit_price' => (string) $unit->getAmount(), 'total' => (string) $total->getAmount()],
            'status' => $status, 'guest_name' => $client->name, 'guest_email' => $client->email,
            'special_notes' => '[TEST] Synthetic QA booking. Do not contact or fulfil.',
            'cancelled_at' => $status === 'cancelled' ? now() : null,
            'cancellation_reason' => $status === 'cancelled' ? '[TEST] Cancellation scenario' : null]);
        if (in_array($status, ['confirmed', 'completed'], true)) {
            $slot->update(['booked_seats' => $charged]);
            $paid = $status === 'completed';
            Payout::create(['booking_id' => $booking->id, 'agency_id' => $experience->agency_id,
                'amount' => (string) $agencyAmount->getAmount(), 'status' => $paid ? 'paid' : 'pending',
                'notes' => '[TEST] Fixture only; no money was transferred.',
                'transferred_at' => $paid ? now()->subDays(2) : null, 'transferred_by' => $paid ? $admin->id : null]);
        }
        if ($status === 'completed') {
            Review::create(['booking_id' => $booking->id, 'user_id' => $client->id, 'experience_id' => $experience->id,
                'rating' => 4, 'body' => '[TEST] Synthetic review for moderation testing; not traveller feedback.', 'is_visible' => false]);
        }
    }

    private function translations(string|array $value): array
    {
        return array_fill_keys(['en', 'fr', 'nl'], $value);
    }
}
