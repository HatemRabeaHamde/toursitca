# Checkout Activity / Pickup Contract

This step stores pickup details for an active checkout session.

## Routes

```txt
GET  /{locale}/checkout/{checkoutSession:uuid}/activity
POST /{locale}/checkout/{checkoutSession:uuid}/activity
```

Route names:

```txt
site.checkout.activity
site.checkout.activity.store
```

## Request

Add pickup now:

```json
{
  "pickup_status": "add_now",
  "pickup_address": "Riad Example, Marrakesh",
  "pickup_lat": 31.6294723,
  "pickup_lng": -7.9810845
}
```

Unknown pickup:

```json
{
  "pickup_status": "unknown"
}
```

Not required:

```json
{
  "pickup_status": "not_required"
}
```

## Rules

- Checkout session must be active.
- Guest checkout is authorized by session id.
- Logged-in checkout is authorized by user id.
- `pickup_address` is required when `pickup_status = add_now`.
- Coordinates are optional for now.
- No Maps API validation is implemented yet.

## Stored Fields

```txt
pickup_status
pickup_address
pickup_lat
pickup_lng
```

## Next Step

Successful save redirects to:

```txt
GET /{locale}/checkout/{checkoutSession:uuid}/contact
```

