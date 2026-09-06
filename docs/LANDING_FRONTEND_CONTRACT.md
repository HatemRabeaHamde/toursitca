# Landing Frontend Contract

This document describes the routes, query parameters, and data contract for the marketplace landing/search listing page.

Current implementation is Laravel Blade. The JSON shapes below describe the prepared data that the frontend/Blade should consume. If an API or AJAX endpoint is added later, it should keep the same contract.

## Route

```txt
GET /{locale}/experiences
```

Examples:

```txt
GET /en/experiences
GET /en/experiences?search=Marrakesh
GET /en/experiences?city=Marrakesh&date=2026-06-11&private_available=1
GET /fr/experiences?category=desert&sort=rating
```

Current Laravel route name:

```txt
site.experiences.index
```

## Query Parameters

| Param | Type | Required | Description |
| --- | --- | --- | --- |
| `search` | string | No | Keyword search across city, category, title, and description. Max 120 chars. |
| `city` | string | No | Exact city/destination filter, case-insensitive. Max 80 chars. |
| `category` | string | No | Experience category key, such as `desert`. Max 80 chars. |
| `date` | date | No | Availability date in `YYYY-MM-DD` format. |
| `min_price` | decimal | No | Minimum `price_per_person`. |
| `max_price` | decimal | No | Maximum `price_per_person`. Must be greater than or equal to `min_price`. |
| `private_available` | boolean | No | `1` filters experiences that support private booking. |
| `pickup_available` | boolean | No | `1` filters experiences with pickup enabled. |
| `sort` | string | No | Sorting option. Defaults to `recommended`. |
| `page` | integer | No | Pagination page. |

## Sort Options

```json
[
  {
    "value": "recommended",
    "label": "Recommended"
  },
  {
    "value": "price_low",
    "label": "Price: low to high"
  },
  {
    "value": "price_high",
    "label": "Price: high to low"
  },
  {
    "value": "rating",
    "label": "Rating"
  },
  {
    "value": "newest",
    "label": "Newest"
  }
]
```

Recommended sort currently means:

```txt
is_top_rated desc
rating_avg desc
reviews_count desc
created_at desc
```

## Full Page Data Shape

This is the target shape for the landing page if exposed as JSON.

```json
{
  "meta": {
    "locale": "en",
    "currency": "MAD",
    "total_results": 126,
    "current_page": 1,
    "per_page": 12
  },
  "filters": {
    "search": "Marrakesh",
    "city": "Marrakesh",
    "category": "desert",
    "date": "2026-06-11",
    "min_price": "50.00",
    "max_price": "500.00",
    "private_available": true,
    "pickup_available": true,
    "sort": "recommended"
  },
  "sort_options": [
    {
      "value": "recommended",
      "label": "Recommended"
    },
    {
      "value": "price_low",
      "label": "Price: low to high"
    },
    {
      "value": "price_high",
      "label": "Price: high to low"
    },
    {
      "value": "rating",
      "label": "Rating"
    },
    {
      "value": "newest",
      "label": "Newest"
    }
  ],
  "cards": [
    {
      "id": 1,
      "slug": "marrakech-balloon-ride",
      "title": "Marrakech: Balloon Ride with Berber Breakfast & Certificate",
      "city": "Marrakesh",
      "category": "balloon",
      "thumbnail_url": "https://example.test/storage/experiences/balloon.jpg",
      "duration_label": "4 hours",
      "rating_avg": "4.8",
      "reviews_count": 10073,
      "badges": [
        "Top rated"
      ],
      "features": [
        "Private option available",
        "Pickup available"
      ],
      "price_from": "4509.00",
      "original_price": "8841.00",
      "currency": "MAD",
      "is_private_available": true,
      "is_pickup_available": true,
      "show_url": "/en/experiences/marrakech-balloon-ride"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 11,
    "per_page": 12,
    "total": 126,
    "from": 1,
    "to": 12,
    "next_page_url": "/en/experiences?page=2",
    "prev_page_url": null
  }
}
```

## Current Blade Variables

The current Blade implementation receives:

```txt
$experiences
$cards
$filters
```

### `$experiences`

Laravel paginator for the underlying `Experience` models.

Used for:

```txt
total results
pagination links
current page
```

### `$cards`

Collection of `ExperienceCardData`.

Each item has:

```txt
id
slug
title
city
category
thumbnailUrl
durationLabel
ratingAvg
reviewsCount
badges
features
priceFrom
originalPrice
currency
isPrivateAvailable
isPickupAvailable
showUrl
```

Blade equivalent of card JSON:

```php
[
    'id' => $card->id,
    'slug' => $card->slug,
    'title' => $card->title,
    'city' => $card->city,
    'category' => $card->category,
    'thumbnail_url' => $card->thumbnailUrl,
    'duration_label' => $card->durationLabel,
    'rating_avg' => $card->ratingAvg,
    'reviews_count' => $card->reviewsCount,
    'badges' => $card->badges,
    'features' => $card->features,
    'price_from' => $card->priceFrom,
    'original_price' => $card->originalPrice,
    'currency' => $card->currency,
    'is_private_available' => $card->isPrivateAvailable,
    'is_pickup_available' => $card->isPickupAvailable,
    'show_url' => $card->showUrl,
]
```

### `$filters`

Instance of `ExperienceSearchData`.

Fields:

```txt
search
city
category
date
minPrice
maxPrice
privateAvailable
pickupAvailable
sort
```

## Card Rules

### Badge Rules

Current:

```txt
is_top_rated = true -> "Top rated"
```

Future:

```txt
is_certified = true -> "Certified"
ranking_score high -> "Top pick"
```

### Feature Rules

Current:

```txt
private_price is not null -> "Private option available"
pickup_enabled = true -> "Pickup available"
```

Future:

```txt
free_cancellation_hours is not null -> "Free cancellation"
pay_later_enabled = true -> "Reserve now & pay later"
language_options not empty -> selected language labels
```

### Price Rules

Current:

```txt
price_from = price_per_person
original_price = optional display-only old price
currency = config('payment.currency')
```

Important:

- Frontend must never calculate final booking price.
- `price_from` is display-only for listing cards.
- Private booking pricing is calculated later in quote/checkout.
- Currency conversion will be introduced in a later phase.

## Private Booking Note

Private booking support on the landing page is only a display/filter signal.

Final private booking behavior in checkout:

```txt
booking_type = private
participants_count = real traveler count entered by the user
charged_seats = max_seats
booked_seats = max_seats
total_price = private_price OR price_per_person * max_seats
```

Example:

```txt
Availability max seats: 10
User selects private booking for 3 real travelers

participants_count = 3
charged_seats = 10
booked_seats = 10
```

## Validation Errors

Invalid query params should redirect back or return validation errors through Laravel's normal validation behavior.

Examples:

```json
{
  "message": "The selected sort is invalid.",
  "errors": {
    "sort": [
      "The selected sort is invalid."
    ]
  }
}
```

```json
{
  "message": "The max price field must be greater than or equal to min price.",
  "errors": {
    "max_price": [
      "The max price field must be greater than or equal to min price."
    ]
  }
}
```

## Future JSON Endpoint

If a frontend API is needed later, add one of these without changing the contract:

```txt
GET /{locale}/experiences.json
```

or:

```txt
GET /{locale}/api/experiences
```

Recommended response should match the "Full Page Data Shape" section.

## Next Phase Dependency

Current `show_url` should eventually point to:

```txt
GET /{locale}/experiences/{experience:slug}
```

Until the show page exists, it may point to the current booking route:

```txt
GET /{locale}/experiences/{experience}/book
```

