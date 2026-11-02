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
// Shared chrome is editable here for the landing page only.
$chrome = require __DIR__.'/../lang/en/site.php';
foreach (['nav' => 'Header', 'footer' => 'Footer', 'language' => 'Language selector'] as $section => $label) {
    $sections[$section] = ['label' => $label, 'fields' => []];
    foreach (Arr::dot($chrome[$section]) as $key => $value) {
        if (is_string($value)) {
            $sections[$section]['fields'][$key] = ['label' => ucfirst(str_replace(['_', '.'], ' ', $key)), 'type' => 'text', 'max' => 1000, 'hint' => 'Landing-page wording. Keep :year, :brand and other placeholders when present.', 'lang' => 'site.'.$section.'.'.$key];
        }
    }
}
for ($slide = 2; $slide <= 5; $slide++) {
    $sections['hero']['fields']['slide_'.$slide.'_image'] = ['label' => 'Slide '.$slide.' image', 'type' => 'image', 'max' => 5120, 'hint' => 'Landing only. Uses the destination photo until you upload an override. JPEG, PNG or WebP, maximum 5 MB.', 'default' => ''];
}
for ($slide = 1; $slide <= 5; $slide++) {
    foreach (['label' => 'text', 'url' => 'link'] as $key => $type) {
        $sections['hero']['fields']['slide_'.$slide.'_'.$key] = ['label' => 'Slide '.$slide.' '.($key === 'label' ? 'destination label' : 'destination link'), 'type' => $type, 'max' => $key === 'label' ? 160 : 2048, 'hint' => 'Optional landing-only override. Leave empty for the destination default.', 'default' => ''];
    }
}
foreach (['hero', 'soul', 'discover', 'people', 'know'] as $section) {
    $sections[$section]['fields']['link_url'] = ['label' => 'Main link destination', 'type' => 'link', 'max' => 2048, 'hint' => 'Optional. Leave empty to keep the existing site destination.', 'default' => ''];
}
foreach (Arr::dot((require __DIR__.'/landing_v2.php')['icons']) as $key => $source) {
    $sections['artwork']['label'] = 'Icons and logos';
    $sections['artwork']['fields'][str_replace('.', '_', $key)] = ['label' => ucfirst(str_replace(['_', '.'], ' ', $key)), 'type' => 'image', 'max' => 5120, 'hint' => 'Static artwork. Existing SVG defaults are preserved; replacement uploads must be JPEG, PNG or WebP, maximum 5 MB.', 'config' => 'landing_v2.icons.'.$key];
}
foreach (['globe' => 'images/site/icons/globe.svg', 'chevron' => 'images/site/icons/chevron-down-light.svg'] as $key => $path) {
    $sections['artwork']['fields'][$key] = ['label' => ucfirst($key).' icon', 'type' => 'image', 'max' => 5120, 'hint' => 'Decorative language selector icon. Raster replacements only, maximum 5 MB.', 'default' => $path];
}
$sections['footer']['fields']['pattern'] = ['label' => 'Footer background artwork', 'type' => 'image', 'max' => 5120, 'hint' => 'Decorative footer image. JPEG, PNG or WebP, maximum 5 MB.', 'config' => 'landing_v2.images.footer_pattern'];
foreach (['instagram', 'facebook', 'youtube'] as $network) {
    $sections['footer']['fields']['social_'.$network] = ['label' => ucfirst($network).' link', 'type' => 'link', 'max' => 2048, 'hint' => 'Optional. Overrides the footer profile link on the landing page.', 'default' => ''];
}
foreach (['about', 'careers', 'contact', 'destinations', 'experiences', 'guides', 'faqs', 'privacy', 'terms'] as $key) {
    $sections['footer']['fields']['link_'.$key] = ['label' => ucfirst($key).' link destination', 'type' => 'link', 'max' => 2048, 'hint' => 'Optional. Leave empty for the current destination.', 'default' => ''];
}
foreach (['discover', 'experiences', 'hosts', 'how', 'contact'] as $key) {
    $sections['nav']['fields']['link_'.$key] = ['label' => ucfirst($key).' link destination', 'type' => 'link', 'max' => 2048, 'hint' => 'Optional. Leave empty for the current destination.', 'default' => ''];
}
$sections['seo'] = ['label' => 'Search engines', 'fields' => [
    'title' => ['label' => 'SEO title', 'type' => 'text', 'max' => 160, 'hint' => 'Describe this homepage accurately.', 'config' => 'seo.default_title'],
    'description' => ['label' => 'Meta description', 'type' => 'textarea', 'max' => 320, 'hint' => 'A short, accurate summary for search results.', 'config' => 'seo.default_description'],
]];

$sections['seo']['fields'] += [
    'robots' => ['label' => 'Search visibility', 'type' => 'text', 'max' => 32, 'allowed' => ['index, follow', 'noindex, follow', 'noindex, nofollow'], 'hint' => 'Use index, follow for a public homepage. noindex removes it from search.', 'default' => 'index, follow'],
    'social_title' => ['label' => 'Social sharing title', 'type' => 'text', 'max' => 160, 'hint' => 'Optional. Uses the SEO title when empty.', 'default' => ''],
    'social_description' => ['label' => 'Social sharing description', 'type' => 'textarea', 'max' => 320, 'hint' => 'Optional. Uses the meta description when empty.', 'default' => ''],
    'social_image' => ['label' => 'Social sharing image', 'type' => 'image', 'max' => 5120, 'hint' => 'JPEG, PNG or WebP, up to 5 MB. Add an image description for each language.', 'config' => 'seo.default_image'],
];
$sections['geo'] = ['label' => 'Publisher identity (GEO)', 'fields' => [
    'name' => ['label' => 'Publisher name', 'type' => 'text', 'max' => 160, 'hint' => 'The real organization operating this marketplace.', 'config' => 'seo.organization.name'],
    'description' => ['label' => 'About the publisher', 'type' => 'textarea', 'max' => 1000, 'hint' => 'Accurate description of the organization. No unsupported credentials or claims.', 'default' => ''],
    'url' => ['label' => 'Official website', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'The official https website for the publisher.', 'config' => 'seo.organization.url'],
    'logo' => ['label' => 'Publisher logo', 'type' => 'image', 'max' => 5120, 'hint' => 'JPEG, PNG or WebP, up to 5 MB.', 'config' => 'seo.organization.logo'],
]];
foreach (['instagram', 'facebook', 'linkedin'] as $network) {
    $sections['geo']['fields'][$network] = ['label' => ucfirst($network).' official profile', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'Optional. Only an official profile belonging to this organization.', 'default' => ''];
}
$sections['aeo'] = ['label' => 'Clear answers (AEO)', 'fields' => [
    'summary' => ['label' => 'Short answer: what does Tourstica offer?', 'type' => 'textarea', 'max' => 600, 'hint' => 'A concise factual answer, shown publicly above the homepage FAQs. Leave empty to hide. Existing FAQ questions and answers stay in the FAQs editor.', 'default' => ''],
]];

$settings = require __DIR__.'/site_content_settings.php';
$settings['panels']['search']['groups'] = ['basics' => ['seo.title', 'seo.description', 'seo.robots'], 'sharing' => ['seo.social_title', 'seo.social_description', 'seo.social_image', 'seo.twitter_card'], 'advanced' => ['seo.max_snippet', 'seo.max_image_preview', 'seo.max_video_preview', 'seo.google_verification']];
$settings['panels']['publisher']['groups'] = ['identity' => ['geo.name', 'geo.legal_name', 'geo.alternate_name', 'geo.description', 'geo.url', 'geo.logo', 'geo.about_url'], 'profiles' => ['geo.email', 'geo.phone', 'geo.instagram', 'geo.facebook', 'geo.linkedin', 'geo.youtube']];
$settings['panels']['answers']['groups'] = ['summary_group' => ['aeo.question', 'aeo.summary'], 'evidence' => ['aeo.reviewer', 'aeo.reviewer_type', 'aeo.reviewed_on', 'aeo.source_1_label', 'aeo.source_1_url', 'aeo.source_2_label', 'aeo.source_2_url']];
foreach ($settings['fields'] as $section => $fields) {
    foreach ($fields as $key => $definition) {
        $sections[$section]['fields'][$key] = array_replace($sections[$section]['fields'][$key] ?? [], $definition);
    }
}

return ['upload_max_kb' => 5120, 'panels' => $settings['panels'], 'pages' => ['home' => ['label' => 'Home', 'view' => 'site.home-v2', 'route' => 'site.home', 'sections' => $sections]]];
