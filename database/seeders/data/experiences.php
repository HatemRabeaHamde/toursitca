<?php

/*
| Demo marketplace content (local / staging only) — the nine experiences of the
| Figma "07 · Experiences — Marketplace v2" screen, led by the Discover hosts,
| plus booking-linked demo reviews. Images live in public/images/experiences.
| Prices are in the platform currency (config payment.currency).
*/

$img = fn (string $name): string => 'images/experiences/'.$name.'.jpg';

return [
    'experiences' => [
        ['slug' => 'two-nights-erg-chigaga-dunes', 'host' => 'hassan', 'category' => 'desert', 'city' => 'Erg Chigaga', 'hours' => 120, 'price' => 3200, 'group' => 8, 'image' => $img('erg-chigaga-dunes'), 'top' => true,
            'gallery' => [$img('erg-chigaga-camp'), $img('ait-benhaddou'), $img('ksar-first-light')],
            'title' => ['en' => 'Two nights in the Erg Chigaga dunes', 'fr' => 'Deux nuits dans les dunes de l’Erg Chigaga', 'nl' => 'Twee nachten in de duinen van Erg Chigaga'],
            'description' => [
                'en' => 'Five slow days from Marrakech to the edge of the Sahara with Hassan, a desert guide from M’Hamid. Cross the High Atlas, stop at Aït Benhaddou, then sleep two nights in a quiet camp in the Erg Chigaga dunes.',
                'fr' => 'Cinq jours sans se presser, de Marrakech aux portes du Sahara avec Hassan, guide du désert originaire de M’Hamid. Traversée du Haut Atlas, halte à Aït Benhaddou, puis deux nuits dans un camp paisible au cœur des dunes de l’Erg Chigaga.',
                'nl' => 'Vijf rustige dagen van Marrakech naar de rand van de Sahara met Hassan, woestijngids uit M’Hamid. Over de Hoge Atlas, een stop in Aït Benhaddou en twee nachten in een stil kamp in de duinen van Erg Chigaga.',
            ],
            // Detail-page story (Figma 09 · Experience detail v2).
            'story' => [
                'highlights' => [
                    'en' => ['Camel trek at sunset', 'Two nights in a desert camp', 'Aït Benhaddou (UNESCO)', 'Stargazing, lanterns off'],
                    'fr' => ['Balade à dos de dromadaire au coucher du soleil', 'Deux nuits en camp dans le désert', 'Aït Benhaddou (UNESCO)', 'Ciel étoilé, lanternes éteintes'],
                    'nl' => ['Kameeltocht bij zonsondergang', 'Twee nachten in een woestijnkamp', 'Aït Benhaddou (UNESCO)', 'Sterrenkijken, lantaarns uit'],
                ],
                'inclusions' => [
                    'en' => ['Local guide with Hassan', '4 nights (riad + desert camp)', 'Breakfasts, 2 lunches, 2 dinners', '4x4 transport & camel trek'],
                    'fr' => ['Guide local avec Hassan', '4 nuits (riad + camp dans le désert)', 'Petits-déjeuners, 2 déjeuners, 2 dîners', 'Transport en 4x4 et balade à dos de dromadaire'],
                    'nl' => ['Lokale gids: Hassan', '4 nachten (riad + woestijnkamp)', 'Ontbijt, 2 lunches, 2 diners', '4x4-vervoer & kameeltocht'],
                ],
                'exclusions' => [
                    'en' => ['flights', 'insurance', 'tips', 'personal expenses'],
                    'fr' => ['vols', 'assurance', 'pourboires', 'dépenses personnelles'],
                    'nl' => ['vluchten', 'verzekering', 'fooien', 'persoonlijke uitgaven'],
                ],
                'itinerary' => [
                    ['place' => ['name' => 'Marrakech', 'lat' => 31.6295, 'lng' => -7.9811],
                        'image' => $img('ait-benhaddou'),
                        'title' => ['en' => 'Marrakech → Aït Benhaddou', 'fr' => 'Marrakech → Aït Benhaddou', 'nl' => 'Marrakech → Aït Benhaddou'],
                        'description' => [
                            'en' => 'Meet Hassan at your pick-up point and cross the High Atlas. Lunch and a slow walk through the ksar.',
                            'fr' => 'Rendez-vous avec Hassan à votre point de prise en charge, puis traversée du Haut Atlas. Déjeuner et promenade tranquille dans le ksar.',
                            'nl' => 'Hassan haalt je op en samen steek je de Hoge Atlas over. Lunch en een rustige wandeling door de ksar.',
                        ]],
                    ['image' => $img('erg-chigaga-camp'), 'place' => ['name' => 'Aït Benhaddou', 'lat' => 31.047, 'lng' => -7.129],
                        'title' => ['en' => 'Aït Benhaddou → Erg Chigaga', 'fr' => 'Aït Benhaddou → Erg Chigaga', 'nl' => 'Aït Benhaddou → Erg Chigaga'],
                        'description' => [
                            'en' => 'South through the Drâa Valley to M’Hamid, then by 4x4 into the dunes. A camel trek at sunset and your first night at camp.',
                            'fr' => 'Cap au sud par la vallée du Drâa jusqu’à M’Hamid, puis en 4x4 dans les dunes. Balade à dos de dromadaire au coucher du soleil et première nuit au camp.',
                            'nl' => 'Zuidwaarts door de Drâa-vallei naar M’Hamid en met de 4x4 de duinen in. Een kameeltocht bij zonsondergang en je eerste nacht in het kamp.',
                        ]],
                    ['image' => $img('erg-chigaga-dunes'), 'place' => ['name' => 'Erg Chigaga', 'lat' => 29.85, 'lng' => -6.0],
                        'title' => ['en' => 'A free day in the dunes', 'fr' => 'Une journée libre dans les dunes', 'nl' => 'Een vrije dag in de duinen'],
                        'description' => [
                            'en' => 'No schedule: walk the dunes, try sandboarding or rest in camp. Lanterns off after dinner for the stars.',
                            'fr' => 'Pas de programme : marche dans les dunes, sandboard ou repos au camp. Lanternes éteintes après le dîner pour les étoiles.',
                            'nl' => 'Geen programma: wandelen door de duinen, sandboarden of uitrusten in het kamp. Na het diner gaan de lantaarns uit voor de sterren.',
                        ]],
                    ['title' => ['en' => 'Erg Chigaga → Ouarzazate', 'fr' => 'Erg Chigaga → Ouarzazate', 'nl' => 'Erg Chigaga → Ouarzazate'],
                        'description' => [
                            'en' => 'Back across the desert by 4x4 and on to Ouarzazate for a night in a riad.',
                            'fr' => 'Retour à travers le désert en 4x4, puis route vers Ouarzazate pour une nuit en riad.',
                            'nl' => 'Met de 4x4 terug door de woestijn en verder naar Ouarzazate voor een nacht in een riad.',
                        ]],
                    ['title' => ['en' => 'Back to Marrakech', 'fr' => 'Retour à Marrakech', 'nl' => 'Terug naar Marrakech'],
                        'description' => [
                            'en' => 'Over the Tizi n’Tichka pass and back in Marrakech by late afternoon.',
                            'fr' => 'Passage du col du Tizi n’Tichka et retour à Marrakech en fin d’après-midi.',
                            'nl' => 'Over de Tizi n’Tichka-pas en tegen het eind van de middag terug in Marrakech.',
                        ]],
                ],
            ]],
        ['slug' => 'local-food-journey-marrakech', 'host' => 'fatima', 'category' => 'food', 'city' => 'Marrakesh', 'hours' => 8, 'price' => 950, 'group' => 6, 'image' => $img('marrakech-food-journey'), 'top' => true,
            'title' => ['en' => 'A local food journey in Marrakech', 'fr' => 'Un voyage culinaire à Marrakech', 'nl' => 'Een culinaire reis door Marrakech'],
            'description' => ['en' => 'Hidden food stalls, a family kitchen and the real flavours of the medina — then cook lunch together in Fatima’s home.']],
        ['slug' => 'walk-through-ancient-medina-fes', 'host' => 'youssef', 'category' => 'culture', 'city' => 'Fes', 'hours' => 7, 'price' => 800, 'group' => 8, 'image' => $img('fes-medina-walk'), 'top' => false,
            'title' => ['en' => 'A walk through the ancient medina', 'fr' => 'Une promenade dans l’ancienne médina', 'nl' => 'Een wandeling door de oude medina'],
            'description' => ['en' => 'Tanneries, madrasas and the workshops of master artisans in the oldest living medina in the world.']],
        ['slug' => 'dawn-surf-on-the-atlantic', 'host' => 'karim', 'category' => 'water', 'city' => 'Taghazout', 'hours' => 6, 'price' => 1100, 'group' => 6, 'image' => $img('dawn-surf-atlantic'), 'top' => false,
            'title' => ['en' => 'Dawn surf on the Atlantic', 'fr' => 'Surf à l’aube sur l’Atlantique', 'nl' => 'Surfen bij zonsopgang op de Atlantische Oceaan'],
            'description' => ['en' => 'Catch the first, glassy waves of the day with a local instructor, then breakfast on the terrace above the point.']],
        ['slug' => 'hiking-the-high-atlas', 'host' => 'amina', 'category' => 'mountain', 'city' => 'Imlil', 'hours' => 72, 'price' => 1500, 'group' => 8, 'image' => $img('hiking-high-atlas'), 'top' => true,
            'title' => ['en' => 'Hiking the High Atlas', 'fr' => 'Randonnée dans le Haut Atlas', 'nl' => 'Wandelen in de Hoge Atlas'],
            'description' => ['en' => 'Trek through Berber villages, mountain valleys and wide-open views, sleeping in village guesthouses along the way.']],
        ['slug' => 'the-ksar-at-first-light', 'host' => 'omar', 'category' => 'culture', 'city' => 'Aït Benhaddou', 'hours' => 8, 'price' => 750, 'group' => 8, 'image' => $img('ksar-first-light'), 'top' => false,
            'title' => ['en' => 'The ksar at first light', 'fr' => 'Le ksar aux premières lueurs', 'nl' => 'De ksar bij het eerste licht'],
            'description' => ['en' => 'Walk the earthen ksar before the day-trippers arrive, with the historian whose family has lived there for generations.']],
        ['slug' => 'a-morning-in-the-souk', 'host' => 'leila', 'category' => 'workshop', 'city' => 'Marrakesh', 'hours' => 4, 'price' => 700, 'group' => 6, 'image' => $img('morning-in-the-souk'), 'top' => false,
            'title' => ['en' => 'A morning in the souk', 'fr' => 'Une matinée dans le souk', 'nl' => 'Een ochtend in de souk'],
            'description' => ['en' => 'Shop with a local, meet the artisans and find the stalls tourists miss — bargaining tips included.']],
        ['slug' => 'sunset-quad-adventure-agafay', 'host' => 'rachid', 'category' => 'adventure', 'city' => 'Agafay', 'hours' => 4, 'price' => 900, 'group' => 8, 'image' => $img('agafay-sunset-quad'), 'top' => false,
            'title' => ['en' => 'Sunset quad adventure', 'fr' => 'Quad au coucher du soleil', 'nl' => 'Quad-avontuur bij zonsondergang'],
            'description' => ['en' => 'Ride across the stony Agafay desert as the light turns gold, with mint tea at a nomad camp before heading back.']],
        ['slug' => 'a-day-in-essaouira', 'host' => 'sara', 'category' => 'culture', 'city' => 'Essaouira', 'hours' => 8, 'price' => 850, 'group' => 6, 'image' => $img('day-in-essaouira'), 'top' => false,
            'title' => ['en' => 'A day in Essaouira', 'fr' => 'Une journée à Essaouira', 'nl' => 'Een dag in Essaouira'],
            'description' => ['en' => 'The port, the ramparts and the art scene with a local who grew up here — and grilled fish straight from the boats.']],
    ],

    // Demo travellers who completed a booking and left a review.
    'travellers' => [
        ['key' => 'sophie', 'name' => 'Sophie', 'country' => 'Canada', 'avatar' => 'images/discover/travellers/sophie.png'],
        ['key' => 'daniel', 'name' => 'Daniel', 'country' => 'UK', 'avatar' => 'images/discover/travellers/daniel.png'],
        ['key' => 'lena', 'name' => 'Lena', 'country' => 'Germany', 'avatar' => 'images/discover/travellers/lena.png'],
        ['key' => 'james', 'name' => 'James', 'country' => 'USA', 'avatar' => 'images/discover/travellers/james.png'],
        ['key' => 'marco', 'name' => 'Marco', 'country' => 'Italy', 'avatar' => 'images/discover/travellers/marco.png'],
        ['key' => 'emily', 'name' => 'Emily', 'country' => 'UK', 'avatar' => 'images/discover/travellers/emily.png'],
        ['key' => 'anouk', 'name' => 'Anouk', 'country' => 'Netherlands', 'avatar' => null],
        ['key' => 'marc', 'name' => 'Marc', 'country' => 'France', 'avatar' => null],
        ['key' => 'julia', 'name' => 'Julia', 'country' => 'Spain', 'avatar' => null],
    ],

    /*
    | Oldest first — the order they were written. `travelled` is the trip date
    | (the booking's availability), `details` = host / organisation / pick-up / value.
    */
    'reviews' => [
        ['experience' => 'hiking-the-high-atlas', 'by' => 'anouk', 'travelled' => '2025-10-11', 'rating' => 5, 'details' => [5, 5, 4, 5],
            'body' => 'Amina knew every family in the valley. Three days felt like a week in the best way.'],
        ['experience' => 'walk-through-ancient-medina-fes', 'by' => 'marc', 'travelled' => '2025-11-02', 'rating' => 5, 'details' => [5, 5, 5, 4],
            'body' => 'Youssef took us into workshops we would never have found alone, and nobody pushed us to buy anything.'],
        ['experience' => 'a-day-in-essaouira', 'by' => 'julia', 'travelled' => '2025-11-20', 'rating' => 5, 'details' => [5, 5, 5, 5],
            'body' => 'Sara showed us her Essaouira — the port at dawn, the galleries and the best grilled sardines.'],
        ['experience' => 'sunset-quad-adventure-agafay', 'by' => 'marc', 'travelled' => '2025-12-06', 'rating' => 4, 'details' => [5, 4, 4, 4],
            'body' => 'Great fun and well organised. The tea at the camp afterwards was the perfect end.'],
        ['experience' => 'a-morning-in-the-souk', 'by' => 'emily', 'travelled' => '2026-02-14', 'rating' => 5, 'details' => [5, 5, 5, 5],
            'body' => 'Leila showed us where locals actually shop and how to bargain kindly. I bought less and loved it more.',
            'photos' => ['images/reviews/emily-souk.jpg']],
        ['experience' => 'the-ksar-at-first-light', 'by' => 'marco', 'travelled' => '2026-03-08', 'rating' => 5, 'details' => [5, 5, 5, 5],
            'body' => 'Arriving before the crowds made all the difference. Omar knows every family story behind those walls.'],
        ['experience' => 'hiking-the-high-atlas', 'by' => 'james', 'travelled' => '2026-04-04', 'rating' => 4, 'details' => [5, 4, 4, 4],
            'body' => 'Day two is tougher than it sounds — bring proper boots. Amina adjusted the route for us and the village lunch was the highlight.',
            'photos' => ['images/reviews/james-high-atlas.jpg'],
            'reply' => 'Thanks James — we now mention the boots in the packing list.'],
        ['experience' => 'dawn-surf-on-the-atlantic', 'by' => 'lena', 'travelled' => '2026-01-17', 'rating' => 5, 'details' => [5, 5, 5, 5],
            'body' => 'Karim had us standing up by the third wave. Lunch on the beach afterwards was the best tagine of the trip.'],
        ['experience' => 'two-nights-erg-chigaga-dunes', 'by' => 'daniel', 'travelled' => '2026-02-20', 'rating' => 5, 'details' => [5, 5, 5, 4],
            'body' => 'Hassan is an amazing guide. We felt safe, welcomed and saw a side of Morocco we’ll never forget.',
            'photos' => ['images/reviews/daniel-dunes.jpg', 'images/reviews/daniel-night-camp.jpg']],
        ['experience' => 'local-food-journey-marrakech', 'by' => 'sophie', 'travelled' => '2026-03-21', 'rating' => 5, 'details' => [5, 5, 5, 5],
            'body' => 'Fatima taught us to fold msemen in her courtyard. We came for a cooking class and left feeling like family.',
            'photos' => ['images/reviews/sophie-food-stalls.jpg', 'images/reviews/sophie-tagine.jpg'],
            'reply' => 'Thank you Sophie — come back for harira in Ramadan!'],
    ],
];
