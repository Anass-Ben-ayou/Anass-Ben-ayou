<?php

return [
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
    'jwt_secret' => env('JWT_SECRET', env('APP_KEY')),
    'jwt_ttl_minutes' => (int) env('JWT_TTL_MINUTES', 120),
    'auth_cookie_name' => env('AUTH_COOKIE_NAME', 'access_token'),
    'csrf_cookie_name' => env('CSRF_COOKIE_NAME', 'XSRF-TOKEN'),
    'cookie_domain' => env('SESSION_DOMAIN'),
    'cookie_secure' => filter_var(env('COOKIE_SECURE', false), FILTER_VALIDATE_BOOL),
    'cookie_same_site' => env('COOKIE_SAME_SITE', 'lax'),
];
