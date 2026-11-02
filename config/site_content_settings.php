<?php

// Examples are suggestions, never published automatically.
return [
    'panels' => [
        'search' => ['title' => 'Search & sharing', 'subtitle' => 'Control how the home page is described in search results and shared links.', 'sections' => ['seo'], 'purpose' => 'Search appearance (SEO)', 'publish' => 'Publish search settings', 'source' => 'https://developers.google.com/search/docs/appearance/title-link',
            'tips' => [
                ['title' => 'Write for the traveller', 'body' => 'Name the page and what it offers. A useful title is specific; repeating Morocco tours in every phrase does not help readers.', 'example' => 'Morocco Tours & Local Experiences | Tourstica'],
                ['title' => 'Describe the real booking process', 'body' => 'Explain what visitors can do next. Do not promise instant confirmation when bookings are confirmed manually.', 'example' => 'Compare experiences from local agencies in Morocco and send a booking request. Your booking is confirmed manually.'],
                ['title' => 'Keep language versions consistent', 'body' => 'Write EN, FR and NL separately. Canonical and language alternates are generated from the real page routes. Character counts are writing guidance; search engines can rewrite or truncate titles and snippets.'],
            ]],
        'publisher' => ['title' => 'Publisher identity', 'subtitle' => 'Make it clear who operates Tourstica and which official profiles belong to it.', 'sections' => ['geo'], 'purpose' => 'Organization identity (GEO)', 'publish' => 'Publish publisher details', 'source' => 'https://developers.google.com/search/docs/appearance/structured-data/organization',
            'tips' => [
                ['title' => 'Use one consistent identity', 'body' => 'Match the organization name, website and logo to your public branding. Add a legal name only if it is accurate.', 'example' => 'Brand name: Tourstica. Legal name: your actual registered company name.'],
                ['title' => 'Link evidence, not claims', 'body' => 'Use official profiles and a real about page. Avoid invented accreditations, review totals, addresses or licensing claims.'],
                ['title' => 'GEO is not a special schema switch', 'body' => 'Clear, accurate, accessible content supports discovery. Google says its AI search features need no special AI markup. Organization data describes the publisher; it does not guarantee an AI citation.'],
            ]],
        'answers' => ['title' => 'Clear answers', 'subtitle' => 'Help travellers understand your service with a direct answer, useful sources and an honest review date.', 'sections' => ['aeo'], 'purpose' => 'Useful answers (AEO)', 'publish' => 'Publish answer content', 'source' => 'https://developers.google.com/search/docs/appearance/ai-features',
            'tips' => [
                ['title' => 'Answer the question first', 'body' => 'Start with the useful answer, then add the details a traveller needs. Use the actual manual-confirmation flow.', 'example' => 'Question: Is my booking confirmed immediately? Answer: No. After you send a booking request, an administrator reviews it and confirms it manually.'],
                ['title' => 'Show the same information to everyone', 'body' => 'The answer summary, sources and review details you publish are visible on the homepage. Add sources that actually support your wording, and keep practical details current.'],
                ['title' => 'Keep the existing FAQ workflow', 'body' => 'Use the FAQ manager for questions and answers. Adding FAQ markup is not a promise of a rich result. Do not invent questions, reviews or expert credentials to fill a schema.'],
            ]],
    ],
    'fields' => [
        'seo' => [
            'title' => ['advice' => 'Lead with the page topic and brand. Around 50–60 characters is a useful editing target, not a Google limit.', 'example' => 'Morocco Tours & Local Experiences | Tourstica'],
            'description' => ['advice' => 'Summarise this page accurately. Around 140–160 characters is a useful editing target, not a ranking rule.', 'example' => 'Compare experiences from local agencies in Morocco. Explore trip details and send a booking request for manual confirmation.'],
            'robots' => ['advice' => 'Public launch pages normally use index, follow. noindex asks search engines to omit the page; it is not password protection.'],
            'social_title' => ['advice' => 'A clear title for shared links. Empty uses the SEO title.', 'example' => 'Find your next experience in Morocco'],
            'social_description' => ['advice' => 'Describe what is on the linked page. Empty uses the meta description.'],
            'social_image' => ['advice' => 'Choose a real, relevant image. A landscape image around 1200 × 630 is a practical sharing format; avoid text that becomes unreadable on a phone.'],
            'max_snippet' => ['label' => 'Text snippet allowance', 'type' => 'text', 'allowed' => ['-1', '0', '160', '320'], 'max' => 5, 'hint' => 'Optional. -1 means no specified character limit; 0 means no text snippet. Blank leaves the search engine default.', 'default' => ''],
            'max_image_preview' => ['label' => 'Image preview allowance', 'type' => 'text', 'allowed' => ['none', 'standard', 'large'], 'max' => 16, 'hint' => 'Optional. Select the maximum image preview size search engines may show.', 'default' => ''],
            'max_video_preview' => ['label' => 'Video preview allowance', 'type' => 'text', 'allowed' => ['-1', '0', '30', '60'], 'max' => 5, 'hint' => 'Optional maximum seconds. -1 allows any duration; 0 allows a static image.', 'default' => ''],
            'twitter_card' => ['label' => 'Social card format', 'type' => 'text', 'allowed' => ['summary', 'summary_large_image'], 'max' => 32, 'hint' => 'Large-image or compact sharing card. Blank keeps the existing large-image card.', 'default' => ''],
            'google_verification' => ['label' => 'Google Search Console verification token', 'type' => 'text', 'max' => 256, 'validation' => ['regex:/^[A-Za-z0-9_-]+$/'], 'hint' => 'Optional: paste only the content token from your own HTML-tag verification method, not the whole tag. This field does not check verification status.', 'default' => ''],
        ],
        'geo' => [
            'name' => ['advice' => 'Use the same public organization name across your site and official profiles.'],
            'description' => ['advice' => 'Say who operates the marketplace and what it does. Only include facts you can substantiate.', 'example' => 'Tourstica is a marketplace connecting travellers with local agencies offering experiences in Morocco.'],
            'legal_name' => ['label' => 'Registered legal name', 'type' => 'text', 'max' => 200, 'hint' => 'Optional. Enter the actual registered operator name. Leave blank when not applicable.', 'default' => ''],
            'alternate_name' => ['label' => 'Alternative brand name', 'type' => 'text', 'max' => 160, 'hint' => 'Optional. A real alternative name people use for the same organization.', 'default' => ''],
            'email' => ['label' => 'Public contact email', 'type' => 'text', 'max' => 254, 'validation' => ['email'], 'hint' => 'Optional. A monitored business address you intend to publish in organization data.', 'default' => ''],
            'phone' => ['label' => 'Public contact phone', 'type' => 'text', 'max' => 32, 'validation' => ['regex:/^\+?[0-9 ()-]{6,32}$/'], 'hint' => 'Optional. A real public business phone, preferably including the country code.', 'default' => ''],
            'about_url' => ['label' => 'About the operator page', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'Optional. Link to an existing public page that accurately identifies the operator.', 'default' => ''],
            'youtube' => ['label' => 'YouTube official profile', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'Optional. Use only an official profile belonging to the operator.', 'default' => ''],
        ],
        'aeo' => [
            'question' => ['label' => 'Question heading', 'type' => 'text', 'max' => 200, 'hint' => 'Optional heading above the public summary. It appears only when the answer summary is present.', 'default' => '', 'example' => 'How does booking with Tourstica work?'],
            'summary' => ['advice' => 'Give a short factual answer. Explain manual confirmation. Do not add unverified safety or travel advice.', 'example' => 'Browse an experience, choose the available date and participants, then send your booking request. An administrator reviews and confirms the booking manually.'],
            'reviewer' => ['label' => 'Reviewed by', 'type' => 'text', 'max' => 160, 'hint' => 'Optional. The actual person or team who reviewed this homepage, including this answer. Published alongside the summary.', 'default' => ''],
            'reviewer_type' => ['label' => 'Reviewer type', 'type' => 'text', 'allowed' => ['Person', 'Organization'], 'max' => 16, 'hint' => 'Select the actual type of the named reviewer.', 'default' => 'Person'],
            'reviewed_on' => ['label' => 'Last reviewed date', 'type' => 'text', 'max' => 10, 'validation' => ['date_format:Y-m-d', 'before_or_equal:today'], 'hint' => 'Optional. YYYY-MM-DD, only after a real review of this homepage and answer. No future dates.', 'default' => ''],
            'source_1_label' => ['label' => 'Source 1 label', 'type' => 'text', 'max' => 160, 'hint' => 'Optional. A readable name for a source that supports the answer.', 'default' => '', 'example' => 'Our booking process'],
            'source_1_url' => ['label' => 'Source 1 URL', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'Optional. A public page that actually supports the answer.', 'default' => ''],
            'source_2_label' => ['label' => 'Source 2 label', 'type' => 'text', 'max' => 160, 'hint' => 'Optional. A readable name for a second relevant source.', 'default' => ''],
            'source_2_url' => ['label' => 'Source 2 URL', 'type' => 'link', 'absolute' => true, 'max' => 2048, 'hint' => 'Optional. Use a trustworthy source relevant to the answer, not a random link.', 'default' => ''],
        ],
    ],
];
