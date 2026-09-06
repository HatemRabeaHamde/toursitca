# Checkout Start Contract

This endpoint creates a timed checkout session and temporarily holds seats.

Payment is currently internal/manual CRUD only. No external payment gateway is used yet.

## Route

```txt
POST /{locale}/checkout/start
```

Route name:

```txt
site.checkout.start
```

## Request

```json
{
  "experience_id": 1,
  "option_id": 10,
  "availability_id": 20,
  "booking_type": "private",
  "participants": {
    "adult": 3
  },
  "language": "en",
  "payment_method": "manual"
}
```

## Payment Methods

Current:

```txt
manual
pay_later
```

Behavior:

```txt
manual -> payment_status = pending
pay_later -> payment_status = not_required
```

Future gateway integration can add:

```txt
stripe
paypal
cmi
```

## Response

```json
{
  "data": {
    "uuid": "4e12d6c5-8c92-4a22-8d15-d1b2d953f3d8",
    "status": "active",
    "reserved_until": "2026-06-11T10:30:00.000000Z",
    "activity_url": "/en/checkout/4e12d6c5-8c92-4a22-8d15-d1b2d953f3d8/activity"
  }
}
```

## Seat Hold Rules

Group:

```txt
held_seats += participants_count
```

Private:

```txt
held_seats = availability.max_seats
```

Private still stores real traveler count:

```txt
participants_count = real travelers
charged_seats = availability.max_seats
```

## Checkout Session Snapshot

The session stores:

```txt
participants
participants_count
charged_seats
booking_type
tour_language
price_snapshot
payment_method
payment_status
reserved_until
```

## Notes

- Checkout session is not a booking yet.
- Final booking will be created after checkout activity/contact/payment steps.
- Expired checkout cleanup will be implemented in a later step.

