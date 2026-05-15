<?php

return [
    'provider' => env('PAYMENT_GATEWAY', env('PAYMENT_PROVIDER', 'stripe')),
    'merchant_id' => env('PAYMENT_MERCHANT_ID'),
    'api_key' => env('PAYMENT_API_KEY'),
    'secret' => env('PAYMENT_SECRET', env('STRIPE_SECRET_KEY')),
    'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
    'callback_url' => env('PAYMENT_CALLBACK_URL', rtrim(env('APP_URL', 'http://localhost:8000'), '/').'/api/checkout/payment-callback'),
    'currency' => strtoupper(env('STRIPE_CURRENCY', env('PAYMENT_CURRENCY', 'MAD'))),
    'frontend_success_url' => env('CHECKOUT_SUCCESS_URL', rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/').'/checkout/success'),
    'frontend_cancel_url' => env('CHECKOUT_CANCEL_URL', rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/').'/checkout/failed'),
    'frontend_pending_url' => env('CHECKOUT_PENDING_URL', rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/').'/checkout/pending'),
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET_KEY', env('PAYMENT_SECRET')),
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'api_base' => env('STRIPE_API_BASE', 'https://api.stripe.com/v1'),
        'api_version' => env('STRIPE_API_VERSION', '2025-02-24.acacia'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', env('PAYMENT_WEBHOOK_SECRET')),
        'test_mode_only' => filter_var(env('STRIPE_TEST_MODE_ONLY', true), FILTER_VALIDATE_BOOL),
        'brands_blocked' => ['american_express', 'discover_global_network'],
    ],
    'payzone' => [
        'base_url' => env('PAYZONE_BASE_URL', 'https://paiement.payzone.ma'),
        'api_version' => env('PAYZONE_API_VERSION', '002.70'),
        'redirect_base_url' => env('PAYZONE_REDIRECT_BASE_URL', 'https://paiement.payzone.ma/payment'),
    ],
    'demo' => [
        'enabled' => filter_var(env('DEMO_CARD_ENABLED', false), FILTER_VALIDATE_BOOL),
    ],
];
