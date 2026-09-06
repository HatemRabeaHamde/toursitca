<?php

return [
    // How long an unpaid booking holds seats before auto-cancellation.
    'unpaid_ttl_hours' => env('BOOKING_UNPAID_TTL_HOURS', 24),

    // Row-level lock timeout (seconds) when reserving seats.
    'seat_lock_timeout' => 5,

    // Max participants per single booking.
    'max_participants' => 20,

    // An experience whose max group size is at or below this is shown as a
    // "small group" on listing cards.
    'small_group_max' => 12,

    // An experience longer than this many hours is shown as "overnight".
    'overnight_hours' => 8,

    // Total available seats across upcoming dates at or below this is shown
    // as "few spots left" on listing cards.
    'low_availability_threshold' => 5,

    // Default cancellation policy (used if experience has none).
    'default_cancellation' => [
        'hours_before' => 48,
        'refund_percentage' => 50.00,
    ],
];
