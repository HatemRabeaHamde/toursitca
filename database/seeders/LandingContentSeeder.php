<?php

namespace Database\Seeders;

use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\Landmark;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    /**
     * Seed real, non-fabricated landing content: experience categories and
     * Moroccan destinations. image_url is left null on purpose — the
     * presenter (ExperienceLandingPageService) already falls back to vetted
     * Unsplash photography per slug/city, so we avoid inventing new URLs.
     */
    public function run(): void
    {
        $this->categories();
        $this->destinations();
        $this->landmarks();
    }

    private function categories(): void
    {
        $categories = [
            ['slug' => 'desert', 'sort_order' => 0, 'name' => [
                'en' => 'Desert',
                'fr' => 'Désert',
                'pl' => 'Pustynia',
            ]],
            ['slug' => 'mountain', 'sort_order' => 1, 'name' => [
                'en' => 'Mountains',
                'fr' => 'Montagnes',
                'pl' => 'Góry',
            ]],
            ['slug' => 'food', 'sort_order' => 2, 'name' => [
                'en' => 'Food & Culinary',
                'fr' => 'Gastronomie',
                'pl' => 'Kuchnia',
            ]],
            ['slug' => 'cultural', 'sort_order' => 3, 'name' => [
                'en' => 'Culture & Heritage',
                'fr' => 'Culture et patrimoine',
                'pl' => 'Kultura i dziedzictwo',
            ]],
            ['slug' => 'adventure', 'sort_order' => 4, 'name' => [
                'en' => 'Adventure',
                'fr' => 'Aventure',
                'pl' => 'Przygoda',
            ]],
            ['slug' => 'wellness', 'sort_order' => 5, 'name' => [
                'en' => 'Wellness & Hammam',
                'fr' => 'Bien-être et hammam',
                'pl' => 'Wellness i hammam',
            ]],
            ['slug' => 'workshop', 'sort_order' => 6, 'name' => [
                'en' => 'Workshops & Crafts',
                'fr' => 'Ateliers et artisanat',
                'pl' => 'Warsztaty i rzemiosło',
            ]],
            ['slug' => 'water', 'sort_order' => 7, 'name' => [
                'en' => 'Coast & Water',
                'fr' => 'Côte et activités nautiques',
                'pl' => 'Wybrzeże i sporty wodne',
            ]],
        ];

        foreach ($categories as $category) {
            $model = ExperienceCategory::query()->firstOrNew(['slug' => $category['slug']]);
            $model->fill([
                'image_url' => null,
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
            $model->setTranslations('name', $category['name']);
            $model->save();
        }
    }

    private function destinations(): void
    {
        $destinations = [
            ['slug' => 'marrakesh', 'city' => 'Marrakesh', 'is_featured' => true, 'sort_order' => 0, 'name' => [
                'en' => 'Marrakesh',
                'fr' => 'Marrakech',
                'pl' => 'Marrakesz',
            ]],
            ['slug' => 'fes', 'city' => 'Fes', 'is_featured' => false, 'sort_order' => 1, 'name' => [
                'en' => 'Fes',
                'fr' => 'Fès',
                'pl' => 'Fez',
            ]],
            ['slug' => 'zagora-sahara', 'city' => 'Zagora', 'is_featured' => false, 'sort_order' => 2, 'name' => [
                'en' => 'Zagora & the Sahara',
                'fr' => 'Zagora et le Sahara',
                'pl' => 'Zagora i Sahara',
            ]],
            ['slug' => 'chefchaouen', 'city' => 'Chefchaouen', 'is_featured' => true, 'sort_order' => 3, 'name' => [
                'en' => 'Chefchaouen',
                'fr' => 'Chefchaouen',
                'pl' => 'Chefchaouen',
            ]],
            ['slug' => 'essaouira', 'city' => 'Essaouira', 'is_featured' => false, 'sort_order' => 4, 'name' => [
                'en' => 'Essaouira',
                'fr' => 'Essaouira',
                'pl' => 'Essaouira',
            ]],
        ];

        foreach ($destinations as $destination) {
            $model = Destination::query()->firstOrNew(['slug' => $destination['slug']]);
            $model->fill([
                'city' => $destination['city'],
                'image_url' => null,
                'is_featured' => $destination['is_featured'],
                'sort_order' => $destination['sort_order'],
                'is_active' => true,
            ]);
            $model->setTranslations('name', $destination['name']);
            $model->save();
        }
    }

    private function landmarks(): void
    {
        $landmarks = [
            ['slug' => 'majorelle-garden', 'city' => 'Marrakesh', 'category' => 'garden', 'sort_order' => 0, 'name' => [
                'en' => 'Majorelle Garden',
                'fr' => 'Jardin Majorelle',
                'pl' => 'Ogród Majorelle',
            ]],
            ['slug' => 'bahia-palace', 'city' => 'Marrakesh', 'category' => 'palace', 'sort_order' => 1, 'name' => [
                'en' => 'Bahia Palace',
                'fr' => 'Palais de la Bahia',
                'pl' => 'Pałac Bahia',
            ]],
            ['slug' => 'yves-saint-laurent-museum', 'city' => 'Marrakesh', 'category' => 'museum', 'sort_order' => 2, 'name' => [
                'en' => 'Yves Saint Laurent Museum',
                'fr' => 'Musée Yves Saint Laurent',
                'pl' => 'Muzeum Yves Saint Laurent',
            ]],
            ['slug' => 'saadian-tombs', 'city' => 'Marrakesh', 'category' => 'historic', 'sort_order' => 3, 'name' => [
                'en' => 'Saadian Tombs',
                'fr' => 'Tombeaux Saadiens',
                'pl' => 'Grobowce Saadytów',
            ]],
            ['slug' => 'jemaa-el-fnaa', 'city' => 'Marrakesh', 'category' => 'landmark', 'sort_order' => 4, 'name' => [
                'en' => 'Jemaa el-Fnaa',
                'fr' => 'Place Jemaa el-Fna',
                'pl' => 'Plac Dżamaa al-Fna',
            ]],
        ];

        foreach ($landmarks as $landmark) {
            $model = Landmark::query()->firstOrNew(['slug' => $landmark['slug']]);
            $model->fill([
                'city' => $landmark['city'],
                'image_url' => null,
                'category' => $landmark['category'],
                'sort_order' => $landmark['sort_order'],
                'is_active' => true,
            ]);
            $model->setTranslations('name', $landmark['name']);
            $model->save();
        }
    }
}
