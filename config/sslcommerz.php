<?php

declare(strict_types=1);

return [
    /*
     * Enable sandbox/test mode.
     * Set to false when going live.
     */
    'sandbox' => env('SSLC_SANDBOX', true),

    /*
     * API credentials from your SSLCommerz merchant panel.
     */
    'store' => [
        'id'       => env('SSLC_STORE_ID'),
        'password' => env('SSLC_STORE_PASSWORD'),
        'currency' => env('SSLC_STORE_CURRENCY', 'BDT'),
    ],

    /*
     * These route names must match the routes defined in routes/web.php.
     * SSLCommerz will POST to these URLs after payment.
     */
    'route' => [
        'success' => env('SSLC_ROUTE_SUCCESS', 'sslc.success'),
        'failure' => env('SSLC_ROUTE_FAILURE', 'sslc.failure'),
        'cancel'  => env('SSLC_ROUTE_CANCEL',  'sslc.cancel'),
        'ipn'     => env('SSLC_ROUTE_IPN',     'sslc.ipn'),
    ],

    'product_profile' => 'general',
];
