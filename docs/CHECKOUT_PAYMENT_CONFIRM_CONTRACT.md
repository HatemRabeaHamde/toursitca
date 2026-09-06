# Checkout Payment Confirm Contract

This step confirms a checkout session and creates the final booking.

Payment is currently manual/internal CRUD only. No external payment gateway is called.

## Routes

```txt
GET  /{locale}/checkout/{checkoutSession:uuid}/payment
POST /{locale}/checkout/{checkoutSession:uuid}/payment
```

Route names:

```txt
site.checkout.payment
site.checkout.payment.confirm
```

## Behavior

On confirmation:

```txt
checkout_session.status = completed
availability.held_seats -= checkout_session.charged_seats
availability.booked_seats += checkout_session.charged_seats
booking is created from checkout_session snapshot
```

## Booking Snapshot

Booking receives:

```txt
checkout_session_id
experience_option_id
participants
participants_count
charged_seats
tour_language
pickup_status
pickup_address
pickup_lat
pickup_lng
price_snapshot
payment_method
payment_status
guest/contact details
```

## Payment Status

Current status behavior:

```txt
manual -> pending
pay_later -> not_required
```

Admin CRUD/payment operations can later update:

```txt
pending
paid
failed
cancelled
```

## Redirect

Success redirects to:

```txt
GET /{locale}/bookings/{booking}/confirmation
```

