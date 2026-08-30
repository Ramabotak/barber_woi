<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    // Checkout Barber Woi uses Core API by default. Set to "snap" only for a
    // deliberate rollback to Midtrans' hosted checkout.
    'payment_driver' => env('MIDTRANS_PAYMENT_DRIVER', 'core'),
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL', ''),
];
