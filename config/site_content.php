<?php

use Illuminate\Support\Arr;

// Fixed presentation contracts: only these content fields may be changed.
$translations = require __DIR__.'/../lang/en/landing_v2.php';
$sections = [];
foreach (['hero', 'soul', 'discover', 'featured', 'people', 'stories', 'plan', 'know'] as $section) {
    $flatten = Arr::dot($translations[$section]);
    $fields = [];
    foreach ($flatten as $key => $default) {
        if ($key === 'image_alt') {
            continue;
        }
        if (! is_string($default)) {
            continue;
        }
        $fields[$key] = ['label' => match ($key) {
            'eyebrow' => 'Small heading above the title', 'title_line_1' => 'Main heading — first line', 'title_line_2' => 'Main heading — second line', 'title_accent' => 'Highlighted heading text', 'title_before' => 'Heading before highlighted text', 'title_after' => 'Heading after highlighted text', 'cta' => 'Button label', 'link' => 'Link label', default => ucfirst(preg_replace_callback('/\d+/', fn ($m) => (string) ((int) $m[0] + 1), str_replace(['_', '.'], ' ', $key)))
        }, 'type' => str_ends_with($key, 'description') ? 'richtext' : 'text', 'max' => str_ends_with($key, 'description') ? 5000 : 1000,
            'hint' => 'Leave a translation empty to use English or the original site text.', 'lang' => 'landing_v2.'.$section.'.'.$key];
    }
    $sections[$section] = ['label' => ucfirst(str_replace('_', ' ', $section)), 'fields' => $fields];
}
foreach (['hero' => ['image' => 'hero'], 'soul' => ['people_image' => 'soul_people', 'places_image' => 'soul_places', 'moments_image' => 'soul_moments', 'decoration' => 'leaves'], 'know' => ['image' => 'know_before']] as $section => $images) {
    foreach ($images as $key => $source) {
        $sections[$section]['fields'][$key] = ['label' => ucfirst(str_replace('_', ' ', $key)), 'type' => 'image', 'max' => 5120, 'hint' => 'JPEG, PNG or WebP. Maximum 5 MB.', 'config' => 'landing_v2.images.'.$source, 'alt_lang' => ($section === 'hero' || $section === 'know') ? 'landing_v2.'.$section.'.image_alt' : null];
    }
}
foreach ([0, 1, 2] as $index) {
    $sections['stories']['fields']['image_'.$index] = ['label' => 'Story cover '.($index + 1), 'type' => 'image', 'max' => 5120, 'hint' => 'Static fallback cover. JPEG, PNG or WebP, maximum 5 MB.', 'config' => 'landing_v2.images.stories.'.$index];
}
$sections['hero']['fields']['film_url'] = ['label' => 'Film destination', 'type' => 'link', 'max' => 2048, 'hint' => 'Use an https URL, a site path or an anchor.', 'config' => 'landing_v2.film_url'];
$sections['seo'] = ['label' => 'Search engines', 'fields' => [
    'title' => ['label' => 'SEO title', 'type' => 'text', 'max' => 160, 'hint' => 'Describe this homepage accurately.', 'config' => 'seo.default_title'],
    'description' => ['label' => 'Meta description', 'type' => 'textarea', 'max' => 320, 'hint' => 'A short, accurate summary for search results.', 'config' => 'seo.default_description'],
]];

return ['upload_max_kb' => 5120, 'pages' => ['home' => ['label' => 'Home', 'view' => 'site.home-v2', 'route' => 'site.home', 'sections' => $sections]]];
