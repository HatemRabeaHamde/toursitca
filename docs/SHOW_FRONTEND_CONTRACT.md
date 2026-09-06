# Experience Show Frontend Contract

This document describes the current contract for the experience show page.

## Route

```txt
GET /{locale}/experiences/{experience:slug}
```

Example:

```txt
GET /en/experiences/marrakech-balloon-ride
```

Route name:

```txt
site.experiences.show
```

## Blade Variables

The page receives:

```txt
$detail
```

`$detail` is an `ExperienceDetailData` object.

## Data Shape

Target JSON equivalent:

```json
{
  "experience": {
    "id": 1,
    "slug": "marrakech-balloon-ride",
    "title": "Marrakech: Balloon Ride with Berber Breakfast & Certificate",
    "description": "Enjoy a morning flight over Marrakech.",
    "provider_name": "Ballooning Marrakech",
    "city": "Marrakesh",
    "category": "balloon",
    "rating_avg": "4.8",
    "reviews_count": 10073
  },
  "gallery": [
    {
      "type": "image",
      "url": "https://example.test/image.jpg"
    }
  ],
  "badges": [
    "Top rated"
  ],
  "features": [
    {
      "label": "Duration hours",
      "value": "4 hours"
    },
    {
      "label": "Private option available",
      "value": "Available"
    }
  ],
  "booking_summary": {
    "price_from": "4509.00",
    "original_price": "8841.00",
    "currency": "MAD",
    "supports_private": true,
    "pickup_enabled": true,
    "next_availabilities": [
      {
        "id": 1,
        "date": "2026-06-11",
        "time": "06:00",
        "available_seats": 8
      }
    ]
  },
  "recommendations": [],
  "booking_url": "/en/experiences/1/book"
}
```

## Notes

- `booking_summary.price_from` is display-only.
- Final pricing must come from the quote endpoint.
- `booking_url` still points to the current booking page until checkout sessions are introduced.
- Recommendations currently use same city or category.

