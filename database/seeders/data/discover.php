<?php

/*
| Discover Morocco seed content — copy taken from the Figma design
| (Tourstica-a · Discover / Destination Marrakech / Interest Cuisine).
| Long guide copy is English; FR/NL fall back to English until translated.
| Images live in public/images/discover.
*/

$img = fn (string $path): string => '/images/discover/'.$path;

return [
    // region = destination slug, specialty = interest slug (Local hosts directory filters).
    // Remote portraits are free Unsplash photos (Mehdi El marouazi, JEaLiFe Pictures, Marco D'Abramo,
    // loli mass, Tânia Mousinho, HamZa NOUASRIA) — replace with the hosts' own photos from admin.
    'hosts' => [
        ['slug' => 'hassan', 'name' => 'Hassan', 'city' => 'M’Hamid', 'region' => 'zagora-sahara', 'specialty' => 'adventure', 'licence' => 'MA-3847', 'photo' => $img('hosts/hassan.jpg'), 'avatar' => $img('hosts/avatars/hassan.png'),
            'role' => ['en' => 'Desert guide', 'fr' => 'Guide du désert', 'nl' => 'Woestijngids'],
            'bio' => ['en' => 'Picks you up in Marrakech for the Erg Chigaga trip.'],
            // Copy from Figma "06 · Host profile — Hassan". Confirm with the host before launch.
            'profile' => [
                'cover' => 'images/reviews/daniel-dunes.jpg',
                'languages' => ['ar', 'fr', 'en'],
                'partner_since' => 2019,
                'quote' => [
                    'en' => 'The desert has taught me patience, simplicity and the beauty of small moments. I love sharing this with travellers.',
                    'fr' => 'Le désert m’a appris la patience, la simplicité et la beauté des petits moments. J’aime partager cela avec les voyageurs.',
                    'nl' => 'De woestijn heeft me geduld, eenvoud en de schoonheid van kleine momenten geleerd. Dat deel ik graag met reizigers.',
                ],
                'story' => [
                    'en' => ['Hassan grew up in M’Hamid, the last village before the dunes of Erg Chigaga. His family moved with the seasons, and he learned to read the sand, the wind and the stars long before he learned to drive.', 'He has guided small groups since 2019, and still ends every trip the same way: lanterns off, everyone quiet, looking up.'],
                    'fr' => ['Hassan a grandi à M’Hamid, dernier village avant les dunes de l’Erg Chigaga. Sa famille se déplaçait au fil des saisons, et il a appris à lire le sable, le vent et les étoiles bien avant d’apprendre à conduire.', 'Il guide de petits groupes depuis 2019 et termine toujours chaque voyage de la même façon : lanternes éteintes, tout le monde en silence, les yeux levés.'],
                    'nl' => ['Hassan groeide op in M’Hamid, het laatste dorp voor de duinen van Erg Chigaga. Zijn familie trok met de seizoenen mee, en hij leerde het zand, de wind en de sterren lezen lang voordat hij leerde autorijden.', 'Hij begeleidt sinds 2019 kleine groepen en sluit elke reis nog steeds op dezelfde manier af: lantaarns uit, iedereen stil, omhoog kijkend.'],
                ],
                'skills' => [
                    'en' => ['Desert navigation', 'Camel trekking', 'Stargazing', 'Draa Valley history', 'Amazigh & Hassani culture', 'Desert cooking'],
                    'fr' => ['Orientation dans le désert', 'Randonnée à dos de dromadaire', 'Observation des étoiles', 'Histoire de la vallée du Drâa', 'Culture amazighe et hassanie', 'Cuisine du désert'],
                    'nl' => ['Navigeren in de woestijn', 'Kameeltochten', 'Sterrenkijken', 'Geschiedenis van de Drâa-vallei', 'Amazigh- en Hassani-cultuur', 'Woestijnkoken'],
                ],
            ]],
        ['slug' => 'fatima', 'name' => 'Fatima', 'city' => 'Marrakech', 'region' => 'marrakesh', 'specialty' => 'cuisine', 'licence' => 'MA-1120', 'photo' => $img('hosts/fatima.jpg'), 'avatar' => $img('hosts/avatars/fatima.png'),
            'role' => ['en' => 'Home cook', 'fr' => 'Cuisinière', 'nl' => 'Thuiskok'],
            'bio' => ['en' => 'Leads the food journey through her neighbourhood.'],
            // Quote from the Figma landing (sample content): replace with the host's own words.
            'profile' => ['quote' => [
                'en' => 'My mother taught me these recipes.',
                'fr' => 'C’est ma mère qui m’a appris ces recettes.',
                'nl' => 'Mijn moeder heeft me deze recepten geleerd.',
            ]]],
        ['slug' => 'youssef', 'name' => 'Youssef', 'city' => 'Fès', 'region' => 'fes', 'specialty' => 'culture', 'licence' => 'MA-2218', 'photo' => $img('hosts/youssef.jpg'), 'avatar' => $img('hosts/avatars/youssef.png'),
            'role' => ['en' => 'Cultural guide', 'fr' => 'Guide culturel', 'nl' => 'Cultuurgids'], 'bio' => null],
        ['slug' => 'omar', 'name' => 'Omar', 'city' => 'Aït Benhaddou', 'region' => 'zagora-sahara', 'specialty' => 'history', 'licence' => 'MA-2781', 'photo' => $img('hosts/omar.jpg'), 'avatar' => $img('hosts/avatars/omar.png'),
            'role' => ['en' => 'Historian', 'fr' => 'Historien', 'nl' => 'Historicus'], 'bio' => null],
        ['slug' => 'amina', 'name' => 'Amina', 'city' => 'High Atlas', 'region' => 'high-atlas', 'specialty' => 'nature', 'licence' => 'MA-3056', 'photo' => 'https://images.unsplash.com/photo-1700775115339-bf46d24218af?w=600&h=700&fit=crop&crop=faces&q=80&auto=format', 'avatar' => $img('hosts/avatars/amina.png'),
            'role' => ['en' => 'Mountain guide', 'fr' => 'Guide de montagne', 'nl' => 'Berggids'], 'bio' => null],
        ['slug' => 'leila', 'name' => 'Leila', 'city' => 'Marrakech', 'region' => 'marrakesh', 'specialty' => 'culture', 'licence' => 'MA-1674', 'photo' => 'https://images.unsplash.com/photo-1731745078172-e3603e118f65?w=600&h=700&fit=crop&crop=faces&q=80&auto=format', 'avatar' => null,
            'role' => ['en' => 'Local expert', 'fr' => 'Experte locale', 'nl' => 'Lokale expert'], 'bio' => null],
        ['slug' => 'sara', 'name' => 'Sara', 'city' => 'Essaouira', 'region' => 'atlantic-coast', 'specialty' => 'cuisine', 'licence' => 'MA-1463', 'photo' => 'https://images.unsplash.com/photo-1635355880372-96b9fd88a218?w=600&h=700&fit=crop&crop=faces&q=80&auto=format', 'avatar' => null,
            'role' => ['en' => 'Local host', 'fr' => 'Hôte locale', 'nl' => 'Lokale gastvrouw'], 'bio' => null],
        ['slug' => 'karim', 'name' => 'Karim', 'city' => 'Taghazout', 'region' => 'atlantic-coast', 'specialty' => 'adventure', 'licence' => 'MA-1902', 'photo' => 'https://images.unsplash.com/photo-1556135063-eba17c48d523?w=600&h=700&fit=crop&crop=faces&q=80&auto=format', 'avatar' => $img('hosts/avatars/karim.png'),
            'role' => ['en' => 'Surf instructor', 'fr' => 'Moniteur de surf', 'nl' => 'Surfinstructeur'], 'bio' => null],
        ['slug' => 'rachid', 'name' => 'Rachid', 'city' => 'Agafay Desert', 'region' => 'marrakesh', 'specialty' => 'adventure', 'licence' => 'MA-2290', 'photo' => 'https://images.unsplash.com/photo-1614495039368-525273956716?w=600&h=700&fit=crop&crop=faces&q=80&auto=format', 'avatar' => null,
            'role' => ['en' => 'Adventure guide', 'fr' => 'Guide d’aventure', 'nl' => 'Avonturengids'], 'bio' => null],
    ],

    // experience slug => host slug (showcase experiences from LandingShowcaseSeeder)
    'experience_hosts' => [
        'showcase-marrakesh-food-walk' => 'fatima',
        'showcase-marrakech-balloon-ride' => 'leila',
        'showcase-sahara-desert-camp' => 'hassan',
        'showcase-fes-craft-workshop' => 'youssef',
        'showcase-essaouira-coast-day' => 'sara',
        'showcase-atlas-mountain-hike' => 'amina',
    ],

    'interests' => [
        ['slug' => 'cuisine', 'categories' => ['food'], 'image' => $img('interests/cuisine.jpg'),
            'name' => ['en' => 'Cuisine', 'fr' => 'Cuisine', 'nl' => 'Keuken'],
            'summary' => ['en' => 'Tagines, bread ovens and family recipes.', 'fr' => 'Tajines, fours à pain et recettes de famille.', 'nl' => 'Tajines, broodovens en familierecepten.']],
        ['slug' => 'culture', 'categories' => ['culture', 'photography'], 'image' => $img('interests/culture.jpg'),
            'name' => ['en' => 'Culture & Traditions', 'fr' => 'Culture et traditions', 'nl' => 'Cultuur & tradities'],
            'summary' => ['en' => 'Music, festivals and everyday rituals.', 'fr' => 'Musique, festivals et rituels du quotidien.', 'nl' => 'Muziek, festivals en dagelijkse rituelen.']],
        ['slug' => 'history', 'categories' => ['culture'], 'image' => $img('interests/history.jpg'),
            'name' => ['en' => 'History & Heritage', 'fr' => 'Histoire et patrimoine', 'nl' => 'Geschiedenis & erfgoed'],
            'summary' => ['en' => 'Kasbahs, medinas and imperial cities.', 'fr' => 'Kasbahs, médinas et villes impériales.', 'nl' => 'Kasbahs, medina’s en keizerssteden.']],
        ['slug' => 'nature', 'categories' => ['mountain'], 'image' => $img('interests/nature.jpg'),
            'name' => ['en' => 'Nature', 'fr' => 'Nature', 'nl' => 'Natuur'],
            'summary' => ['en' => 'Atlas valleys, cedar forests and quiet villages.', 'fr' => 'Vallées de l’Atlas, forêts de cèdres et villages paisibles.', 'nl' => 'Atlasdalen, cederbossen en rustige dorpen.']],
        ['slug' => 'adventure', 'categories' => ['adventure', 'desert', 'balloon', 'water'], 'image' => $img('interests/adventure.jpg'),
            'name' => ['en' => 'Adventure', 'fr' => 'Aventure', 'nl' => 'Avontuur'],
            'summary' => ['en' => 'Dunes, trails, quads and Atlantic waves.', 'fr' => 'Dunes, sentiers, quads et vagues de l’Atlantique.', 'nl' => 'Duinen, paden, quads en Atlantische golven.']],
        ['slug' => 'shopping', 'categories' => ['workshop'], 'image' => $img('interests/shopping.jpg'),
            'name' => ['en' => 'Shopping & Handcrafts', 'fr' => 'Shopping et artisanat', 'nl' => 'Shoppen & handwerk'],
            'summary' => ['en' => 'Souks, workshops and the hands behind them.', 'fr' => 'Souks, ateliers et les mains qui les font vivre.', 'nl' => 'Souks, werkplaatsen en de handen erachter.']],
    ],

    // Existing destination slugs are updated; new ones are created.
    'destinations' => [
        ['slug' => 'marrakesh', 'city' => 'Marrakesh', 'region' => 'Marrakech-Safi', 'x' => 37.9, 'y' => 50.0, 'sort' => 0,
            'image' => $img('destinations/marrakech.jpg'), 'hero' => $img('destinations/marrakech.jpg'),
            'name' => ['en' => 'Marrakech', 'fr' => 'Marrakech', 'nl' => 'Marrakech'],
            'summary' => ['en' => 'Red-walled medina, buzzing souks and riad courtyards.'],
            'tags' => ['en' => ['Culture', 'Cuisine', 'Crafts'], 'fr' => ['Culture', 'Cuisine', 'Artisanat'], 'nl' => ['Cultuur', 'Keuken', 'Ambacht']]],
        ['slug' => 'fes', 'city' => 'Fes', 'region' => 'Fès-Meknès', 'x' => 65.2, 'y' => 27.5, 'sort' => 1,
            'image' => $img('destinations/fes.jpg'), 'hero' => null,
            'name' => ['en' => 'Fès', 'fr' => 'Fès', 'nl' => 'Fez'],
            'summary' => ['en' => 'The oldest living medina and its master artisans.'],
            'tags' => ['en' => ['Heritage', 'Crafts'], 'fr' => ['Patrimoine', 'Artisanat'], 'nl' => ['Erfgoed', 'Ambacht']]],
        ['slug' => 'chefchaouen', 'city' => 'Chefchaouen', 'region' => 'Tanger-Tétouan-Al Hoceïma', 'x' => 62.1, 'y' => 13.3, 'sort' => 2,
            'image' => null, 'hero' => null,
            'name' => ['en' => 'Chefchaouen', 'fr' => 'Chefchaouen', 'nl' => 'Chefchaouen'],
            'summary' => ['en' => 'Blue lanes tucked into the Rif mountains.'],
            'tags' => ['en' => ['Nature', 'Culture'], 'fr' => ['Nature', 'Culture'], 'nl' => ['Natuur', 'Cultuur']]],
        ['slug' => 'essaouira', 'city' => 'Essaouira', 'region' => 'Marrakech-Safi', 'x' => 18.2, 'y' => 52.5, 'sort' => 3,
            'image' => $img('destinations/essaouira.jpg'), 'hero' => null,
            'name' => ['en' => 'Essaouira', 'fr' => 'Essaouira', 'nl' => 'Essaouira'],
            'summary' => ['en' => 'A windy Atlantic port of seafood, music and surf.'],
            'tags' => ['en' => ['Coast', 'Cuisine'], 'fr' => ['Côte', 'Cuisine'], 'nl' => ['Kust', 'Keuken']]],
        ['slug' => 'high-atlas', 'city' => 'Imlil', 'region' => 'Marrakech-Safi', 'x' => 42.4, 'y' => 60.0, 'sort' => 4,
            'image' => $img('destinations/high-atlas.jpg'), 'hero' => null,
            'name' => ['en' => 'High Atlas', 'fr' => 'Haut Atlas', 'nl' => 'Hoge Atlas'],
            'summary' => ['en' => 'Amazigh villages, green valleys and mountain trails.'],
            'tags' => ['en' => ['Nature', 'Adventure'], 'fr' => ['Nature', 'Aventure'], 'nl' => ['Natuur', 'Avontuur']]],
        ['slug' => 'zagora-sahara', 'city' => 'Zagora', 'region' => 'Drâa-Tafilalet', 'x' => 71.2, 'y' => 81.7, 'sort' => 5,
            'image' => '/images/experiences/erg-chigaga-dunes.jpg', 'hero' => null,
            'name' => ['en' => 'Sahara', 'fr' => 'Sahara', 'nl' => 'Sahara'],
            'summary' => ['en' => 'Dunes, kasbahs and nights under the stars.'],
            'tags' => ['en' => ['Desert', 'Adventure'], 'fr' => ['Désert', 'Aventure'], 'nl' => ['Woestijn', 'Avontuur']]],
        // A region (Essaouira → Taghazout) for the hosts directory; no map pin.
        ['slug' => 'atlantic-coast', 'city' => 'Taghazout', 'region' => 'Souss-Massa', 'x' => null, 'y' => null, 'sort' => 6,
            'image' => $img('destinations/essaouira.jpg'), 'hero' => null,
            'name' => ['en' => 'Atlantic coast', 'fr' => 'Côte atlantique', 'nl' => 'Atlantische kust'],
            'summary' => ['en' => 'Surf towns, fishing ports and long sandy beaches.'],
            'tags' => ['en' => ['Coast', 'Adventure'], 'fr' => ['Côte', 'Aventure'], 'nl' => ['Kust', 'Avontuur']]],
    ],

    // destination slug => [interest slug => note]
    'destination_interests' => [
        'marrakesh' => ['cuisine' => 'Tanjia and evening food stalls.'],
        'fes' => ['cuisine' => 'Pastilla and old-medina kitchens.'],
        'essaouira' => ['cuisine' => 'Grilled fish fresh from the port.'],
        'high-atlas' => ['cuisine' => 'Home cooking in Amazigh villages.'],
    ],

    'stories' => [
        ['title' => 'A courtyard kitchen in Fès', 'city' => 'Fès', 'days' => 2, 'rating' => 4.9, 'image' => $img('stories/courtyard-kitchen-fes.jpg'), 'destination' => 'fes', 'interest' => 'cuisine', 'author' => 'Sample traveller', 'country' => 'Netherlands'],
        ['title' => 'Dawn surf on the Atlantic', 'city' => 'Taghazout', 'days' => 3, 'rating' => 4.8, 'image' => $img('destinations/essaouira.jpg'), 'destination' => 'essaouira', 'interest' => 'adventure', 'author' => 'Sample traveller', 'country' => 'France'],
        ['title' => 'Hiking the High Atlas', 'city' => 'Imlil', 'days' => 4, 'rating' => 4.7, 'image' => $img('destinations/high-atlas.jpg'), 'destination' => 'marrakesh', 'interest' => 'nature', 'author' => 'Sample traveller', 'country' => 'United Kingdom'],
    ],

    'guides' => [
        'destinations' => [
            'marrakesh' => [
                'guide' => [
                    'hero' => [
                        'location' => 'Marrakech-Safi, Morocco',
                        'title' => 'Marrakech, where the city lives in its',
                        'accent' => 'streets.',
                        'lede' => 'A red-walled medina of souks, riad courtyards and a square that turns into an open-air kitchen every evening.',
                    ],
                    'facts' => ['known_for' => 'Medina, souks & riads', 'best_for' => 'Cuisine · Crafts · Culture', 'base_for' => 'High Atlas & Sahara trips'],
                    'overview' => [
                        'title' => 'A city that lives in its streets.',
                        'paragraphs' => [
                            'Marrakech grew as a trading city at the foot of the High Atlas, and its medina still works that way: neighbourhoods built around a mosque, a fountain, a bread oven and a hammam. Life happens outside — in alleys, workshops and rooftop terraces.',
                            'It is loud, warm and a little overwhelming on the first day. Go slowly, walk with someone who lives here, and the city opens up quickly.',
                        ],
                        'quote' => 'Everyone rushes to the square. Come with me to the bread oven first — that’s where the neighbourhood talks.',
                        'quote_by' => 'Fatima, home cook in Marrakech',
                    ],
                    'landmarks' => ['title' => 'Places worth slowing down for.'],
                    'culture' => [
                        'title' => 'Crafts you can still watch being made.',
                        'paragraphs' => [
                            'Each corner of the souk has its trade — leather near the tanneries, copper in the smiths’ quarter, wool dyed in the dyers’ souk. Many workshops are family-run for generations.',
                            'Bargaining is expected, but friendly. Ask before photographing people and their work.',
                        ],
                    ],
                    'food' => ['title' => 'What Marrakech eats.'],
                    'experiences' => ['title' => 'Live Marrakech with a local host.', 'link' => 'Explore all experiences in Marrakech'],
                    'stories' => ['title' => 'Written after the trip.'],
                    'hosts' => ['title' => 'The people who show you their city.'],
                    'tips' => ['title' => 'Good to know before you go.'],
                    'start' => ['body' => 'Most first-timers begin with a food walk, then head to the mountains.'],
                    'cta' => [
                        'eyebrow' => 'Ready for Marrakech?',
                        'title' => 'See every experience in',
                        'accent' => 'Marrakech.',
                        'lede' => 'Food walks, souk mornings and trips to the mountains and desert — all with local hosts.',
                    ],
                ],
                'blocks' => [
                    ['type' => 'landmark', 'title' => 'Jemaa el-Fnaa', 'body' => 'The main square: storytellers and musicians by day, open-air food stalls after dusk.', 'image' => $img('landmarks/jemaa-el-fnaa.jpg')],
                    ['type' => 'landmark', 'title' => 'Koutoubia Mosque', 'body' => 'Its 12th-century minaret is the city’s compass. Non-Muslims admire it from the gardens.', 'image' => $img('landmarks/koutoubia.jpg')],
                    ['type' => 'landmark', 'title' => 'Bahia Palace', 'body' => 'A 19th-century palace of painted cedar ceilings, zellige tiles and quiet courtyards.', 'image' => $img('landmarks/bahia-palace.jpg')],
                    ['type' => 'craft', 'title' => null, 'body' => null, 'image' => $img('interests/shopping.jpg')],
                    ['type' => 'craft', 'title' => null, 'body' => null, 'image' => $img('interests/culture.jpg')],
                    ['type' => 'dish', 'title' => 'Tanjia', 'body' => 'The city’s own dish: meat slow-cooked for hours in a clay jar in the embers of a hammam fire.', 'image' => $img('dishes/tagine.jpg')],
                    ['type' => 'dish', 'title' => 'Evening stalls', 'body' => 'Snails, harira soup and grilled skewers in Jemaa el-Fnaa once the sun goes down.', 'image' => $img('experiences/food-journey.jpg')],
                    ['type' => 'dish', 'title' => 'Mint tea & bread', 'body' => 'Poured from high, served with khobz still warm from the neighbourhood oven.', 'image' => $img('dishes/khobz.jpg')],
                    ['type' => 'tip', 'title' => 'Getting around', 'body' => 'The medina is walkable only. Taxis wait at the gates; agree the fare or ask for the meter.'],
                    ['type' => 'tip', 'title' => 'When to go', 'body' => 'Spring and autumn are mild. Summer afternoons are very hot — plan walks early.'],
                    ['type' => 'tip', 'title' => 'Staying', 'body' => 'Riads inside the medina are quiet and central; ask your host to meet you at the gate.'],
                    ['type' => 'tip', 'title' => 'Respect', 'body' => 'Dress modestly in the medina and ask before photographing people.'],
                ],
            ],
        ],
        'interests' => [
            'cuisine' => [
                'hero_image' => $img('interests/cuisine.jpg'),
                'guide' => [
                    'hero' => [
                        'eyebrow' => 'Interest · Cuisine',
                        'title' => 'Moroccan food is a family',
                        'accent' => 'conversation.',
                        'lede' => 'Slow tagines, Friday couscous and bread from the neighbourhood oven — recipes passed down at home, and best learned in one.',
                        'primary' => 'Food experiences',
                        'secondary' => 'Where to eat it',
                    ],
                    'meaning' => [
                        'title' => 'Cooked slowly, shared from one',
                        'accent' => 'dish.',
                        'paragraphs' => [
                            'A Moroccan meal is built for sharing: one tagine in the middle of the table, bread instead of cutlery, and a host who keeps adding to your side of the plate.',
                            'Recipes change from region to region and from house to house — which is why the best way to understand them is with a family, not a menu.',
                        ],
                        'quote' => 'My mother taught me these recipes. I still cook them the way she did — no measuring, only tasting.',
                        'quote_by' => 'Fatima · Home cook, Marrakech',
                        'quote_avatar' => $img('hosts/fatima-avatar.png'),
                    ],
                    'origins' => ['title' => 'Many kitchens in one', 'accent' => 'cuisine.', 'lede' => 'Moroccan food grew from several traditions that met over centuries.'],
                    'dishes' => ['title' => 'What to taste', 'accent' => 'first.'],
                    'regions' => ['title' => 'Every region cooks it', 'accent' => 'differently.'],
                    'experiences' => ['title' => 'Cook, taste and shop with a local.', 'lede' => 'Food experiences led by named, licensed hosts.', 'link' => 'All food experiences'],
                    'stories' => ['title' => 'From someone who cooked it.'],
                    'hosts' => ['title' => 'Cooks who open their kitchens.', 'empty' => 'More food hosts in Fès and Essaouira are being vetted. We only list hosts once their licence is confirmed.'],
                    'notes' => ['title' => 'At a Moroccan', 'accent' => 'table.'],
                    'cta' => [
                        'eyebrow' => 'Hungry yet?',
                        'title' => 'Cook, taste and share a table in',
                        'accent' => 'Morocco.',
                        'lede' => 'Every food experience is led by a local host and confirmed before you pay.',
                        'button' => 'Explore food experiences',
                    ],
                ],
                'blocks' => [
                    ['type' => 'origin', 'title' => 'Amazigh roots', 'body' => 'Couscous, clay cooking pots and the slow tagine come from the country’s first peoples.'],
                    ['type' => 'origin', 'title' => 'Arab & Andalusian', 'body' => 'Spices, sweet-and-savoury dishes like pastilla, and refined palace cooking.'],
                    ['type' => 'origin', 'title' => 'Jewish Moroccan', 'body' => 'Preserved lemons, slow Sabbath stews and a long tradition of shared recipes.'],
                    ['type' => 'dish', 'title' => 'Tagine', 'body' => 'Meat or vegetables cooked for hours under a cone-shaped lid.', 'image' => $img('dishes/tagine.jpg')],
                    ['type' => 'dish', 'title' => 'Couscous', 'body' => 'Steamed semolina, traditionally the family meal every Friday.', 'image' => $img('dishes/couscous.jpg')],
                    ['type' => 'dish', 'title' => 'Khobz', 'body' => 'Round bread, often baked in the neighbourhood’s shared oven.', 'image' => $img('dishes/khobz.jpg')],
                    ['type' => 'also', 'title' => 'Pastilla · Harira · Rfissa · Tanjia · Mint tea · Msemen', 'body' => 'Ask your host what their family cooks — every answer is different.'],
                    ['type' => 'tip', 'title' => 'Eat with bread', 'body' => 'Use a piece of khobz to scoop from your side of the dish, with your right hand.'],
                    ['type' => 'tip', 'title' => 'Say yes to tea', 'body' => 'Mint tea is a welcome. A glass or two is polite; you can decline the third.'],
                    ['type' => 'tip', 'title' => 'During Ramadan', 'body' => 'Many places close by day; evenings are lively. Ask your host about iftar.'],
                    ['type' => 'tip', 'title' => 'Dietary needs', 'body' => 'Tell your host when you book — vegetarian tagines are easy to arrange.'],
                ],
            ],
        ],
    ],
];
