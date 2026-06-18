<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Experience\Actions\SaveExperienceOptionAction;
use App\Domain\Experience\Models\Availability;
use App\Domain\Experience\Models\Experience;
use App\Domain\Experience\Models\ExperienceMedia;
use App\Domain\Landing\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LandingShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $agency = $this->platformAgency();

        $this->experiences($agency);
        $this->testimonials();
    }

    private function platformAgency(): Agency
    {
        $owner = User::query()->firstOrCreate(
            ['email' => env('PLATFORM_AGENCY_EMAIL', 'platform@morocco-tourism.test')],
            [
                'name' => env('PLATFORM_AGENCY_OWNER_NAME', 'Platform Experiences'),
                'password' => Hash::make(Str::password(16)),
                'preferred_lang' => config('locales.default', 'en'),
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        return Agency::query()->updateOrCreate(
            ['slug' => 'platform'],
            [
                'user_id' => $owner->id,
                'name' => 'Platform',
                'description' => [
                    'en' => 'Platform-owned experiences.',
                    'fr' => 'Expériences gérées par la plateforme.',
                    'pl' => 'Doświadczenia prowadzone przez platformę.',
                ],
                'commission_rate' => '0.00',
                'status' => 'active',
                'city' => 'Marrakesh',
                'phone' => null,
                'languages' => ['en', 'fr', 'pl'],
                'is_platform' => true,
            ],
        );
    }

    private function experiences(Agency $agency): void
    {
        $items = [
            [
                'slug' => 'showcase-marrakech-balloon-ride',
                'title' => ['en' => 'Showcase Marrakech balloon ride', 'fr' => 'Vol en montgolfière showcase à Marrakech', 'pl' => 'Pokazowy lot balonem w Marrakeszu'],
                'description' => [
                    'en' => 'Sample detail-page listing for local UI review. It uses demo copy and public imagery so the gallery, booking card, trust blocks, and trip information can be reviewed before verified supplier content is available.',
                    'fr' => 'Fiche exemple pour vérifier localement la page détail. Elle utilise un contenu démo et des images publiques afin de contrôler la galerie, la carte de réservation, les blocs de confiance et les informations du voyage avant le contenu fournisseur vérifié.',
                    'pl' => 'Przykładowa oferta do lokalnego przeglądu strony szczegółów. Używa treści demo i publicznych obrazów, aby sprawdzić galerię, kartę rezerwacji, bloki zaufania oraz informacje o wycieczce przed dodaniem zweryfikowanych treści dostawcy.',
                ],
                'category' => 'adventure',
                'location_city' => 'Marrakesh',
                'duration_hours' => '4.0',
                'price_per_person' => '120.00',
                'original_price' => '150.00',
                'deal_ends_at' => now()->addDays(5)->endOfDay(),
                'private_price' => '620.00',
                'max_group_size' => 10,
                'inclusions' => [
                    'en' => ['Demo hotel pickup coordination', 'Sample Moroccan breakfast line', 'Sample flight certificate', 'Shared transport for local preview'],
                    'fr' => ['Coordination démo de prise en charge hôtel', 'Ligne exemple pour le petit-déjeuner marocain', 'Certificat de vol exemple', 'Transport partagé pour aperçu local'],
                    'pl' => ['Demo koordynacji odbioru z hotelu', 'Przykładowa pozycja marokańskiego śniadania', 'Przykładowy certyfikat lotu', 'Wspólny transport do lokalnego podglądu'],
                ],
                'exclusions' => [
                    'en' => ['Personal expenses', 'Tips', 'Anything not listed in inclusions'],
                    'fr' => ['Dépenses personnelles', 'Pourboires', 'Tout élément non listé dans les inclusions'],
                    'pl' => ['Wydatki osobiste', 'Napiwki', 'Wszystko, czego nie ma na liście w cenie'],
                ],
                'media' => [
                    'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=1600&q=85&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1400&q=85&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1400&q=85&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1548013146-72479768bada?w=1400&q=85&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => true,
                'sort_offset' => 0,
            ],
            [
                'slug' => 'showcase-marrakesh-food-walk',
                'title' => ['en' => 'Marrakesh food walk', 'fr' => 'Balade gourmande à Marrakech', 'pl' => 'Spacer kulinarny po Marrakeszu'],
                'description' => ['en' => 'A sample marketplace listing for browsing food experiences in Marrakesh.', 'fr' => 'Une fiche exemple pour parcourir les expériences culinaires à Marrakech.', 'pl' => 'Przykładowa oferta do przeglądania doświadczeń kulinarnych w Marrakeszu.'],
                'category' => 'food',
                'location_city' => 'Marrakesh',
                'duration_hours' => '3.0',
                'price_per_person' => '38.00',
                'original_price' => '52.00',
                'deal_ends_at' => now()->addDays(6)->endOfDay(),
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1548013146-72479768bada?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => true,
                'sort_offset' => 1,
            ],
            [
                'slug' => 'showcase-sahara-desert-camp',
                'title' => ['en' => 'Sahara desert camp', 'fr' => 'Camp dans le désert du Sahara', 'pl' => 'Obóz na Saharze'],
                'description' => ['en' => 'A sample marketplace listing for browsing desert experiences.', 'fr' => 'Une fiche exemple pour parcourir les expériences dans le désert.', 'pl' => 'Przykładowa oferta do przeglądania doświadczeń pustynnych.'],
                'category' => 'desert',
                'location_city' => 'Zagora',
                'duration_hours' => '8.0',
                'price_per_person' => '95.00',
                'original_price' => '125.00',
                'deal_ends_at' => now()->addDays(3)->endOfDay(),
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => true,
                'sort_offset' => 2,
            ],
            [
                'slug' => 'showcase-fes-craft-workshop',
                'title' => ['en' => 'Fes craft workshop', 'fr' => 'Atelier artisanal à Fès', 'pl' => 'Warsztat rzemiosła w Fezie'],
                'description' => ['en' => 'A sample marketplace listing for browsing craft workshops in Fes.', 'fr' => 'Une fiche exemple pour parcourir les ateliers artisanaux à Fès.', 'pl' => 'Przykładowa oferta do przeglądania warsztatów rzemiosła w Fezie.'],
                'category' => 'workshop',
                'location_city' => 'Fes',
                'duration_hours' => '2.5',
                'price_per_person' => '44.00',
                'original_price' => null,
                'deal_ends_at' => null,
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => false,
                'sort_offset' => 3,
            ],
            [
                'slug' => 'showcase-chefchaouen-photo-walk',
                'title' => ['en' => 'Chefchaouen photo walk', 'fr' => 'Balade photo à Chefchaouen', 'pl' => 'Spacer fotograficzny po Chefchaouen'],
                'description' => ['en' => 'A sample marketplace listing for browsing photography walks in Chefchaouen.', 'fr' => 'Une fiche exemple pour parcourir les balades photo à Chefchaouen.', 'pl' => 'Przykładowa oferta do przeglądania spacerów fotograficznych w Chefchaouen.'],
                'category' => 'photography',
                'location_city' => 'Chefchaouen',
                'duration_hours' => '2.0',
                'price_per_person' => '34.00',
                'original_price' => null,
                'deal_ends_at' => null,
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => false,
                'sort_offset' => 4,
            ],
            [
                'slug' => 'showcase-essaouira-coast-day',
                'title' => ['en' => 'Essaouira coast day', 'fr' => 'Journée côte à Essaouira', 'pl' => 'Dzień na wybrzeżu w Essaouirze'],
                'description' => ['en' => 'A sample marketplace listing for browsing coastal experiences in Essaouira.', 'fr' => 'Une fiche exemple pour parcourir les expériences côtières à Essaouira.', 'pl' => 'Przykładowa oferta do przeglądania doświadczeń nadmorskich w Essaouirze.'],
                'category' => 'water',
                'location_city' => 'Essaouira',
                'duration_hours' => '5.0',
                'price_per_person' => '58.00',
                'original_price' => '70.00',
                'deal_ends_at' => now()->addDays(10)->endOfDay(),
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => false,
                'sort_offset' => 5,
            ],
            [
                'slug' => 'showcase-atlas-mountain-hike',
                'title' => ['en' => 'Atlas mountain hike', 'fr' => 'Randonnée dans l’Atlas', 'pl' => 'Wędrówka w Atlasie'],
                'description' => ['en' => 'A sample marketplace listing for browsing mountain experiences.', 'fr' => 'Une fiche exemple pour parcourir les expériences en montagne.', 'pl' => 'Przykładowa oferta do przeglądania doświadczeń górskich.'],
                'category' => 'mountain',
                'location_city' => 'Marrakesh',
                'duration_hours' => '6.0',
                'price_per_person' => '64.00',
                'original_price' => null,
                'deal_ends_at' => null,
                'private_price' => null,
                'max_group_size' => 8,
                'inclusions' => null,
                'exclusions' => null,
                'media' => [
                    'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=1400&q=85&auto=format&fit=crop',
                ],
                'is_top_rated' => false,
                'sort_offset' => 6,
            ],
        ];

        foreach ($items as $item) {
            $experience = Experience::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'agency_id' => $agency->id,
                    'created_by' => $agency->user_id,
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'category' => $item['category'],
                    'difficulty' => 'easy',
                    'duration_hours' => $item['duration_hours'],
                    'max_group_size' => $item['max_group_size'],
                    'price_per_person' => $item['price_per_person'],
                    'original_price' => $item['original_price'],
                    'deal_starts_at' => $item['deal_ends_at'] ? now()->subDay() : null,
                    'deal_ends_at' => $item['deal_ends_at'],
                    'private_price' => $item['private_price'],
                    'pickup_enabled' => true,
                    'location_city' => $item['location_city'],
                    'meeting_point' => null,
                    'inclusions' => $item['inclusions'],
                    'exclusions' => $item['exclusions'],
                    'status' => 'published',
                    'rating_avg' => '0.00',
                    'reviews_count' => 0,
                    'is_top_rated' => $item['is_top_rated'],
                    'created_at' => now()->subDays($item['sort_offset']),
                    'updated_at' => now()->subDays($item['sort_offset']),
                ],
            );

            $this->media($experience, $item['media']);

            Availability::query()->updateOrCreate(
                [
                    'experience_id' => $experience->id,
                    'date' => now()->addDays(7 + $item['sort_offset'])->toDateString(),
                    'time_slot' => '09:00:00',
                ],
                [
                    'max_seats' => 8,
                    'booked_seats' => 0,
                    'held_seats' => 0,
                    'is_active' => true,
                ],
            );

            if ($item['slug'] === 'showcase-sahara-desert-camp') {
                $this->desertCampOptions($experience);
            }
        }
    }

    private function desertCampOptions(Experience $experience): void
    {
        $action = new SaveExperienceOptionAction;

        $options = [
            [
                'attributes' => [
                    'title' => ['en' => 'Standard Desert Camp', 'fr' => 'Camp Désertique Standard'],
                    'duration_minutes' => 1440,
                    'pickup_enabled' => true,
                    'private_available' => false,
                    'pay_later_enabled' => true,
                    'cancellation_hours' => 24,
                    'price_type' => 'per_person',
                    'status' => 'active',
                ],
                'prices' => [
                    ['participant_type' => 'adult', 'min_age' => 13, 'max_age' => null, 'price' => '95.00', 'original_price' => null, 'currency' => 'MAD'],
                    ['participant_type' => 'child', 'min_age' => 3, 'max_age' => 12, 'price' => '70.00', 'original_price' => null, 'currency' => 'MAD'],
                ],
                'languages' => [
                    ['language_code' => 'en', 'type' => 'live_guide'],
                ],
            ],
            [
                'attributes' => [
                    'title' => ['en' => 'Luxury Desert Camp (Highly Recommended)', 'fr' => 'Camp Désertique de Luxe (Recommandé)'],
                    'duration_minutes' => 1440,
                    'pickup_enabled' => true,
                    'private_available' => true,
                    'pay_later_enabled' => true,
                    'cancellation_hours' => 24,
                    'price_type' => 'per_person',
                    'status' => 'active',
                ],
                'prices' => [
                    ['participant_type' => 'adult', 'min_age' => 13, 'max_age' => null, 'price' => '165.00', 'original_price' => '185.00', 'currency' => 'MAD'],
                    ['participant_type' => 'child', 'min_age' => 3, 'max_age' => 12, 'price' => '130.00', 'original_price' => '145.00', 'currency' => 'MAD'],
                ],
                'languages' => [
                    ['language_code' => 'en', 'type' => 'live_guide'],
                    ['language_code' => 'fr', 'type' => 'live_guide'],
                ],
            ],
            [
                'attributes' => [
                    'title' => ['en' => 'Private 3-Day Desert Adventure (Premium)', 'fr' => 'Aventure Privée de 3 Jours dans le Désert (Premium)'],
                    'duration_minutes' => 4320,
                    'pickup_enabled' => true,
                    'private_available' => true,
                    'pay_later_enabled' => true,
                    'cancellation_hours' => 48,
                    'price_type' => 'per_group',
                    'status' => 'active',
                ],
                'prices' => [
                    ['participant_type' => 'group', 'min_age' => null, 'max_age' => null, 'price' => '420.00', 'original_price' => null, 'currency' => 'MAD'],
                ],
                'languages' => [
                    ['language_code' => 'en', 'type' => 'live_guide'],
                    ['language_code' => 'fr', 'type' => 'live_guide'],
                ],
            ],
        ];

        foreach ($options as $option) {
            $existing = $experience->options()
                ->whereJsonContains('title->en', $option['attributes']['title']['en'])
                ->first();

            $action->execute($experience, $option['attributes'], $option['prices'], $option['languages'], $existing);
        }
    }

    /**
     * @param  array<int, string>  $urls
     */
    private function media(Experience $experience, array $urls): void
    {
        foreach ($urls as $index => $url) {
            ExperienceMedia::query()->updateOrCreate(
                [
                    'experience_id' => $experience->id,
                    'path' => $url,
                ],
                [
                    'type' => 'image',
                    'source_type' => 'url',
                    'sort_order' => $index,
                ],
            );
        }
    }

    private function testimonials(): void
    {
        $items = [
            [
                'author_name' => ['en' => 'Demo traveler', 'fr' => 'Voyageur démo', 'pl' => 'Podróżnik demo'],
                'author_country' => 'Sample content',
                'body' => [
                    'en' => 'Sample story content for local development. Replace it with verified traveler feedback before production.',
                    'fr' => 'Contenu d’histoire exemple pour le développement local. Remplacez-le par un avis voyageur vérifié avant la production.',
                    'pl' => 'Przykładowa historia do lokalnego rozwoju. Przed produkcją zastąp ją zweryfikowaną opinią podróżnika.',
                ],
                'experience_title' => 'Development seed',
                'rating' => '5.00',
                'sort_order' => 0,
            ],
            [
                'author_name' => ['en' => 'Sample guest', 'fr' => 'Invité exemple', 'pl' => 'Przykładowy gość'],
                'author_country' => 'Sample content',
                'body' => [
                    'en' => 'Use this card to review spacing, typography, and the testimonial layout while real reviews are not available yet.',
                    'fr' => 'Utilisez cette carte pour vérifier l’espacement, la typographie et la mise en page tant que les vrais avis ne sont pas encore disponibles.',
                    'pl' => 'Użyj tej karty do sprawdzenia odstępów, typografii i układu, zanim pojawią się prawdziwe opinie.',
                ],
                'experience_title' => 'Development seed',
                'rating' => '5.00',
                'sort_order' => 1,
            ],
        ];

        foreach ($items as $item) {
            $testimonial = Testimonial::query()->firstOrNew([
                'experience_title' => $item['experience_title'],
                'sort_order' => $item['sort_order'],
            ]);
            $testimonial->fill([
                'author_country' => $item['author_country'],
                'rating' => $item['rating'],
                'avatar_url' => null,
                'sort_order' => $item['sort_order'],
                'is_active' => true,
            ]);
            $testimonial->setTranslations('author_name', $item['author_name']);
            $testimonial->setTranslations('body', $item['body']);
            $testimonial->save();
        }
    }
}
