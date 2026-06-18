# Availability Quote Frontend Contract

This endpoint returns server-side pricing and availability validation for the selected experience.

## Route

```txt
POST /{locale}/experiences/{experience:slug}/quote
```

Route name:

```txt
site.experiences.quote
```

## Request

```json
{
  "booking_type": "group",
  "participants": {
    "adult": 2,
    "child": 1
  },
  "option_id": 10,
  "availability_id": 20,
  "language": "en"
}
```

Backward-compatible request:

```json
{
  "booking_type": "group",
  "participants_count": 3,
  "availability_id": 20
}
```

## Request Fields

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `booking_type` | string | Yes | `group` or `private`. |
| `participants` | object | No | Participant counts by type, such as `adult` and `child`. |
| `participants_count` | integer | No | Fallback if `participants` is not sent. Treated as adults. |
| `option_id` | integer | No | Experience option id. |
| `availability_id` | integer | No | Selected availability slot id. |
| `language` | string | No | Selected tour language code. |

## Response

```json
{
  "data": {
    "experience_id": 1,
    "option_id": 10,
    "availability_id": 20,
    "booking_type": "group",
    "participants": {
      "adult": 2,
      "child": 1
    },
    "participants_count": 3,
    "charged_seats": 3,
    "language": "en",
    "currency": "MAD",
    "unit_price": "100.00",
    "subtotal": "260.00",
    "original_subtotal": "320.00",
    "discount_amount": "60.00",
    "total": "260.00",
    "available_seats": 8
  }
}
```

## Private Booking Rule

For private booking:

```txt
participants_count = real traveler count
charged_seats = availability.max_seats OR experience.max_group_size
```

Pricing:

```txt
if private_price exists:
    total = private_price
else:
    total = price_per_person * charged_seats
```

Example:

```json
{
  "booking_type": "private",
  "participants": {
    "adult": 3
  },
  "availability_id": 20
}
```

Response:

```json
{
  "data": {
    "participants_count": 3,
    "charged_seats": 10,
    "total": "1000.00"
  }
}
```

## Notes

- The frontend must not calculate final totals.
- Quote totals are still not a booking. Final booking will snapshot pricing in checkout.
- If `option_id` is omitted, the server uses the first active option if available; otherwise it falls back to the experience base price.
- If `availability_id` is provided, the server validates seat availability.

