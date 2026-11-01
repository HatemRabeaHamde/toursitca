<?php

/* Traveller reviews page — UI strings. Review content lives in the database. */

return [
    'meta_title' => 'Traveller reviews — written after the trip, never before',
    'breadcrumb' => [
        'experiences' => 'Experiences',
        'reviews' => 'Traveller reviews',
    ],
    'hero' => [
        'eyebrow' => 'Traveller reviews',
        'title' => 'Written after the trip, never',
        'accent' => 'before.',
        'lede' => 'Every review comes from a traveller who completed the trip — linked to a real booking.',
    ],
    'summary' => [
        'average' => 'Average across all experiences',
        'count' => 'From :count review|From :count reviews',
        'breakdown' => 'Rating breakdown',
        'stars' => ':stars ★',
        'details' => 'What travellers rate',
        'detail' => [
            'hosts' => 'Hosts',
            'organisation' => 'Organisation',
            'pickup' => 'Pick-up & timing',
            'value' => 'Value for money',
        ],
    ],
    'filters' => [
        'label' => 'Filter reviews',
        'interests' => 'Interests',
        'all' => 'All',
        'experience' => 'Experience',
        'all_experiences' => 'All experiences',
        'host' => 'Host',
        'all_hosts' => 'All hosts',
        'rating' => 'Rating',
        'any_rating' => 'Any rating',
        'ratings' => [
            5 => '5 stars',
            4 => '4 stars & up',
            3 => '3 stars & up',
        ],
        'photos' => 'With photos',
        'apply' => 'Apply',
    ],
    'verified_only' => 'Verified bookings only',
    'sort' => [
        'label' => 'Sort:',
        'recent' => 'Most recent',
        'highest' => 'Highest rated',
        'lowest' => 'Lowest rated',
    ],
    'count' => ':count review|:count reviews',
    'card' => [
        'verified' => 'Verified booking',
        'travelled' => 'Travelled :date',
        'reply_from' => 'Reply from :name',
        'reply' => 'Reply from the host',
        'stars' => 'Rated :rating out of 5',
        'photo' => 'Photo from :name',
    ],
    'more' => [
        'button' => 'Show more reviews',
        'showing' => 'Showing :shown of :total reviews',
    ],
    'empty' => [
        'title' => 'No reviews match these filters yet.',
        'body' => 'Reviews appear here once travellers have completed their trip.',
        'reset' => 'Clear filters',
    ],
    'anonymous' => 'Traveller',
];
