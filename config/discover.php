<?php

return [
    /*
    | Editorial images of the Discover hub (paths under public/). Guide pages
    | take their images from the database (destinations, interests, blocks).
    */
    'images' => [
        'hero' => 'images/discover/heroes/discover.jpg',
        'hosts_hero' => 'images/discover/landmarks/jemaa-el-fnaa.jpg',
        'routes' => [
            'destination' => 'images/discover/interests/adventure.jpg',
            'interest' => 'images/discover/dishes/tagine.jpg',
        ],
    ],

    /*
    | Dashed lines on the Discover map, as pairs of destination slugs.
    | A line is drawn only when both destinations have a pin (map_x/map_y).
    */
    'map' => [
        'routes' => [
            ['marrakesh', 'fes'],
            ['fes', 'chefchaouen'],
            ['marrakesh', 'essaouira'],
            ['marrakesh', 'high-atlas'],
            ['high-atlas', 'zagora-sahara'],
        ],
    ],

    /* Local hosts directory. */
    'hosts' => [
        'per_page' => 12,
    ],
];
