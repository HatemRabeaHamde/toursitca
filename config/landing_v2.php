<?php

/*
|--------------------------------------------------------------------------
| Landing v2 (Tourstica editorial homepage)
|--------------------------------------------------------------------------
|
| Static design assets exported from Figma (file ZEE8i243q791MhTAcMbqkv,
| frame 222:4489). Every path is relative to /public. Dynamic imagery
| (categories, experiences, agencies) comes from the database instead.
|
*/

$dir = 'images/landing-v2/';

return [
    'limits' => [
        'hero_cities' => 5,
        'interests' => 6,
        'featured' => 5,
        'stories' => 3,
        'faqs' => 4,
    ],

    'hero_slide_ms' => 6000,

    /*
    | Hero "Watch the film" target. Falls back to the #story section.
    */
    'film_url' => env('LANDING_FILM_URL', '#story'),

    'images' => [
        'hero' => $dir.'hero-marrakech.jpg',
        'leaves' => $dir.'leaves.png',
        'soul_people' => $dir.'soul-people.jpg',
        'soul_places' => $dir.'soul-places.jpg',
        'soul_moments' => $dir.'soul-moments.jpg',
        'stories' => [
            $dir.'story-1.jpg',
            $dir.'story-2.jpg',
            $dir.'story-3.jpg',
        ],
        'know_before' => $dir.'know-essaouira.jpg',
        'footer_pattern' => $dir.'footer-pattern.png',
    ],

    'icons' => [
        'logo_light' => $dir.'logo-light.svg',
        'logo_footer' => $dir.'logo-footer.svg',
        'arrow_white' => $dir.'icon-arrow-white.svg',
        'arrow_dark' => $dir.'icon-arrow-dark.svg',
        'play' => $dir.'icon-play.svg',
        'arrow_clay' => $dir.'icon-arrow-clay.svg',
        'arrow_clay_sm' => $dir.'icon-arrow-clay-sm.svg',
        'arrow_link' => $dir.'icon-arrow-link.svg',
        'shield_light' => $dir.'icon-shield-light.svg',
        'star_lg' => $dir.'icon-star-lg.svg',
        'star_sm' => $dir.'icon-star-sm.svg',
        'star_gold' => $dir.'icon-star-gold.svg',
        'check_light' => $dir.'icon-check-light.svg',
        'faq_open' => $dir.'icon-faq-open.svg',
        'faq_plus' => $dir.'icon-faq-plus.svg',
        'send' => $dir.'icon-send.svg',
        'trust' => [
            'verified' => $dir.'icon-trust-verified.svg',
            'licensed' => $dir.'icon-trust-licensed.svg',
            'local' => $dir.'icon-trust-local.svg',
            'rated' => $dir.'icon-trust-rated.svg',
        ],
        'steps' => [
            'discover' => $dir.'icon-step-discover.svg',
            'choose' => $dir.'icon-step-choose.svg',
            'experience' => $dir.'icon-step-experience.svg',
            'share' => $dir.'icon-step-share.svg',
        ],
        'social' => [
            'instagram' => $dir.'icon-instagram.svg',
            'facebook' => $dir.'icon-facebook.svg',
            'youtube' => $dir.'icon-youtube.svg',
        ],
    ],

    /*
    | Social profiles — '#' until the real URLs are set in .env.
    */
    'social_urls' => [
        'instagram' => env('SOCIAL_INSTAGRAM_URL', '#'),
        'facebook' => env('SOCIAL_FACEBOOK_URL', '#'),
        'youtube' => env('SOCIAL_YOUTUBE_URL', '#'),
    ],
];
