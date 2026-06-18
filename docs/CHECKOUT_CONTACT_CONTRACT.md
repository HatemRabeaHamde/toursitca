# Checkout Contact Contract

This step stores lead traveler/contact details for an active checkout session.

## Routes

```txt
GET  /{locale}/checkout/{checkoutSession:uuid}/contact
POST /{locale}/checkout/{checkoutSession:uuid}/contact
GET  /{locale}/checkout/{checkoutSession:uuid}/payment
```

Route names:

```txt
site.checkout.contact
site.checkout.contact.store
site.checkout.payment
```

## Request

```json
{
  "contact_first_name": "Hatem",
  "contact_last_name": "Ali",
  "contact_email": "hatem@example.com",
  "contact_phone": "+201000000000",
  "contact_country": "Egypt",
  "special_requests": "Please contact me by WhatsApp."
}
```

## Rules

- Checkout session must be active.
- Guest checkout is authorized by session id.
- Logged-in checkout is authorized by user id.
- `contact_first_name`, `contact_last_name`, and `contact_email` are required.
- `contact_email` must be valid.

## Stored Fields

```txt
contact_first_name
contact_last_name
contact_email
contact_phone
contact_country
special_requests
```

## Next Step

Successful save redirects to:

```txt
GET /{locale}/checkout/{checkoutSession:uuid}/payment
```

Payment is currently manual/internal CRUD:

```txt
payment_method = manual/pay_later
payment_status = pending/not_required/paid/failed/cancelled
```

