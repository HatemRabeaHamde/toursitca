<?php

namespace Database\Seeders;

use App\Domain\Discover\Models\GuideBlock;
use App\Domain\Agency\Models\Agency;
use App\Domain\Discover\Models\Host;
use App\Domain\Discover\Models\Interest;
use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Seeds Discover Morocco: hosts, interests, destination cards + map pins,
 * the Marrakech and Cuisine guides, and sample stories. Idempotent.
 * Content: database/seeders/data/discover.php (copy from the Figma design).
 */
class DiscoverContentSeeder extends Seeder
{
    public function run(): void
    {
        $data = require __DIR__.'/data/discover.php';

        $interests = $this->interests($data['interests']);
        $destinations = $this->destinations($data['destinations']);
        $hosts = $this->hosts($data['hosts'], $destinations, $interests);
        $this->assignHosts($data['experience_hosts'], $hosts);
        $this->linkInterests($data['destination_interests'], $destinations, $interests);
        $this->guides($data['guides']['destinations'], $destinations);
        $this->guides($data['guides']['interests'], $interests);
        $this->stories($data['stories'], $destinations, $interests);
    }

    /**
     * @param array<string, Destination> $destinations
     * @param array<string, Interest> $interests
     * @return array<string, Host>
     */
    private function hosts(array $rows, array $destinations, array $interests): array
    {
        // Demo hosts are curated by the platform agency until real agencies add their own.
        $platformAgencyId = Agency::query()->where('slug', 'platform')->value('id');

        return collect($rows)->mapWithKeys(function (array $row, int $index) use ($destinations, $interests, $platformAgencyId): array {
            $host = Host::query()->withTrashed()->firstOrNew(['slug' => $row['slug']]);
            $host->fill([
                'name' => $row['name'],
                'city' => $row['city'],
                'destination_id' => $destinations[$row['region']]->id ?? null,
                'interest_id' => $interests[$row['specialty']]->id ?? null,
                'photo_url' => $row['photo'],
                'avatar_url' => $row['avatar'],
                'licence_number' => $row['licence'],
                'is_verified' => true,
                'sort_order' => $index,
                'is_active' => true,
            ]);
            $host->setTranslations('role', $row['role']);
            $host->setTranslations('bio', $row['bio'] ?? []);
            $host->setTranslations('quote', $row['profile']['quote'] ?? []);
            $host->setTranslations('story', $row['profile']['story'] ?? []);
            $host->setTranslations('skills', $row['profile']['skills'] ?? []);
            $host->languages = $row['profile']['languages'] ?? null;
            $host->partner_since = $row['profile']['partner_since'] ?? null;
            $host->cover_url = $row['profile']['cover'] ?? null;
            $host->agency_id = $platformAgencyId;
            $host->reviewed_at = now();
            $host->deleted_at = null;
            $host->save();

            return [$row['slug'] => $host];
        })->all();
    }

    /** @param array<string, Host> $hosts */
    private function assignHosts(array $map, array $hosts): void
    {
        foreach ($map as $experienceSlug => $hostSlug) {
            Experience::query()->where('slug', $experienceSlug)->update(['host_id' => $hosts[$hostSlug]->id]);
        }
    }

    /** @return array<string, Interest> */
    private function interests(array $rows): array
    {
        return collect($rows)->mapWithKeys(function (array $row, int $index): array {
            $interest = Interest::query()->withTrashed()->firstOrNew(['slug' => $row['slug']]);
            $interest->fill([
                'image_url' => $row['image'],
                'category_slugs' => $row['categories'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
            $interest->setTranslations('name', $row['name']);
            $interest->setTranslations('summary', $row['summary']);
            $interest->deleted_at = null;
            $interest->save();

            return [$row['slug'] => $interest];
        })->all();
    }

    /** @return array<string, Destination> */
    private function destinations(array $rows): array
    {
        return collect($rows)->mapWithKeys(function (array $row): array {
            $destination = Destination::query()->withTrashed()->firstOrNew(['slug' => $row['slug']]);
            $destination->fill([
                'city' => $row['city'],
                'region' => $row['region'],
                'image_url' => $row['image'] ?? $destination->image_url,
                'hero_image_url' => $row['hero'],
                'map_x' => $row['x'],
                'map_y' => $row['y'],
                'sort_order' => $row['sort'],
                'is_active' => true,
            ]);
            $destination->setTranslations('name', $row['name']);
            $destination->setTranslations('summary', $row['summary']);
            $destination->setTranslations('tags', $row['tags']);
            $destination->deleted_at = null;
            $destination->save();

            return [$row['slug'] => $destination];
        })->all();
    }

    private function linkInterests(array $links, array $destinations, array $interests): void
    {
        foreach ($links as $destinationSlug => $notes) {
            $index = 0;
            foreach ($notes as $interestSlug => $note) {
                $destinations[$destinationSlug]->interests()->syncWithoutDetaching([
                    $interests[$interestSlug]->id => ['note' => ['en' => $note], 'sort_order' => $index++],
                ]);
            }
        }
    }

    /** @param array<string, Model> $models */
    private function guides(array $guides, array $models): void
    {
        foreach ($guides as $slug => $guide) {
            $model = $models[$slug];
            $model->setTranslation('guide', 'en', $guide['guide']);
            if (isset($guide['hero_image'])) {
                $model->hero_image_url = $guide['hero_image'];
            }
            $model->save();

            GuideBlock::query()->whereMorphedTo('guideable', $model)->delete();
            foreach ($guide['blocks'] as $index => $block) {
                $model->blocks()->create([
                    'type' => $block['type'],
                    'title' => $block['title'] ? ['en' => $block['title']] : null,
                    'body' => $block['body'] ? ['en' => $block['body']] : null,
                    'image_url' => $block['image'] ?? null,
                    'sort_order' => $index,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function stories(array $rows, array $destinations, array $interests): void
    {
        foreach ($rows as $index => $row) {
            $story = Testimonial::query()->withTrashed()->firstOrNew(['experience_title' => $row['title']]);
            $story->fill([
                'author_country' => $row['country'],
                'rating' => $row['rating'],
                'image_url' => $row['image'],
                'city' => $row['city'],
                'days' => $row['days'],
                'destination_id' => $destinations[$row['destination']]->id,
                'interest_id' => $interests[$row['interest']]->id,
                'sort_order' => $index,
                'is_active' => true,
            ]);
            $story->setTranslations('author_name', ['en' => $row['author']]);
            $story->setTranslations('body', ['en' => 'Sample story — replace with the traveller’s own words after their trip.']);
            $story->deleted_at = null;
            $story->save();
        }
    }
}
