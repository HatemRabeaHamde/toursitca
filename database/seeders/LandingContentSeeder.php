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
                'nl' => 'Desert',
            ]],
            ['slug' => 'mountain', 'sort_order' => 1, 'name' => [
                'en' => 'Mountains',
                'fr' => 'Montagnes',
                'nl' => 'Mountains',
            ]],
            ['slug' => 'food', 'sort_order' => 2, 'name' => [
                'en' => 'Food & Culinary',
                'fr' => 'Gastronomie',
                'nl' => 'Food & Culinary',
            ]],
            ['slug' => 'cultural', 'sort_order' => 3, 'name' => [
                'en' => 'Culture & Heritage',
                'fr' => 'Culture et patrimoine',
                'nl' => 'Culture & Heritage',
            ]],
            ['slug' => 'adventure', 'sort_order' => 4, 'name' => [
                'en' => 'Adventure',
                'fr' => 'Aventure',
                'nl' => 'Adventure',
            ]],
            ['slug' => 'wellness', 'sort_order' => 5, 'name' => [
                'en' => 'Wellness & Hammam',
                'fr' => 'Bien-être et hammam',
                'nl' => 'Wellness & Hammam',
            ]],
            ['slug' => 'workshop', 'sort_order' => 6, 'name' => [
                'en' => 'Workshops & Crafts',
                'fr' => 'Ateliers et artisanat',
                'nl' => 'Workshops & Crafts',
            ]],
            ['slug' => 'water', 'sort_order' => 7, 'name' => [
                'en' => 'Coast & Water',
                'fr' => 'Côte et activités nautiques',
                'nl' => 'Coast & Water',
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
                'nl' => 'Marrakesh',
            ]],
            ['slug' => 'fes', 'city' => 'Fes', 'is_featured' => false, 'sort_order' => 1, 'name' => [
                'en' => 'Fes',
                'fr' => 'Fès',
                'nl' => 'Fes',
            ]],
            ['slug' => 'zagora-sahara', 'city' => 'Zagora', 'is_featured' => false, 'sort_order' => 2, 'name' => [
                'en' => 'Zagora & the Sahara',
                'fr' => 'Zagora et le Sahara',
                'nl' => 'Zagora & the Sahara',
            ]],
            ['slug' => 'chefchaouen', 'city' => 'Chefchaouen', 'is_featured' => true, 'sort_order' => 3, 'name' => [
                'en' => 'Chefchaouen',
                'fr' => 'Chefchaouen',
                'nl' => 'Chefchaouen',
            ]],
            ['slug' => 'essaouira', 'city' => 'Essaouira', 'is_featured' => false, 'sort_order' => 4, 'name' => [
                'en' => 'Essaouira',
                'fr' => 'Essaouira',
                'nl' => 'Essaouira',
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
                'nl' => 'Majorelle Garden',
            ]],
            ['slug' => 'bahia-palace', 'city' => 'Marrakesh', 'category' => 'palace', 'sort_order' => 1, 'name' => [
                'en' => 'Bahia Palace',
                'fr' => 'Palais de la Bahia',
                'nl' => 'Bahia Palace',
            ]],
            ['slug' => 'yves-saint-laurent-museum', 'city' => 'Marrakesh', 'category' => 'museum', 'sort_order' => 2, 'name' => [
                'en' => 'Yves Saint Laurent Museum',
                'fr' => 'Musée Yves Saint Laurent',
                'nl' => 'Yves Saint Laurent Museum',
            ]],
            ['slug' => 'saadian-tombs', 'city' => 'Marrakesh', 'category' => 'historic', 'sort_order' => 3, 'name' => [
                'en' => 'Saadian Tombs',
                'fr' => 'Tombeaux Saadiens',
                'nl' => 'Saadian Tombs',
            ]],
            ['slug' => 'jemaa-el-fnaa', 'city' => 'Marrakesh', 'category' => 'landmark', 'sort_order' => 4, 'name' => [
                'en' => 'Jemaa el-Fnaa',
                'fr' => 'Place Jemaa el-Fna',
                'nl' => 'Jemaa el-Fnaa',
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
