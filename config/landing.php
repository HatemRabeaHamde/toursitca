<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Landing Page Demo Mode
    |--------------------------------------------------------------------------
    |
    | Demo content is only allowed in non-production environments. Production
    | must show real backend data or localized empty states.
    |
    */
    'demo_environments' => ['local', 'testing'],

    'section_limits' => [
        'cards' => 6,
        'deals' => 6,
        'categories' => 8,
        'destinations' => 5,
        'agencies' => 4,
    ],

    'images' => [
        'hero' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1800&q=90',
        'card_fallback' => 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=700&q=80',
        'process' => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=900&q=80',
        'process_steps' => [
            'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=85',
            'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=800&q=85',
            'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&q=85',
        ],
        'process_avatar' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=100&q=80',
        'reel_fallback' => 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=400&q=80',
        'demo_cards' => [
            'marrakech_food' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=900&q=80',
            'sahara_camp' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=900&q=80',
            'fes_workshop' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=900&q=80',
            'chefchaouen_photo' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=900&q=80',
            'essaouira_coast' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=900&q=80',
            'atlas_hike' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=900&q=80',
        ],
        'demo_reels' => [
            'medina' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=500&q=80',
            'desert' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=500&q=80',
            'blue_city' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=500&q=80',
            'coast' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=500&q=80',
        ],
        'categories' => [
            'balloon' => 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e?w=300&q=80',
            'food' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=300&q=80',
            'cultural' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=80',
            'culture' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=80',
            'mountain' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=300&q=80',
            'adventure' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=300&q=80',
            'workshop' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=300&q=80',
            'wellness' => 'https://images.unsplash.com/photo-1560180474-e8563fd75bab?w=300&q=80',
            'photography' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?w=300&q=80',
            'water' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=300&q=80',
            'default' => 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?w=300&q=80',
        ],
        'destinations' => [
            'fes' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=900&q=80',
            'sahara' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=900&q=80',
            'zagora' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=900&q=80',
            'chefchaouen' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=900&q=80',
            'essaouira' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=900&q=80',
            'default' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=900&q=80',
        ],
    ],
];
