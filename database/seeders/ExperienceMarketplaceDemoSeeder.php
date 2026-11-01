<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Booking\Models\Booking;
use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceMedia;
use App\Domain\Review\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Demo marketplace (local / staging only): the nine Figma experiences with
 * local images and named hosts, future availability, and a few completed
 * bookings with reviews so ratings and the reviews section are real data.
 * Idempotent — safe to run again.
 */
class ExperienceMarketplaceDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $data = require __DIR__.'/data/experiences.php';
        $agency = Agency::query()->where('slug', 'platform')->first();
        if (! $agency) {
            return;
        }

        $experiences = $this->experiences($data['experiences'], $agency);
        $travellers = $this->travellers($data['travellers']);
        $this->reviews($data['reviews'], $experiences, $travellers);
    }

    /** @return array<string, Experience> */
    private function experiences(array $rows, Agency $agency): array
    {
        $hosts = Host::query()->pluck('id', 'slug');

        return collect($rows)->mapWithKeys(function (array $row, int $index) use ($agency, $hosts): array {
            $experience = Experience::query()->updateOrCreate(['slug' => $row['slug']], [
                'agency_id' => $agency->id,
                'created_by' => $agency->user_id,
                'host_id' => $hosts[$row['host']] ?? null,
                'title' => $row['title'],
                'description' => $row['description'],
                'category' => $row['category'],
                'difficulty' => 'easy',
                'duration_hours' => (string) $row['hours'],
                'max_group_size' => $row['group'],
                'price_per_person' => number_format($row['price'], 2, '.', ''),
                'private_price' => null,
                'pickup_enabled' => true,
                'location_city' => $row['city'],
                'status' => 'published',
                'is_top_rated' => $row['top'],
                'created_at' => now()->subDays(20 - $index),
            ]);

            ExperienceMedia::query()->where('experience_id', $experience->id)->delete();
            foreach ([$row['image'], ...($row['gallery'] ?? [])] as $order => $path) {
                ExperienceMedia::query()->create([
                    'experience_id' => $experience->id,
                    'type' => 'image',
                    'source_type' => 'upload',
                    'path' => $path,
                    'sort_order' => $order,
                ]);
            }

            $this->story($experience, $row['story'] ?? null);

            foreach ([10, 24] as $days) {
                $this->slot($experience, now()->addDays($days + $index)->toDateString(), ['max_seats' => $row['group'], 'booked_seats' => 0, 'held_seats' => 0, 'is_active' => true]);
            }

            return [$row['slug'] => $experience];
        })->all();
    }

    /** Highlights, what's included and a day-by-day itinerary for the detail page. */
    private function story(Experience $experience, ?array $story): void
    {
        $experience->itineraryItems()->delete();

        if ($story === null) {
            return;
        }

        $experience->update([
            'highlights' => $story['highlights'],
            'inclusions' => $story['inclusions'],
            'exclusions' => $story['exclusions'],
        ]);

        foreach ($story['itinerary'] as $order => $step) {
            $experience->itineraryItems()->create([
                'sort_order' => $order,
                'type' => 'stop',
                'title' => $step['title'],
                'description' => $step['description'],
                'image' => $step['image'] ?? null,
                'location_name' => $step['place']['name'] ?? null,
                'location_lat' => $step['place']['lat'] ?? null,
                'location_lng' => $step['place']['lng'] ?? null,
                'is_main_stop' => $order === 0,
            ]);
        }
    }

    /** @return array<string, array{user: User, country: string}> */
    private function travellers(array $rows): array
    {
        return collect($rows)->mapWithKeys(function (array $row): array {
            $user = User::query()->firstOrCreate(
                ['email' => $row['key'].'@travellers.example.test'],
                [
                    'name' => $row['name'],
                    'password' => Hash::make(Str::password(16)),
                    'preferred_lang' => config('locales.default', 'en'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ],
            );
            $user->forceFill(['name' => $row['name'], 'avatar' => $row['avatar'] ? '/'.$row['avatar'] : null])->save();

            return [$row['key'] => ['user' => $user, 'country' => $row['country']]];
        })->all();
    }

    /**
     * One completed booking per review (on the trip date), then ratings are
     * recomputed from the reviews exactly like UpdateExperienceRating does.
     * Previous demo bookings/reviews are removed first so dates stay exact.
     *
     * @param  array<string, Experience>  $experiences
     * @param  array<string, array{user: User, country: string}>  $travellers
     */
    private function reviews(array $rows, array $experiences, array $travellers): void
    {
        $this->forgetDemoTrips(collect($travellers)->map(fn (array $t): int => $t['user']->id)->values()->all());
        $count = count($rows);

        foreach ($rows as $index => $row) {
            $experience = $experiences[$row['experience']];
            $traveller = $travellers[$row['by']];
            $writtenAt = now()->subDays(($count - $index) * 6)->setTime(10, 0);

            $availability = $this->slot($experience, $row['travelled'], ['max_seats' => $experience->max_group_size, 'booked_seats' => 2, 'held_seats' => 0, 'is_active' => false]);

            $price = (float) $experience->price_per_person;
            $booking = Booking::query()->create([
                'user_id' => $traveller['user']->id,
                'experience_id' => $experience->id,
                'availability_id' => $availability->id,
                'booking_type' => 'group',
                'participants_count' => 2,
                'unit_price' => number_format($price, 2, '.', ''),
                'total_price' => number_format($price * 2, 2, '.', ''),
                'commission_rate' => '0.00',
                'commission_amount' => '0.00',
                'agency_amount' => number_format($price * 2, 2, '.', ''),
                'status' => 'completed',
                'guest_name' => $traveller['user']->name,
                'guest_email' => $traveller['user']->email,
                'contact_country' => $traveller['country'],
            ]);

            [$host, $organisation, $pickup, $value] = $row['details'];
            $review = new Review([
                'booking_id' => $booking->id,
                'user_id' => $traveller['user']->id,
                'experience_id' => $experience->id,
                'rating' => $row['rating'],
                'rating_host' => $host,
                'rating_organisation' => $organisation,
                'rating_pickup' => $pickup,
                'rating_value' => $value,
                'body' => $row['body'],
                'is_visible' => true,
                'agency_reply' => $row['reply'] ?? null,
                'replied_at' => isset($row['reply']) ? $writtenAt->copy()->addDay() : null,
            ]);
            $review->created_at = $writtenAt;
            $review->updated_at = $writtenAt;
            $review->save();

            foreach ($row['photos'] ?? [] as $order => $path) {
                $review->photos()->create(['path' => $path, 'sort_order' => $order]);
            }
        }

        foreach ($experiences as $experience) {
            $experience->forceFill([
                'rating_avg' => (float) ($experience->reviews()->avg('rating') ?? 0),
                'reviews_count' => $experience->reviews()->count(),
            ])->save();
        }
    }

    /** @param list<int> $userIds */
    private function forgetDemoTrips(array $userIds): void
    {
        $bookings = Booking::withTrashed()->whereIn('user_id', $userIds)->get(['id', 'availability_id']);

        Review::withTrashed()->whereIn('booking_id', $bookings->pluck('id'))->forceDelete();
        Booking::withTrashed()->whereIn('id', $bookings->pluck('id'))->forceDelete();
        Availability::query()->whereIn('id', $bookings->pluck('availability_id'))->where('is_active', false)->delete();
    }

    /** Find-or-create a 09:00 slot (dates are matched by day, whatever the driver stores). */
    private function slot(Experience $experience, string $date, array $attributes): Availability
    {
        $slot = Availability::query()->where('experience_id', $experience->id)->whereDate('date', $date)->first()
            ?? new Availability(['experience_id' => $experience->id, 'date' => $date, 'time_slot' => '09:00:00']);

        $slot->fill($attributes)->save();

        return $slot;
    }
}
